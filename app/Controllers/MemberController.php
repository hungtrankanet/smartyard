<?php

namespace App\Controllers;

use App\Libraries\BusinessVerifyService;
use App\Libraries\CompanyMatcher;
use App\Libraries\OcrService;
use App\Models\IndustryTypeModel;
use App\Models\MemberBranchModel;
use App\Models\MemberCardModel;
use App\Models\MemberContactModel;
use App\Models\MemberModel;
use App\Models\MemberVerifyLogModel;

/**
 * MemberController: Enterprise Business Member Management & Verification
 */
class MemberController extends BaseAdminController
{
    protected $db;
    protected $memberModel;
    protected $industryTypeModel;
    protected $contactModel;
    protected $branchModel;
    protected $memberCardModel;
    protected $verifyLogModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
        $this->memberModel = new MemberModel();
        $this->industryTypeModel = new IndustryTypeModel();
        $this->contactModel = new MemberContactModel();
        $this->branchModel = new MemberBranchModel();
        $this->memberCardModel = new MemberCardModel();
        $this->verifyLogModel = new MemberVerifyLogModel();
    }

    protected function checkMemberPermission()
    {
        if (!authCheck()) {
            redirectToUrl(adminUrl('login'));
            exit();
        }
        if (!isSuperAdmin() && !hasPermission('members')) {
            redirectToUrl(adminUrl());
            exit();
        }
    }

    public function index()
    {
        $this->checkMemberPermission();
        $data['title'] = trans('members') ?? 'Quản Lý Đối Tác Doanh Nghiệp';
        $filters = [
            'q'                => inputGet('q'),
            'industry_type_id' => inputGet('industry_type_id'),
            'verify_status'    => inputGet('verify_status'),
            'member_type'      => inputGet('member_type'),
            'status'           => inputGet('status'),
            'city'             => inputGet('city'),
        ];
        $numRows = $this->memberModel->getMembersCount($filters);
        $data['pager'] = paginate(20, $numRows);
        $data['members'] = $this->memberModel->getMembersPaginated(20, $data['pager']->offset, $filters);
        $data['industries'] = $this->industryTypeModel->getIndustries();
        $data['stats'] = $this->memberModel->getStats();
        $data['filters'] = $filters;

        echo view('admin/includes/_header', $data);
        echo view('admin/members/index', $data);
        echo view('admin/includes/_footer');
    }

    public function addMember()
    {
        $this->checkMemberPermission();
        $data = ['title' => 'Thêm Mới Đối Tác Doanh Nghiệp', 'industries' => $this->industryTypeModel->getIndustries()];
        echo view('admin/includes/_header', $data);
        echo view('admin/members/form', $data);
        echo view('admin/includes/_footer');
    }

    private function getMemberPostInput(): array
    {
        $contacts = $this->request->getPost('contacts') ?: [];
        $pName = inputPost('representative_name'); $pPhone = inputPost('phone');
        $pEmail = inputPost('email'); $pPos = inputPost('position');
        if (empty($pName) && !empty($contacts)) {
            foreach ($contacts as $c) {
                if (!empty($c['full_name'])) {
                    $pName = $c['full_name']; $pPos = $c['position'] ?? $pPos;
                    $pPhone = $c['phone'] ?? $pPhone; $pEmail = $c['email'] ?? $pEmail;
                    if (!empty($c['is_primary'])) break;
                }
            }
        }
        return [
            'company_name'        => inputPost('company_name'),
            'company_name_en'     => inputPost('company_name_en'),
            'company_name_local'  => inputPost('company_name_local'),
            'detected_language'   => inputPost('detected_language') ?: 'vi',
            'tax_code'            => inputPost('tax_code'),
            'address'             => inputPost('address'),
            'city'                => inputPost('city'),
            'website'             => inputPost('website'),
            'fanpage'             => inputPost('fanpage'),
            'phone'               => $pPhone,
            'email'               => $pEmail,
            'representative_name' => $pName,
            'position'            => $pPos,
            'industry_type_id'    => inputPost('industry_type_id'),
            'member_type'         => inputPost('member_type') ?: 'member',
            'status'              => inputPost('status') !== null ? clrNum(inputPost('status')) : 1,
            'note'                => inputPost('note'),
        ];
    }

    public function addMemberPost()
    {
        $this->checkMemberPermission();
        $val = \Config\Services::validation();
        $val->setRule('company_name', 'Tên doanh nghiệp', 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('members/add'))->withInput();
        }

        $contacts = $this->request->getPost('contacts') ?: [];
        $branches = $this->request->getPost('branches') ?: [];
        $memberData = array_merge($this->getMemberPostInput(), ['verify_status' => 'pending', 'next_verify_at' => date('Y-m-d H:i:s', strtotime('+6 months'))]);

        $this->db->transStart();
        $memberId = $this->memberModel->addMember($memberData);
        if ($memberId) {
            $pContactId = null;
            if (!empty($contacts)) {
                foreach ($contacts as $c) {
                    if (empty($c['full_name'])) continue;
                    $cid = $this->contactModel->addContact($memberId, $c);
                    if (!empty($c['is_primary']) && $cid) $pContactId = $cid;
                }
            } elseif (!empty($memberData['representative_name'])) {
                $pContactId = $this->contactModel->addContact($memberId, ['full_name' => $memberData['representative_name'], 'position' => $memberData['position'], 'phone' => $memberData['phone'], 'email' => $memberData['email'], 'is_primary' => 1]);
            }
            if (!empty($branches)) {
                foreach ($branches as $b) {
                    if (!empty($b['branch_name'])) $this->branchModel->addBranch($memberId, $b);
                }
            }
            $this->handleCardUpload($memberId, $pContactId);
        }
        $this->db->transComplete();

        if ($this->db->transStatus() !== false && $memberId) {
            BusinessVerifyService::triggerAsyncVerification([$memberId]);
            setSuccessMessage('Đối tác đã được thêm thành công. Hệ thống đang tự động xác minh dữ liệu.');
            return redirect()->to(adminUrl('members'));
        }
        setErrorMessage('Không thể lưu thông tin đối tác. Vui lòng thử lại.');
        return redirect()->to(adminUrl('members/add'))->withInput();
    }

    public function editMember($id)
    {
        $this->checkMemberPermission();
        $member = $this->memberModel->getMemberWithRelations($id);
        if (empty($member)) {
            setErrorMessage('Không tìm thấy thông tin đối tác.');
            return redirect()->to(adminUrl('members'));
        }
        $data = ['title' => 'Chỉnh Sửa Thông Tin Đối Tác', 'member' => $member, 'industries' => $this->industryTypeModel->getIndustries()];
        echo view('admin/includes/_header', $data);
        echo view('admin/members/form', $data);
        echo view('admin/includes/_footer');
    }

    public function editMemberPost($id)
    {
        $this->checkMemberPermission();
        $id = clrNum($id);
        $val = \Config\Services::validation();
        $val->setRule('company_name', 'Tên doanh nghiệp', 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(adminUrl('members/edit/' . $id))->withInput();
        }

        $contacts = $this->request->getPost('contacts') ?: [];
        $branches = $this->request->getPost('branches') ?: [];
        $memberData = $this->getMemberPostInput();

        $this->db->transStart();
        $this->memberModel->updateMember($id, $memberData);

        if (!empty($contacts)) {
            $this->contactModel->deleteByCompanyId($id);
            $pContactId = null;
            foreach ($contacts as $c) {
                if (empty($c['full_name'])) continue;
                $cid = $this->contactModel->addContact($id, $c);
                if (!empty($c['is_primary']) && $cid) $pContactId = $cid;
            }
        }
        if (!empty($branches)) {
            $this->branchModel->deleteByCompanyId($id);
            foreach ($branches as $b) {
                if (!empty($b['branch_name'])) $this->branchModel->addBranch($id, $b);
            }
        }
        $this->handleCardUpload($id, $pContactId ?? null);
        $this->db->transComplete();

        if ($this->db->transStatus() !== false) {
            setSuccessMessage('Đã cập nhật thông tin đối tác thành công.');
            return redirect()->to(adminUrl('members'));
        }
        setErrorMessage('Không thể cập nhật đối tác. Vui lòng thử lại.');
        return redirect()->to(adminUrl('members/edit/' . $id))->withInput();
    }

    public function deleteMemberPost()
    {
        $this->checkMemberPermission();
        $id = clrNum(inputPost('id') ?: inputGet('id'));
        if (!empty($id)) {
            $this->memberModel->deleteMember($id);
            setSuccessMessage('Đã xoá đối tác thành công.');
        } else {
            setErrorMessage('ID đối tác không hợp lệ.');
        }
        if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success']);
        return redirect()->to(adminUrl('members'));
    }

    public function uploadCards()
    {
        $this->checkMemberPermission();
        $data = ['title' => 'Tải Lên Danh Thiếp & OCR Tự Động (AI Vision)', 'queue' => $this->session->get('ocr_batch_queue') ?: [], 'aiWriter' => aiWriter()];
        echo view('admin/includes/_header', $data);
        echo view('admin/members/upload_cards', $data);
        echo view('admin/includes/_footer');
    }

    public function uploadFileOnlyAjax()
    {
        $this->checkMemberPermission();
        $file = $this->request->getFile('card_file');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['status' => 'error', 'message' => 'Tệp không hợp lệ']);
        $uploadDir = FCPATH . 'uploads/cards/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) $uploadDir = FCPATH . 'uploads/tmp/';
        $ext = $file->guessExtension() ?: ($file->getClientExtension() ?: 'jpg');
        $fileName = 'card_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        $file->move($uploadDir, $fileName);
        $relPath = str_replace(FCPATH, '', $uploadDir . $fileName);
        return $this->response->setJSON(['status' => 'success', 'file_path' => $relPath, 'filename' => $file->getClientName() ?: $fileName, 'full_url' => base_url($relPath)]);
    }

    public function ocrPairAjax()
    {
        $this->checkMemberPermission();
        $frontPath = inputPost('front_path');
        $backPath  = inputPost('back_path');
        $side      = inputPost('side') ?: (!empty($backPath) ? 'pair' : 'single');
        $ocrService = new OcrService();
        if (!empty($frontPath) && !empty($backPath)) {
            $extracted = $ocrService->extractBusinessCard(['front' => FCPATH . ltrim($frontPath, '/'), 'back' => FCPATH . ltrim($backPath, '/')]);
        } elseif (!empty($frontPath)) {
            $extracted = $ocrService->extractBusinessCard(FCPATH . ltrim($frontPath, '/'));
        } elseif (!empty($backPath)) {
            $extracted = $ocrService->extractBusinessCard(FCPATH . ltrim($backPath, '/'));
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Thiếu ảnh để quét']);
        }
        $item = array_merge($extracted, ['file_path' => $frontPath ?: $backPath, 'front_path' => $frontPath, 'back_path' => $backPath, 'side' => $side, 'filename' => basename($frontPath ?: $backPath)]);
        return $this->response->setJSON(['status' => 'success', 'data' => $item]);
    }

    public function uploadCardAjax()
    {
        $this->checkMemberPermission();
        $file = $this->request->getFile('card_file');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['status' => 'error', 'message' => 'Tệp danh thiếp không hợp lệ']);
        $uploadDir = FCPATH . 'uploads/cards/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) $uploadDir = FCPATH . 'uploads/tmp/';
        $ext = $file->guessExtension() ?: ($file->getClientExtension() ?: 'jpg');
        $fileName = 'card_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        $file->move($uploadDir, $fileName);
        $fullPath = $uploadDir . $fileName;
        $extracted = (new OcrService())->extractBusinessCard($fullPath);
        $item = array_merge($extracted, ['file_path' => str_replace(FCPATH, '', $fullPath), 'filename' => $file->getClientName() ?: $fileName, 'side' => inputPost('side') ?: 'single']);
        return $this->response->setJSON(['status' => 'success', 'data' => $item]);
    }

    public function confirmOcr()
    {
        $this->checkMemberPermission();
        $queue = $this->session->get('ocr_batch_queue') ?: [];
        $postCards = $this->request->getPost('cards_json');
        if (!empty($postCards)) {
            $decoded = json_decode($postCards, true);
            if (!empty($decoded) && is_array($decoded)) {
                $queue = $decoded;
                $this->session->set('ocr_batch_queue', $queue);
            }
        }
        if (empty($queue)) {
            setErrorMessage('Chưa có dữ liệu danh thiếp trong hàng đợi. Vui lòng tải lên ảnh để AI nhận diện.');
            return redirect()->to(adminUrl('members/upload-cards'));
        }

        $existingMembers = $this->memberModel->getAllCompaniesForMatching();
        $groupedData = (new CompanyMatcher())->groupCards($queue, $existingMembers);
        $data = ['title' => 'Xác Nhận & Gom Nhóm Danh Thiếp (AI OCR)', 'groupedData' => $groupedData, 'industryTypes' => $this->industryTypeModel->getIndustries(), 'totalCards' => count($queue), 'totalGroups' => count($groupedData)];

        echo view('admin/includes/_header', $data);
        echo view('admin/members/confirm_ocr', $data);
        echo view('admin/includes/_footer');
    }

    public function confirmOcrPost()
    {
        $this->checkMemberPermission();
        $groups = $this->request->getPost('groups');
        if (empty($groups) || !is_array($groups)) {
            setErrorMessage('Dữ liệu xác nhận không hợp lệ.');
            return redirect()->to(adminUrl('members/confirm-ocr'));
        }

        $this->db->transStart();
        $savedCount = 0;
        $savedMemberIds = [];

        foreach ($groups as $grp) {
            $company = $grp['company_info'] ?? [];
            if (empty($company['company_name'])) continue;

            $existingId = !empty($grp['existing_member_id']) ? clrNum($grp['existing_member_id']) : null;
            $memberId = null;

            if ($existingId && $this->memberModel->getMember($existingId)) {
                $memberId = $existingId;
                $updateData = array_filter(['company_name_en' => $company['company_name_en'] ?? null, 'company_name_local' => $company['company_name_local'] ?? null, 'tax_code' => $company['tax_code'] ?? null, 'website' => $company['website'] ?? null, 'fanpage' => $company['fanpage'] ?? null]);
                if (!empty($updateData)) $this->memberModel->updateMember($memberId, $updateData);
            } else {
                $memberData = [
                    'company_name' => $company['company_name'], 'company_name_en' => $company['company_name_en'] ?? null, 'company_name_local' => $company['company_name_local'] ?? null,
                    'detected_language' => $company['detected_language'] ?? 'vi', 'tax_code' => $company['tax_code'] ?? null, 'address' => $company['address'] ?? null, 'city' => $company['city'] ?? null,
                    'website' => $company['website'] ?? null, 'fanpage' => $company['fanpage'] ?? null, 'phone' => $company['phone'] ?? null, 'email' => $company['email'] ?? null,
                    'representative_name' => $company['representative_name'] ?? null, 'position' => $company['position'] ?? null, 'industry_type_id' => !empty($company['industry_type_id']) ? clrNum($company['industry_type_id']) : null,
                    'member_type' => $company['member_type'] ?? 'member', 'status' => 1, 'verify_status' => 'pending', 'next_verify_at' => date('Y-m-d H:i:s', strtotime('+6 months')), 'note' => $company['note'] ?? null,
                ];
                $memberId = $this->memberModel->addMember($memberData);
            }

            if (!$memberId) continue;
            $savedCount++;
            $savedMemberIds[] = $memberId;

            $contactMap = [];
            $primaryContactId = null;
            if (!empty($grp['contacts']) && is_array($grp['contacts'])) {
                foreach ($grp['contacts'] as $cIdx => $c) {
                    if (empty($c['full_name'])) continue;
                    $cid = $this->contactModel->addContact($memberId, $c);
                    if ($cid) {
                        $contactMap[$cIdx] = $cid;
                        if (!empty($c['is_primary']) || $primaryContactId === null) $primaryContactId = $cid;
                    }
                }
            }

            if (!empty($grp['branches']) && is_array($grp['branches'])) {
                foreach ($grp['branches'] as $b) {
                    if (!empty($b['branch_name'])) $this->branchModel->addBranch($memberId, $b);
                }
            }

            if (!empty($grp['cards']) && is_array($grp['cards'])) {
                foreach ($grp['cards'] as $card) {
                    if (empty($card['file_path'])) continue;
                    $targetContactId = (!empty($card['contact_index']) && isset($contactMap[$card['contact_index']])) ? $contactMap[$card['contact_index']] : $primaryContactId;
                    $this->memberCardModel->addCard(['member_id' => $memberId, 'contact_id' => $targetContactId, 'file_path' => $card['file_path'], 'side' => $card['side'] ?? 'single', 'ocr_raw' => !empty($card['ocr_raw']) ? (is_array($card['ocr_raw']) ? json_encode($card['ocr_raw'], JSON_UNESCAPED_UNICODE) : $card['ocr_raw']) : null, 'ocr_parsed' => json_encode($company, JSON_UNESCAPED_UNICODE), 'ocr_status' => 'done']);
                }
            }
        }

        $this->db->transComplete();
        if ($this->db->transStatus() !== false && $savedCount > 0) {
            $this->session->remove('ocr_batch_queue');
            if (!empty($savedMemberIds)) BusinessVerifyService::triggerAsyncVerification($savedMemberIds);
            setSuccessMessage("Đã lưu thành công {$savedCount} doanh nghiệp! Hệ thống đang tự động xác minh Google Maps, Fanpage và Website.");
            return redirect()->to(adminUrl('members'));
        }
        setErrorMessage('Không thể hoàn tất lưu danh thiếp. Vui lòng kiểm tra lại dữ liệu.');
        return redirect()->to(adminUrl('members/confirm-ocr'));
    }

    public function skipOcr()
    {
        $this->checkMemberPermission();
        $this->session->remove('ocr_batch_queue');
        setSuccessMessage('Đã bỏ qua toàn bộ hàng đợi OCR.');
        return redirect()->to(adminUrl('members'));
    }

    public function detail($id)
    {
        $this->checkMemberPermission();
        $member = $this->memberModel->getMemberWithRelations($id);
        if (empty($member)) {
            setErrorMessage('Không tìm thấy thông tin đối tác.');
            return redirect()->to(adminUrl('members'));
        }
        $data = ['title' => 'Hồ Sơ 360° Doanh Nghiệp: ' . esc($member->company_name), 'member' => $member];
        echo view('admin/includes/_header', $data);
        echo view('admin/members/detail', $data);
        echo view('admin/includes/_footer');
    }

    public function verifyMember($id)
    {
        $this->checkMemberPermission();
        $res = (new BusinessVerifyService($this->memberModel, $this->verifyLogModel))->verifyMember($id);
        $statusText = ($res['status'] === 'verified') ? 'Hợp lệ' : (($res['status'] === 'failed') ? 'Đã đóng cửa' : 'Chưa xác định');
        setSuccessMessage('Xác minh doanh nghiệp hoàn tất. Kết quả: ' . $statusText);
        return redirect()->to(adminUrl('members/detail/' . clrNum($id)));
    }

    public function verifyMemberAjax($id = null)
    {
        $this->checkMemberPermission();
        $id = clrNum($id ?: (inputPost('member_id') ?: inputGet('id')));
        if (empty($id)) return $this->response->setJSON(['status' => 'error', 'message' => 'ID doanh nghiệp không hợp lệ', 'csrf_token' => csrf_hash()]);
        try {
            $service = new BusinessVerifyService($this->memberModel, $this->verifyLogModel); $service->setSleepSeconds(0); $res = $service->verifyMember($id);
            return $this->response->setJSON(['status' => 'success', 'verify_status' => $res['status'] ?? 'unknown', 'result' => $res, 'member' => $this->memberModel->getMemberWithRelations($id), 'recent_logs' => $this->verifyLogModel->getLogsByMemberId($id, 5), 'csrf_token' => csrf_hash()]);
        } catch (\Throwable $e) { return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage(), 'result' => ['status' => 'unknown', 'message' => $e->getMessage()], 'csrf_token' => csrf_hash()]); }
    }

    public function verifyLogs()
    {
        $this->checkMemberPermission();
        $data['title'] = 'Nhật Ký & Tiến Độ Xác Minh Doanh Nghiệp';
        $filters = ['q' => inputGet('q'), 'check_type' => inputGet('check_type'), 'result' => inputGet('result'), 'member_id' => inputGet('member_id')];
        $perPage = 25; $numRows = $this->verifyLogModel->getLogsCount($filters);
        $data['pager'] = paginate($perPage, $numRows);
        $data['logs'] = $this->verifyLogModel->getLogsPaginated($perPage, $data['pager']->offset, $filters);
        $data['filters'] = $filters; $data['totalLogs'] = $numRows; $data['stats'] = $this->memberModel->getStats();
        echo view('admin/includes/_header', $data); echo view('admin/members/verify_logs', $data); echo view('admin/includes/_footer');
    }

    public function getPendingQueueAjax()
    {
        $this->checkMemberPermission();
        $members = $this->memberModel->getMembersPaginated(100, 0, ['verify_status' => 'pending', 'status' => 1]);
        $queue = array_map(fn($m) => ['id' => $m->id, 'company_name' => $m->company_name, 'tax_code' => $m->tax_code, 'website' => $m->website, 'fanpage' => $m->fanpage], $members);
        return $this->response->setJSON(['status' => 'success', 'queue' => $queue, 'total' => count($queue)]);
    }

    public function posts() {
        $this->checkMemberPermission();
        $postModel = new \App\Models\MemberPostModel(); $filters = ['q' => inputGet('q'), 'status' => inputGet('status')];
        $perPage = 20; $numRows = $postModel->getPostsCount($filters); $data['pager'] = paginate($perPage, $numRows);
        $data['posts'] = $postModel->getPendingPostsPaginated($perPage, $data['pager']->offset, $filters);
        $data['filters'] = $filters; $data['title'] = 'Kiểm Duyệt Bài Giới Thiệu Doanh Nghiệp';
        echo view('admin/includes/_header', $data); echo view('admin/members/posts', $data); echo view('admin/includes/_footer');
    }

    public function approvePost($id) {
        $this->checkMemberPermission();
        if ((new \App\Models\MemberPostModel())->approvePost(clrNum($id), (int)user()->id)) setSuccessMessage('Đã duyệt bài giới thiệu.');
        return redirect()->to(adminUrl('members/posts'));
    }

    public function rejectPost($id) {
        $this->checkMemberPermission();
        $reason = trim((string)inputPost('reject_reason')) ?: 'Nội dung chưa phù hợp.';
        if ((new \App\Models\MemberPostModel())->rejectPost(clrNum($id), $reason)) setSuccessMessage('Đã từ chối bài viết.');
        return redirect()->to(adminUrl('members/posts'));
    }

    protected function handleCardUpload($memberId, $contactId = null)
    {
        $file = $this->request->getFile('card_image');
        if ($file && $file->isValid()) {
            $uploadDir = FCPATH . 'uploads/cards/';
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
            $ext = $file->guessExtension() ?: ($file->getClientExtension() ?: 'jpg');
            $fileName = 'card_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            if ($file->move($uploadDir, $fileName)) {
                $this->memberCardModel->addCard(['member_id' => $memberId, 'contact_id' => $contactId, 'file_path' => 'uploads/cards/' . $fileName, 'side' => 'single', 'ocr_status' => 'done']);
            }
        }
    }
}
