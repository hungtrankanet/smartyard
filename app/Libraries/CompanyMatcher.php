<?php

namespace App\Libraries;

/**
 * CompanyMatcher: 3-Tier Multi-Algorithm Company Auto-Grouping & Member Deduplication Engine
 *
 * Tier 1: Tax Code exact match (100% confidence)
 * Tier 2: Corporate Domain match with public hosting blacklist (95% confidence)
 * Tier 3: Multilingual Fuzzy Company Name match (>= 80% composite similarity)
 */
class CompanyMatcher
{
    protected array $publicDomainBlacklist = [
        'facebook.com', 'fb.com', 'fb.me', 'zalo.me', 'linkedin.com', 'twitter.com', 'x.com',
        'instagram.com', 'tiktok.com', 'wechat.com', 'line.me', 'youtube.com', 'gmail.com',
        'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'google.com', 'sites.google.com',
        'wixsite.com', 'wordpress.com', 'blogspot.com', 'github.io', 'dropbox.com', 'drive.google.com',
        't.me', 'telegram.org', 'viber.com', 'skype.com',
    ];

    protected array $diacriticsMap = [
        'a' => 'à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ', 'e' => 'è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ',
        'i' => 'ì|í|ị|ỉ|ĩ', 'o' => 'ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ',
        'u' => 'ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ', 'y' => 'ỳ|ý|ỵ|ỷ|ỹ', 'd' => 'đ',
        'A' => 'À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ', 'E' => 'È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ',
        'I' => 'Ì|Í|Ị|Ỉ|Ĩ', 'O' => 'Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ',
        'U' => 'Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ', 'Y' => 'Ỳ|Ý|Ỵ|Ỷ|Ỹ', 'D' => 'Đ',
    ];

    protected array $legalSuffixes = [
        'cong ty tnhh mtv', 'cong ty tnhh', 'cong ty co phan', 'cong ty cp', 'cty tnhh mtv', 'cty tnhh',
        'cty cp', 'cty', 'doanh nghiep tu nhan', 'dntn', 'tap doan', 'chi nhanh', 'van phong dai dien',
        'vpdd', 'xuat nhap khau', 'xnk', 'thuong mai dich vu', 'thuong mai', 'dich vu', 'giao nhan van tai',
        'giao nhan', 'van tai quoc te', 'van tai', 'kho van', 'joint stock company', 'company limited',
        'co limited', 'co ltd', 'co., ltd', 'co.,ltd', 'co. ltd', 'corporation', 'corp', 'incorporated',
        'inc', 'limited', 'ltd', 'jsc', 'llc', 'plc', 'group', 'holdings', 'holding', 'logistics',
        'forwarding', 'shipping', 'transportation', 'transport', 'international', 'trading', 'solutions',
        'services', 'express',
    ];

    public function groupCards(array $rawCardsList, array $existingDbMembers = []): array
    {
        $groups = [];

        foreach ($rawCardsList as $index => $cardItem) {
            $ocrData = $this->extractOcrData($cardItem);
            $bestGroupIndex = null;
            $bestMatch = ['matched' => false, 'score' => 0.0, 'type' => 'none', 'field' => ''];

            // 1. In-batch grouping check
            foreach ($groups as $gIdx => $group) {
                $match = $this->calculateSimilarity($ocrData, $group['company_info']);
                if ($match['matched'] && $match['score'] > $bestMatch['score']) {
                    $bestMatch = $match;
                    $bestGroupIndex = $gIdx;
                }
            }

            if ($bestGroupIndex !== null && $bestMatch['matched']) {
                $this->mergeCardIntoGroup($groups[$bestGroupIndex], $cardItem, $ocrData, $bestMatch['score'], $bestMatch['type'], $bestMatch['field'], $index);
            } else {
                // 2. Cross-match with DB members
                $dbMatchResult = $this->findBestDbMemberMatch($ocrData, $existingDbMembers);
                $matchedDbMember = $dbMatchResult['member'];
                $dbMatch = $dbMatchResult['match'];

                $groups[] = $this->createGroupFromCard(
                    $cardItem, $ocrData, $matchedDbMember,
                    $dbMatch['matched'] ? 'existing_db' : 'new',
                    $dbMatch['matched'] ? $dbMatch['score'] : 1.0,
                    $dbMatch['matched'] ? $dbMatch['field'] : '',
                    $index
                );
            }
        }

        return array_values($groups);
    }

