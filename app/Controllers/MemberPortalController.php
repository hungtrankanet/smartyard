<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\MemberOtpModel;
use App\Models\MemberPostModel;
use App\Models\MemberInteractionModel;
use App\Models\IndustryTypeModel;
use App\Models\AuthModel;

class MemberPortalController extends BaseController
{
    protected $db;
    protected MemberModel $memberModel;
    protected MemberOtpModel $otpModel;
    protected MemberPostModel $memberPostModel;
    protected MemberInteractionModel $interactionModel;
    protected IndustryTypeModel $industryModel;
    protected AuthModel $authModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db                = \Config\Database::connect();
        $this->memberModel       = new MemberModel();
        $this->otpModel          = new MemberOtpModel();
        $this->memberPostModel   = new MemberPostModel();
        $this->interactionModel  = new MemberInteractionModel();
        $this->industryModel     = new IndustryTypeModel();
        $this->authModel         = new AuthModel();
        $this->ensureUserMemberIdColumnExists();
    }

    private function ensureUserMemberIdColumnExists()
    {
        if ($this->db->tableExists('users')) {
            $fields = $this->db->getFieldNames('users');
            if (!in_array('member_id', $fields, true)) {
                $forge = \Config\Database::forge();
                $forge->addColumn('users', [
                    'member_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'role_id',
                    ],
                ]);
                @$this->db->query("ALTER TABLE `users` ADD INDEX `idx_users_member_id` (`member_id`)");
            }
        }
    }

    public function index()
    {
        if (authCheck()) {
            if (!empty(user()->member_id) || !isAdmin()) return redirect()->to(langBaseUrl('member/dashboard'));
            return redirect()->to(adminUrl());
        }
        return redirect()->to(langBaseUrl('member/login'));
    }

    public function register()
    {
        if (authCheck() && (!empty(user()->member_id) || !isAdmin())) return redirect()->to(langBaseUrl('member/dashboard'));
        $data = setPageMeta('Đăng Ký Thành Viên Doanh Nghiệp (B2B Logistics)');
        $data['title'] = 'Đăng Ký Thành Viên Doanh Nghiệp (B2B Logistics)';
        $data['industries'] = $this->industryModel->getIndustries();
        $data['userSession'] = getUserSession();
        return loadView('partials/_header', $data)
            . view('auth/member_register', $data)
            . loadView('partials/_footer', $data);
    }

    public function sendRegisterOtpAjax()
    {
        $email = trim((string)inputPost('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Địa chỉ Email không hợp lệ.', 'csrf_token' => csrf_hash()]);
        }

        $authModel = new \App\Models\AuthModel();
        if ($authModel->getUserByEmail($email)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email này đã được đăng ký tài khoản trong hệ thống.', 'csrf_token' => csrf_hash()]);
        }

        if (!$this->otpModel->canResendOtp($email, 'register')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng đợi 60 giây trước khi yêu cầu gửi lại mã OTP mới.', 'csrf_token' => csrf_hash()]);
        }

        $otp = $this->otpModel->generateOtp($email, 'register');
        $companyName = trim((string)inputPost('company_name')) ?: 'Quý Doanh nghiệp';
        $sent = false;
        try {
            $emailModel = new \App\Models\EmailModel();
            $sent = (bool)$emailModel->sendEmailMemberOtp($email, $otp, $companyName);
        } catch (\Throwable $e) {}

        if (!$sent) {
            $subject = '[TOP BEST GLOBAL] Mã OTP xác thực thành viên: ' . $otp;
            $msg = "Xin chào " . $companyName . ",\n\nMã OTP xác thực đăng ký tài khoản thành viên TOP BEST GLOBAL của bạn là: " . $otp . "\n\nMã có hiệu lực trong 5 phút.\n\nTrân trọng,\nTOP BEST GLOBAL.";
            @mail($email, $subject, $msg, "From: no-reply@topbestglobal.com\r\nContent-Type: text/plain; charset=UTF-8");
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Mã OTP đã được gửi đến ' . $email . '. Vui lòng kiểm tra hòm thư (và mục Spam).',
            'expires_in' => 300,
            'csrf_token' => csrf_hash(),
            'debug_otp'  => (ENVIRONMENT !== 'production') ? $otp : null,
        ]);
    }

    public function verifyRegisterOtpAjax()
    {
        $email       = trim((string)inputPost('email'));
        $otp         = trim((string)inputPost('otp'));
        $companyName = trim((string)inputPost('company_name'));
        $fullName    = trim((string)inputPost('full_name'));
        $phone       = trim((string)inputPost('phone'));
        $password    = (string)inputPost('password');
        $industryId  = clrNum(inputPost('industry_type_id'));

        if (empty($companyName) || empty($email) || empty($password) || empty($otp)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ tất cả thông tin bắt buộc.', 'csrf_token' => csrf_hash()]);
        }
        if (strlen($password) < 6) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mật khẩu phải có ít nhất 6 ký tự.', 'csrf_token' => csrf_hash()]);
        }

        if (!$this->otpModel->verifyOtp($email, $otp, 'register')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Mã OTP không chính xác hoặc đã hết hạn (5 phút).', 'csrf_token' => csrf_hash()]);
        }

        $authModel = new \App\Models\AuthModel();
        if ($authModel->getUserByEmail($email)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Email đã tồn tại trong hệ thống.', 'csrf_token' => csrf_hash()]);
        }

        try {
            $this->db->transStart();

            $preferredLang = $this->request->getPost('preferred_lang') ?: 'vi';

            // 1. Tạo Doanh Nghiệp trong bảng members
            $memberId = $this->memberModel->addMember([
                'company_name'        => $companyName,
                'email'               => $email,
                'phone'               => $phone,
                'representative_name' => $fullName ?: $companyName,
                'industry_type_id'    => $industryId ?: null,
                'preferred_lang'      => $preferredLang,
                'member_type'         => 'member',
                'status'              => 1,
                'verify_status'       => 'pending',
                'note'                => 'Đăng ký trực tuyến qua Member Portal',
            ]);

            if (!$memberId) {
                $this->db->transRollback();
                $err = $this->db->error();
                log_message('error', 'addMember failed: ' . json_encode($err));
                return $this->response->setJSON(['status' => 'error', 'message' => 'Lỗi tạo hồ sơ doanh nghiệp. ' . ($err['message'] ?? ''), 'csrf_token' => csrf_hash()]);
            }

            // 2. Tạo User trong bảng users với role_id = 3
            $username = explode('@', $email)[0] . '_' . mt_rand(100, 999);
            $this->db->table('users')->insert([
                'username'              => $username,
                'slug'                  => $authModel->generateUniqueSlug($username),
                'email'                 => $email,
                'password'              => password_hash($password, PASSWORD_DEFAULT),
                'role_id'               => 3,
                'user_type'             => 'registered',
                'member_id'             => $memberId,
                'status'                => 1,
                'email_status'          => 1,
                'token'                 => generateToken(),
                'avatar'                => '',
                'show_email_on_profile' => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'last_seen'             => date('Y-m-d H:i:s'),
            ]);
            $userId = $this->db->insertID();

            // 3. Tạo Contact đại diện
            $contactModel = new \App\Models\MemberContactModel();
            $contactModel->addContact($memberId, [
                'full_name'  => $fullName ?: $companyName,
                'phone'      => $phone,
                'email'      => $email,
                'is_primary' => 1,
            ]);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $err = $this->db->error();
                log_message('error', 'Member register transStatus false: ' . json_encode($err));
                return $this->response->setJSON([
                    'status'     => 'error',
                    'message'    => 'Lỗi lưu dữ liệu: ' . (!empty($err['message']) ? $err['message'] : 'Giao dịch database bị từ chối.'),
                    'csrf_token' => csrf_hash(),
                ]);
            }

            // Tự động đăng nhập
            $user = $authModel->getUser($userId);
            if ($user) {
                $authModel->loginUser($user);
            }

            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => 'Đăng ký thành công! Đang chuyển hướng vào Member Portal...',
                'redirect_url' => langBaseUrl('member/dashboard'),
                'csrf_token'   => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Member register exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status'     => 'error',
                'message'    => 'Lỗi hệ thống: ' . $e->getMessage(),
                'csrf_token' => csrf_hash(),
            ]);
        }
    }

    public function login()
    {
        if (authCheck()) {
            if (!empty(user()->member_id) || !isAdmin()) return redirect()->to(langBaseUrl('member/dashboard'));
            return redirect()->to(adminUrl());
        }
        $data = setPageMeta('Đăng Nhập Thành Viên Doanh Nghiệp');
        $data['title'] = 'Đăng Nhập Thành Viên Doanh Nghiệp';
        $data['userSession'] = getUserSession();
        return loadView('partials/_header', $data)
            . view('auth/member_login', $data)
            . loadView('partials/_footer', $data);
    }

    public function loginPost()
    {
        $email    = trim((string)inputPost('email'));
        $password = (string)inputPost('password');

        $authModel = new \App\Models\AuthModel();
        $user = $authModel->getUserByEmail($email);
        if (!$user || !password_verify($password, $user->password)) {
            $this->session->setFlashdata('error', 'Email hoặc mật khẩu không chính xác.');
            return redirect()->to(langBaseUrl('member/login'))->withInput();
        }
        if ($user->status != 1) {
            $this->session->setFlashdata('error', 'Tài khoản của bạn đang bị tạm khóa. Vui lòng liên hệ Admin.');
            return redirect()->to(langBaseUrl('member/login'));
        }

        $authModel->loginUser($user);

        if (isAdmin()) return redirect()->to(adminUrl());
        return redirect()->to(langBaseUrl('member/dashboard'));
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(langBaseUrl('member/login'));
    }

    public function dashboard()
    {
        $this->checkMemberAuth();
        $user = user();
        $memberId = (int)($user->member_id ?? 0);
        $member = $this->memberModel->getMemberWithRelations($memberId);

        if (!$member) {
            $this->session->setFlashdata('error', 'Hồ sơ doanh nghiệp không tồn tại.');
            return redirect()->to(langBaseUrl());
        }

        $post = $this->memberPostModel->getPostByMemberId($memberId);
        $visitors = $this->interactionModel->getProfileVisitors($memberId, 25);
        $stats = $this->interactionModel->getVisitorStats($memberId);
        $messages = $this->interactionModel->getInboxMessages($memberId, 30);
        $unreadCount = $this->interactionModel->getUnreadCount($memberId);
        $industries = $this->industryModel->getIndustries();
        $allOtherMembers = $this->memberModel->getMembersPaginated(100, 0, ['status' => 1]);
        $otherMembers = array_values(array_filter($allOtherMembers, function($m) use ($memberId) {
            return $m->id != $memberId;
        }));

        $data = setPageMeta('Cổng Thành Viên & Bảng Điều Khiển Đối Tác - ' . $member->company_name);
        $data['title']       = 'Cổng Thành Viên & Bảng Điều Khiển Đối Tác - ' . $member->company_name;
        $data['user']        = $user;
        $data['member']      = $member;
        $data['post']        = $post;
        $data['visitors']    = $visitors;
        $data['stats']       = $stats;
        $data['messages']    = $messages;
        $data['unreadCount'] = $unreadCount;
        $data['industries']  = $industries;
        $data['otherMembers'] = $otherMembers;
        $data['userSession'] = getUserSession();

        return loadView('partials/_header', $data)
            . view('member_portal/dashboard', $data)
            . loadView('partials/_footer', $data);
    }

    public function saveIntroductionPostAjax()
    {
        $this->checkMemberAuth();
        $user = user();
        $memberId = (int)($user->member_id ?? 0);

        $title   = trim((string)inputPost('title'));
        $summary = trim((string)inputPost('summary'));
        $content = trim((string)inputPost('content'));

        if (empty($title) || empty($content)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tiêu đề và nội dung bài giới thiệu không được để trống.', 'csrf_token' => csrf_hash()]);
        }

        $imagePath = null;
        $file = $this->request->getFile('banner_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = FCPATH . 'uploads/members';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $newName);
            $imagePath = 'uploads/members/' . $newName;
        }

        $postData = [
            'title'   => $title,
            'summary' => $summary,
            'content' => $content,
        ];
        if ($imagePath) {
            $postData['image_default'] = $imagePath;
        }

        $saved = $this->memberPostModel->saveMemberPost($memberId, (int)$user->id, $postData);
        if ($saved) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Đã lưu và gửi bài giới thiệu tới Ban Quản Trị duyệt!', 'csrf_token' => csrf_hash()]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Không thể lưu bài viết. Vui lòng thử lại.', 'csrf_token' => csrf_hash()]);
    }

    public function sendMessageAjax()
    {
        $receiverMemberId = clrNum(inputPost('receiver_member_id'));
        $message = trim((string)inputPost('message'));
        $subject = trim((string)inputPost('subject')) ?: 'Yêu cầu kết nối & Báo giá Logistics';
        $senderCompany = trim((string)inputPost('sender_company'));
        $senderName = trim((string)inputPost('sender_name'));
        $senderPhone = trim((string)inputPost('sender_phone'));
        $senderEmail = trim((string)inputPost('sender_email'));

        if (empty($receiverMemberId) || empty($message)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng chọn doanh nghiệp nhận và nhập nội dung tin nhắn.', 'csrf_token' => csrf_hash()]);
        }

        $senderMemberId = null;
        $senderUserId = null;

        if (authCheck()) {
            $user = user();
            $senderUserId = (int)$user->id;
            $senderMemberId = (int)($user->member_id ?? 0);
            $senderMember = $this->memberModel->getMember($senderMemberId);
            $senderCompany = $senderCompany ?: ($senderMember->company_name ?? $user->username);
            $senderName = $senderName ?: $user->username;
            $senderEmail = $senderEmail ?: $user->email;
            $senderPhone = $senderPhone ?: ($senderMember->phone ?? null);
        } else {
            if (empty($senderCompany) && empty($senderName)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng nhập tên công ty hoặc người gửi liên hệ.', 'csrf_token' => csrf_hash()]);
            }
            if (empty($senderPhone) && empty($senderEmail)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Vui lòng để lại số điện thoại hoặc email để đối tác phản hồi.', 'csrf_token' => csrf_hash()]);
            }
        }

        $msgId = $this->interactionModel->sendMessage([
            'sender_user_id'     => $senderUserId,
            'sender_member_id'   => $senderMemberId ?: null,
            'receiver_member_id' => $receiverMemberId,
            'sender_name'        => $senderName ?: $senderCompany,
            'sender_company'     => $senderCompany ?: 'Khách hàng B2B TOP BEST GLOBAL',
            'sender_phone'       => $senderPhone,
            'sender_email'       => $senderEmail,
            'subject'            => $subject,
            'message'            => $message,
        ]);

        if ($senderMemberId && $senderMemberId != $receiverMemberId) {
            $this->interactionModel->recordView($receiverMemberId, $senderMemberId, 'b2b_inquiry');
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Tin nhắn B2B & yêu cầu báo giá của bạn đã được gửi trực tiếp tới hòm thư đối tác thành công!',
            'message_id' => $msgId,
            'csrf_token' => csrf_hash()
        ]);
    }

    public function markMessageReadAjax($id)
    {
        $this->checkMemberAuth();
        $memberId = (int)(user()->member_id ?? 0);
        $ok = $this->interactionModel->markMessageAsRead(clrNum($id), $memberId);
        return $this->response->setJSON(['status' => $ok ? 'success' : 'error']);
    }

    protected function checkMemberAuth()
    {
        if (!authCheck()) {
            redirectToUrl(langBaseUrl('member/login'));
            exit;
        }
    }
}
