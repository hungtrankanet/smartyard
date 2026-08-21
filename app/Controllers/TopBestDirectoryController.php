<?php

namespace App\Controllers;

use Config\TopBestData;

class TopBestDirectoryController extends BaseController
{
    public function index()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $industryFilter = $this->request->getGet('industry') ?? '';
        $provinceFilter = $this->request->getGet('province') ?? '';
        $searchKeyword = trim($this->request->getGet('q') ?? '');

        $allProfiles = TopBestData::getDirectoryProfiles();
        $filtered = array_filter($allProfiles, function ($item) use ($industryFilter, $provinceFilter, $searchKeyword) {
            if (!empty($industryFilter) && $item['category_id'] !== $industryFilter) {
                return false;
            }
            if (!empty($provinceFilter) && $item['province'] !== $provinceFilter) {
                return false;
            }
            if (!empty($searchKeyword)) {
                $matchName = stripos($item['name'], $searchKeyword) !== false;
                $matchFormula = stripos($item['title_formula'], $searchKeyword) !== false;
                $matchCode = stripos($item['code'], $searchKeyword) !== false;
                if (!$matchName && !$matchFormula && !$matchCode) {
                    return false;
                }
            }
            return true;
        });

        // Group into BEST (Rank 1-10) and TOP (Rank 11-100)
        $bestProfiles = array_filter($filtered, fn($p) => $p['rank_tier'] === 'BEST');
        $topProfiles = array_filter($filtered, fn($p) => $p['rank_tier'] === 'TOP');

        $data = [
            'title'          => $isEn ? 'Directory & Official Rankings — TOP BEST GLOBAL' : 'Bảng Xếp Hạng & Danh Bạ Hồ Sơ Đạt Chuẩn — TOP BEST GLOBAL',
            'description'    => 'Tra cứu danh bạ công khai các đơn vị, thương hiệu và sản phẩm đạt chuẩn TOP & BEST trên 34 tỉnh thành Việt Nam.',
            'keywords'       => 'bang xep hang top best, danh ba doanh nghiep, ho so vinh danh, vietkings, worldkings',
            'bestProfiles'   => $bestProfiles,
            'topProfiles'    => $topProfiles,
            'totalCount'     => count($filtered),
            'industries'     => TopBestData::getIndustries(),
            'provinces'      => TopBestData::getProvinces(),
            'currentIndustry'=> $industryFilter,
            'currentProvince'=> $provinceFilter,
            'currentQuery'   => $searchKeyword,
            'userSession'    => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('directory', $data)
            . loadView('partials/_footer', $data);
    }

    public function detail($codeOrSlug = null)
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $allProfiles = TopBestData::getDirectoryProfiles();
        $profile = null;

        foreach ($allProfiles as $p) {
            if ($p['code'] === $codeOrSlug || str_slug($p['name']) === $codeOrSlug || $p['code'] === 'TBG-VN-2026-001') {
                $profile = $p;
                break;
            }
        }
        if (!$profile) {
            $profile = $allProfiles[0];
        }

        $data = [
            'title'       => esc($profile['title_formula']) . ' | TOP BEST GLOBAL',
            'description' => esc($profile['summary']),
            'keywords'    => esc($profile['name']) . ', top best global, ' . esc($profile['province']),
            'profile'     => $profile,
            'userSession' => getUserSession()
        ];

        return loadView('partials/_header', $data)
            . loadView('directory_detail', $data)
            . loadView('partials/_footer', $data);
    }

    public function embedBadge($code = null)
    {
        $allProfiles = TopBestData::getDirectoryProfiles();
        $profile = null;
        foreach ($allProfiles as $p) {
            if ($p['code'] === $code) {
                $profile = $p;
                break;
            }
        }
        if (!$profile) {
            $profile = $allProfiles[0];
        }

        return $this->response->setBody(view('themes/suntransco/partials/_embed_badge', ['profile' => $profile]));
    }
}