    public function calculateSimilarity(array $card1, array $card2): array
    {
        // Tier 1: Tax Code Exact Match (100%)
        $tax1 = $this->cleanTaxCode($card1['tax_code'] ?? '');
        $tax2 = $this->cleanTaxCode($card2['tax_code'] ?? '');
        if (strlen($tax1) >= 8 && strlen($tax2) >= 8 && $tax1 === $tax2) {
            return ['matched' => true, 'score' => 1.0, 'type' => 'tax_code', 'field' => 'tax_code'];
        }

        // Tier 2: Root Domain Match (95%)
        $dom1 = $this->extractRootDomain($card1['website'] ?? '');
        $dom2 = $this->extractRootDomain($card2['website'] ?? '');
        if (!empty($dom1) && !empty($dom2) && !$this->isPublicDomain($dom1) && !$this->isPublicDomain($dom2) && $dom1 === $dom2) {
            return ['matched' => true, 'score' => 0.95, 'type' => 'domain', 'field' => 'website'];
        }

        // Tier 3: Multilingual Fuzzy Company Name Match (>= 80%)
        $names1 = array_filter([$card1['company_name'] ?? '', $card1['company_name_vi'] ?? '', $card1['company_name_en'] ?? '', $card1['company_name_local'] ?? '']);
        $names2 = array_filter([$card2['company_name'] ?? '', $card2['company_name_vi'] ?? '', $card2['company_name_en'] ?? '', $card2['company_name_local'] ?? '']);

        $maxScore = 0.0;
        foreach ($names1 as $n1) {
            foreach ($names2 as $n2) {
                $score = $this->calculateNameSimilarity($n1, $n2);
                if ($score > $maxScore) $maxScore = $score;
            }
        }

        if ($maxScore >= 0.80) {
            return ['matched' => true, 'score' => round($maxScore, 4), 'type' => 'fuzzy_name', 'field' => 'company_name'];
        }

        return ['matched' => false, 'score' => round($maxScore, 4), 'type' => 'none', 'field' => ''];
    }

    public function calculateNameSimilarity(string $name1, string $name2): float
    {
        $c1 = $this->cleanCompanyName($name1);
        $c2 = $this->cleanCompanyName($name2);
        if (empty($c1) || empty($c2)) return 0.0;
        if ($c1 === $c2) return 1.0;

        similar_text($c1, $c2, $percent);
        $s1 = $percent / 100.0;

        $maxLen = max(mb_strlen($c1, 'UTF-8'), mb_strlen($c2, 'UTF-8'));
        $lev = levenshtein(substr($c1, 0, 255), substr($c2, 0, 255));
        $s2 = $maxLen > 0 ? max(0.0, 1.0 - ($lev / $maxLen)) : 0.0;

        $s3 = $this->jaccardSimilarity($c1, $c2);

        // Token Overlap (containment score)
        $tokens1 = array_unique(array_filter(explode(' ', trim($c1))));
        $tokens2 = array_unique(array_filter(explode(' ', trim($c2))));
        $intersectCount = count(array_intersect($tokens1, $tokens2));
        $minTokens = min(count($tokens1), count($tokens2));
        $maxTokens = max(count($tokens1), count($tokens2));
        $s4 = ($minTokens > 0 && $intersectCount > 0) ? ($intersectCount / $minTokens) * pow($intersectCount / $maxTokens, 0.25) : 0.0;

        return max($s1, $s2, $s3, $s4);
    }

    public function jaccardSimilarity(string $s1, string $s2): float
    {
        $tokens1 = array_unique(array_filter(explode(' ', trim($s1))));
        $tokens2 = array_unique(array_filter(explode(' ', trim($s2))));
        if (empty($tokens1) || empty($tokens2)) return 0.0;

        $intersection = count(array_intersect($tokens1, $tokens2));
        $union = count(array_unique(array_merge($tokens1, $tokens2)));
        return $union > 0 ? ($intersection / $union) : 0.0;
    }

    public function cleanTaxCode(?string $taxCode): string
    {
        if (empty($taxCode)) return '';
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', (string)$taxCode);
        $cleaned = preg_replace('/^(?:mst|tax|msdn)/i', '', $cleaned);
        return strtoupper(trim($cleaned));
    }

    public function extractRootDomain(?string $url): string
    {
        if (empty($url)) return '';
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) $url = 'http://' . $url;
        $host = parse_url($url, PHP_URL_HOST) ?: $url;
        $host = strtolower(trim($host));
        $host = preg_replace('/^www\./i', '', $host);
        return preg_replace('/:\d+$/', '', $host);
    }

    public function isPublicDomain(string $domain): bool
    {
        $cleanDomain = strtolower(trim($domain));
        foreach ($this->publicDomainBlacklist as $pub) {
            $suffix = '.' . $pub;
            if ($cleanDomain === $pub || (strlen($cleanDomain) > strlen($suffix) && substr($cleanDomain, -strlen($suffix)) === $suffix)) {
                return true;
            }
        }
        return false;
    }

    public function cleanCompanyName(?string $name): string
    {
        if (empty($name)) return '';
        $str = $this->stripDiacritics($name);
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', trim($str));

        foreach ($this->legalSuffixes as $suffix) {
            $str = preg_replace('/^' . preg_quote($suffix, '/') . '\s+/i', '', $str);
            $str = preg_replace('/\s+' . preg_quote($suffix, '/') . '$/i', '', $str);
        }
        return trim($str);
    }

    public function stripDiacritics(string $str): string
    {
        foreach ($this->diacriticsMap as $replacement => $pattern) {
            $str = preg_replace('/(' . $pattern . ')/u', $replacement, $str);
        }
        return $str;
    }

    public function parseBranches(string $address, string $city, string $country, string $branchInfo, string $phone = '', string $email = ''): array
    {
        $branches = [];

        // Headquarters branch
        if (!empty($address) || !empty($city)) {
            $branches[] = [
                'branch_name'     => 'Trụ Sở Chính ' . (!empty($city) ? $city : (!empty($country) ? $country : '')),
                'country'         => !empty($country) ? $country : 'Việt Nam',
                'city'            => $city, 'address' => $address, 'phone' => $phone, 'email' => $email,
                'is_headquarters' => 1, 'metadata' => ['is_hq' => true],
            ];
        }

        // Additional branches
        if (!empty($branchInfo)) {
            $segments = preg_split('/;\s*|\n+|\r+|\s*\|\s*/u', $branchInfo);
            foreach ($segments as $seg) {
                $seg = trim($seg);
                if (empty($seg)) continue;

                $branchTitle = $seg;
                $branchAddress = !empty($address) ? $address : $seg;
                if (preg_match('/^([^:–—]+)[:–—]\s*(.+)$/u', $seg, $m)) {
                    $branchTitle = trim($m[1]);
                    $branchAddress = trim($m[2]);
                }

                $detectedCity = $this->detectCity($seg) ?: (!empty($city) ? $city : '');
                $detectedCountry = $this->detectCountry($seg) ?: (!empty($country) ? $country : 'Việt Nam');

                $branches[] = [
                    'branch_name'     => $branchTitle, 'country' => $detectedCountry,
                    'city'            => $detectedCity, 'address' => $branchAddress,
                    'phone'           => $phone, 'email' => $email, 'is_headquarters' => 0,
                    'metadata'        => ['extracted_from' => 'branch_info'],
                ];
            }
        }

        return $branches;
    }

    protected function detectCity(string $text): string
    {
        $cities = [
            'Hà Nội', 'Hải Phòng', 'Đà Nẵng', 'TP. Hồ Chí Minh', 'TP.HCM', 'Hồ Chí Minh', 'Cần Thơ',
            'Bình Dương', 'Đồng Nai', 'Quảng Ninh', 'Bắc Ninh', 'Lạng Sơn', 'Quy Nhơn', 'Bình Định',
            'Vũng Tàu', 'Nha Trang', 'Huế', 'Vinh', 'Shanghai', 'Thượng Hải', 'Shenzhen', 'Thâm Quyến',
            'Beijing', 'Bắc Kinh', 'Guangzhou', 'Quảng Châu', 'Tokyo', 'Osaka', 'Seoul', 'Singapore',
        ];
        foreach ($cities as $city) {
            if (mb_stripos($text, $city, 0, 'UTF-8') !== false) return $city;
        }
        return '';
    }

    protected function detectCountry(string $text): string
    {
        $countries = [
            'Trung Quốc' => 'China', 'China' => 'China', 'Nhật Bản' => 'Japan', 'Japan' => 'Japan',
            'Hàn Quốc' => 'Korea', 'Korea' => 'Korea', 'Singapore' => 'Singapore', 'Mỹ' => 'USA',
            'USA' => 'USA', 'Việt Nam' => 'Việt Nam', 'Vietnam' => 'Việt Nam',
        ];
        foreach ($countries as $needle => $canonical) {
            if (mb_stripos($text, $needle, 0, 'UTF-8') !== false) return $canonical;
        }
        return '';
    }

    protected function extractOcrData(array $cardItem): array
    {
        if (isset($cardItem['ocr_parsed']) && is_array($cardItem['ocr_parsed'])) return $cardItem['ocr_parsed'];
        if (isset($cardItem['ocr_data']) && is_array($cardItem['ocr_data'])) return $cardItem['ocr_data'];
        return $cardItem;
    }

    protected function createGroupFromCard(array $cardItem, array $ocrData, $matchedDbMember = null, string $matchType = 'new', float $matchScore = 1.0, string $matchedField = '', int $cardIndex = 0): array
    {
        $groupId = 'grp_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 10);
        $dbId = is_object($matchedDbMember) ? ($matchedDbMember->id ?? null) : (is_array($matchedDbMember) ? ($matchedDbMember['id'] ?? null) : null);

        $companyInfo = [
            'id'                 => $dbId,
            'company_name'       => $ocrData['company_name'] ?? ($ocrData['company_name_vi'] ?? ''),
            'company_name_vi'    => $ocrData['company_name_vi'] ?? ($ocrData['company_name'] ?? ''),
            'company_name_en'    => $ocrData['company_name_en'] ?? '',
            'company_name_local' => $ocrData['company_name_local'] ?? '',
            'detected_language'  => $ocrData['detected_language'] ?? 'vi',
            'tax_code'           => $ocrData['tax_code'] ?? '',
            'address'            => $ocrData['address'] ?? '',
            'city'               => $ocrData['city'] ?? '',
            'country'            => $ocrData['country'] ?? 'Việt Nam',
            'website'            => $ocrData['website'] ?? '',
            'fanpage'            => $ocrData['fanpage'] ?? '',
            'phone'              => $ocrData['phone'] ?? '',
            'email'              => $ocrData['email'] ?? '',
            'industry_type_id'   => null,
            'member_type'        => is_object($matchedDbMember) ? ($matchedDbMember->member_type ?? 'member') : (is_array($matchedDbMember) ? ($matchedDbMember['member_type'] ?? 'member') : 'member'),
            'note'               => $ocrData['note'] ?? '',
            'metadata'           => ['source_card_indices' => [$cardIndex]],
        ];

        $indId = is_object($matchedDbMember) ? ($matchedDbMember->industry_type_id ?? null) : (is_array($matchedDbMember) ? ($matchedDbMember['industry_type_id'] ?? null) : null);
        if (empty($indId)) {
            $searchContext = ($ocrData['company_name'] ?? '') . ' ' . ($ocrData['company_name_vi'] ?? '') . ' ' . ($ocrData['company_name_en'] ?? '') . ' ' . ($ocrData['position'] ?? '') . ' ' . ($ocrData['department'] ?? '') . ' ' . ($ocrData['note'] ?? '') . ' ' . ($ocrData['branch_info'] ?? '');
            if (class_exists('App\Models\IndustryTypeModel')) {
                try { $indId = (new \App\Models\IndustryTypeModel())->detectIndustryId($searchContext); } catch (\Throwable $e) {}
            }
        }
        $companyInfo['industry_type_id'] = $indId;

        $contacts = [];
        $contactName = $ocrData['contact_name'] ?? ($ocrData['representative_name'] ?? '');
        if (!empty($contactName)) {
            $contacts[] = [
                'full_name'       => $contactName, 'full_name_vi' => $ocrData['contact_name_vi'] ?? $contactName,
                'full_name_en'    => $ocrData['contact_name_en'] ?? '', 'full_name_local' => $ocrData['contact_name_local'] ?? '',
                'position'        => $ocrData['position'] ?? '', 'position_vi' => $ocrData['position_vi'] ?? ($ocrData['position'] ?? ''),
                'position_en'     => $ocrData['position_en'] ?? '', 'department' => $ocrData['department'] ?? '',
                'phone'           => $ocrData['phone'] ?? '', 'phone_2' => $ocrData['phone_2'] ?? '',
                'email'           => $ocrData['email'] ?? '', 'email_2' => $ocrData['email_2'] ?? '',
                'is_primary'      => 1, 'social_media' => is_array($ocrData['social_media'] ?? null) ? $ocrData['social_media'] : [],
                'metadata'        => ['source_card_index' => $cardIndex],
            ];
        }

        $branches = $this->parseBranches($companyInfo['address'], $companyInfo['city'], $companyInfo['country'], $ocrData['branch_info'] ?? '', $companyInfo['phone'], $companyInfo['email']);
        $sourceCards = [['file_path' => $cardItem['file_path'] ?? ($cardItem['path'] ?? ''), 'side' => $cardItem['side'] ?? 'single', 'card_index' => $cardIndex, 'ocr_raw' => $ocrData]];

        return [
            'group_id'           => $groupId, 'match_type' => $matchType, 'match_score' => $matchScore,
            'matched_field'      => $matchedField, 'existing_member_id' => $dbId, 'company_info' => $companyInfo,
            'contacts'           => $contacts, 'branches' => $branches, 'source_cards' => $sourceCards,
        ];
    }

    protected function mergeCardIntoGroup(array &$group, array $cardItem, array $ocrData, float $matchScore, string $matchType, string $matchedField, int $cardIndex): void
    {
        $c = &$group['company_info'];
        $contactName = $ocrData['contact_name'] ?? ($ocrData['representative_name'] ?? '');

        // Specific legal company name takes priority over generic group name
        if (!empty($ocrData['company_name'])) {
            $hasSpecificForm = preg_match('/(?:co\.,?\s*ltd|company\s*limited|tnhh|cổ\s*phần|jsc|corp)/i', $ocrData['company_name']);
            $currIsGeneric = preg_match('/(?:group|recruitment\s*group|holding)/i', $c['company_name']);
            if ($hasSpecificForm && $currIsGeneric) {
                if (empty($c['company_name_en'])) $c['company_name_en'] = $c['company_name'];
                $c['company_name'] = $ocrData['company_name'];
                $c['company_name_vi'] = $ocrData['company_name'];
            }
        }

        // If newly merged card has person contact and new address, promote its address to HQ
        if (!empty($contactName) && !empty($ocrData['address']) && $ocrData['address'] !== $c['address']) {
            if (!empty($c['address'])) {
                // Move old address to branches
                $group['branches'][] = [
                    'branch_name'     => 'Văn phòng ' . ($c['city'] ?: 'Chi nhánh'),
                    'country'         => $c['country'] ?: 'Việt Nam',
                    'city'            => $c['city'], 'address' => $c['address'],
                    'phone'           => $c['phone'], 'email' => $c['email'],
                    'is_headquarters' => 0, 'metadata' => ['relocated_from_prev_hq' => true],
                ];
            }
            $c['address'] = $ocrData['address'];
            $c['city'] = $ocrData['city'] ?: $c['city'];
            $c['phone'] = $ocrData['phone'] ?: $c['phone'];
            $c['email'] = $ocrData['email'] ?: $c['email'];
        } else {
            if (empty($c['address']) && !empty($ocrData['address'])) $c['address'] = $ocrData['address'];
            if (empty($c['city']) && !empty($ocrData['city'])) $c['city'] = $ocrData['city'];
            if (empty($c['phone']) && !empty($ocrData['phone'])) $c['phone'] = $ocrData['phone'];
            if (empty($c['email']) && !empty($ocrData['email'])) $c['email'] = $ocrData['email'];
        }

        if ((empty($c['country']) || $c['country'] === 'Việt Nam') && !empty($ocrData['country'])) $c['country'] = $ocrData['country'];
        if (empty($c['tax_code']) && !empty($ocrData['tax_code'])) $c['tax_code'] = $ocrData['tax_code'];
        if (empty($c['website']) && !empty($ocrData['website'])) $c['website'] = $ocrData['website'];
        if (empty($c['fanpage']) && !empty($ocrData['fanpage'])) $c['fanpage'] = $ocrData['fanpage'];
        if (empty($c['company_name_en']) && !empty($ocrData['company_name_en'])) $c['company_name_en'] = $ocrData['company_name_en'];
        if (empty($c['company_name_local']) && !empty($ocrData['company_name_local'])) $c['company_name_local'] = $ocrData['company_name_local'];
        if (empty($c['note']) && !empty($ocrData['note'])) $c['note'] = $ocrData['note'];
        elseif (!empty($ocrData['note']) && strpos($c['note'], $ocrData['note']) === false) $c['note'] .= ' | ' . $ocrData['note'];

        if (empty($c['industry_type_id']) && class_exists('App\Models\IndustryTypeModel')) {
            try {
                $searchContext = ($c['company_name'] ?? '') . ' ' . ($ocrData['company_name'] ?? '') . ' ' . ($ocrData['position'] ?? '') . ' ' . ($ocrData['note'] ?? '');
                $c['industry_type_id'] = (new \App\Models\IndustryTypeModel())->detectIndustryId($searchContext);
            } catch (\Throwable $e) {}
        }
        if (!in_array($cardIndex, $c['metadata']['source_card_indices'] ?? [])) {
            $c['metadata']['source_card_indices'][] = $cardIndex;
        }

        // Sync or recreate HQ branch
        if (!empty($c['address']) || !empty($c['city'])) {
            $hasHq = false;
            foreach ($group['branches'] as &$b) {
                if (!empty($b['is_headquarters'])) {
                    $hasHq = true;
                    $b['branch_name'] = 'Trụ Sở Chính ' . (!empty($c['city']) ? $c['city'] : '');
                    $b['address'] = $c['address'];
                    $b['city'] = $c['city'];
                    $b['phone'] = $c['phone'];
                    $b['email'] = $c['email'];
                }
            }
            unset($b);
            if (!$hasHq) {
                array_unshift($group['branches'], [
                    'branch_name'     => 'Trụ Sở Chính ' . (!empty($c['city']) ? $c['city'] : (!empty($c['country']) ? $c['country'] : '')),
                    'country'         => !empty($c['country']) ? $c['country'] : 'Việt Nam',
                    'city'            => $c['city'], 'address' => $c['address'],
                    'phone'           => $c['phone'], 'email' => $c['email'],
                    'is_headquarters' => 1, 'metadata' => ['is_hq' => true],
                ]);
            }
        }

        // Merge Contacts
        if (!empty($contactName)) {
            $group['contacts'] = array_values(array_filter($group['contacts'], function($ct) { return !empty($ct['full_name']); }));
            $isDuplicate = false;
            foreach ($group['contacts'] as $existingContact) {
                if ($this->calculateNameSimilarity($existingContact['full_name'], $contactName) > 0.85) {
                    $isDuplicate = true;
                    break;
                }
            }
            if (!$isDuplicate) {
                $group['contacts'][] = [
                    'full_name'       => $contactName, 'full_name_vi' => $ocrData['contact_name_vi'] ?? $contactName,
                    'full_name_en'    => $ocrData['contact_name_en'] ?? '', 'full_name_local' => $ocrData['contact_name_local'] ?? '',
                    'position'        => $ocrData['position'] ?? '', 'position_vi' => $ocrData['position_vi'] ?? ($ocrData['position'] ?? ''),
                    'position_en'     => $ocrData['position_en'] ?? '', 'department' => $ocrData['department'] ?? '',
                    'phone'           => $ocrData['phone'] ?? '', 'phone_2' => $ocrData['phone_2'] ?? '',
                    'email'           => $ocrData['email'] ?? '', 'email_2' => $ocrData['email_2'] ?? '',
                    'is_primary'      => empty($group['contacts']) ? 1 : 0,
                    'social_media'    => is_array($ocrData['social_media'] ?? null) ? $ocrData['social_media'] : [],
                    'metadata'        => ['source_card_index' => $cardIndex],
                ];
            }
        }

        // Merge Branches
        if (!empty($ocrData['branch_info'])) {
            $newBranches = $this->parseBranches($ocrData['address'] ?? ($c['address'] ?? ''), $ocrData['city'] ?? ($c['city'] ?? ''), $ocrData['country'] ?? 'Việt Nam', $ocrData['branch_info'], $ocrData['phone'] ?? ($c['phone'] ?? ''), $ocrData['email'] ?? ($c['email'] ?? ''));
            foreach ($newBranches as $nb) {
                if (!empty($nb['is_headquarters'])) continue;
                $exists = false;
                foreach ($group['branches'] as $eb) {
                    if ($this->calculateNameSimilarity($eb['branch_name'], $nb['branch_name']) > 0.85 || (!empty($eb['address']) && $eb['address'] === $nb['address'])) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) $group['branches'][] = $nb;
            }
        }

        // Final Branch Deduplication
        $uniqueBranches = [];
        $seenAddrs = [];
        foreach ($group['branches'] as $b) {
            $key = mb_strtolower(trim(($b['address'] ?? '') . '_' . ($b['city'] ?? '')));
            if (!empty($b['address']) && isset($seenAddrs[$key])) continue;
            if (!empty($b['address'])) $seenAddrs[$key] = true;
            $uniqueBranches[] = $b;
        }
        $group['branches'] = $uniqueBranches;

        $group['source_cards'][] = ['file_path' => $cardItem['file_path'] ?? ($cardItem['path'] ?? ''), 'side' => $cardItem['side'] ?? 'single', 'card_index' => $cardIndex, 'ocr_raw' => $ocrData];
    }

    protected function findBestDbMemberMatch(array $ocrData, array $existingDbMembers): array
    {
        $bestMember = null;
        $bestMatch = ['matched' => false, 'score' => 0.0, 'type' => 'none', 'field' => ''];

        foreach ($existingDbMembers as $member) {
            $memArray = is_object($member) ? (array)$member : $member;
            $match = $this->calculateSimilarity($ocrData, $memArray);
            if ($match['matched'] && $match['score'] > $bestMatch['score']) {
                $bestMatch = $match;
                $bestMember = $member;
            }
        }

        return ['member' => $bestMember, 'match' => $bestMatch];
    }
}
