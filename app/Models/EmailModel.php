<?php namespace App\Models;

require APPPATH . "ThirdParty/swiftmailer/vendor/autoload.php";
require APPPATH . "ThirdParty/phpmailer/vendor/autoload.php";
require APPPATH . "ThirdParty/mailjet/vendor/autoload.php";

use CodeIgniter\Model;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Mailjet\Resources;

class EmailModel extends BaseModel
{
    //send text email
    public function sendTestEmail($email, $subject, $message)
    {
        if (!empty($email)) {
            $data = [
                'subject' => $subject,
                'message' => $message,
                'to' => $email,
                'template_path' => "email/email_newsletter",
                'subscriber' => "",
            ];
            return $this->sendEmail($data);
        }
    }

    //send member OTP email
    public function sendEmailMemberOtp($email, $otp, $companyName = '')
    {
        if (!empty($email) && !empty($otp)) {
            $data = [
                'subject' => '[TOP BEST GLOBAL] Mã OTP xác thực thành viên: ' . $otp,
                'to' => $email,
                'template_path' => 'email/email_member_otp',
                'otp_code' => $otp,
                'company_name' => $companyName ?: 'Quý Doanh nghiệp',
            ];
            return $this->sendEmail($data);
        }
        return false;
    }

    //send email activation
    public function sendEmailActivation($userId)
    {
        $user = getUserById($userId);
        if (!empty($user)) {
            $token = $user->token;
            if (empty($token)) {
                $token = generateToken();
                $this->db->table('users')->where('id', $user->id)->update(['token' => $token]);
            }
            $data = [
                'subject' => trans("confirm_your_email"),
                'to' => $user->email,
                'template_path' => "email/email_activation",
                'token' => $token
            ];
            return $this->sendEmail($data);
        }
        return false;
    }

    //send reset password email
    public function sendEmailResetPassword($userId)
    {
        $user = getUserById($userId);
        if (!empty($user)) {
            $token = $user->token;
            if (empty($token)) {
                $token = generateToken();
                $this->db->table('users')->where('id', $user->id)->update(['token' => $token]);
            }
            $data = [
                'subject' => trans("reset_password"),
                'to' => $user->email,
                'template_path' => "email/email_reset_password",
                'token' => $token
            ];
            return $this->sendEmail($data);
        }
        return false;
    }

    /**
     * Send RFQ quotation notification email to Sales team & Confirmation to customer
     */
    public function sendEmailServiceRfq(array $rfqData)
    {
        $salesEmail = !empty($this->generalSettings->mail_contact) 
            ? $this->generalSettings->mail_contact 
            : (!empty($this->generalSettings->mail_reply_to) ? $this->generalSettings->mail_reply_to : 'sales@topbestglobal.com');

        $customerEmail = !empty($rfqData['email']) ? trim($rfqData['email']) : '';
        $customerName  = !empty($rfqData['name']) ? trim($rfqData['name']) : 'Quý Khách Hàng';
        $companyName   = !empty($rfqData['company']) ? trim($rfqData['company']) : 'Chưa cập nhật';
        $phone         = !empty($rfqData['phone']) ? trim($rfqData['phone']) : 'Chưa cập nhật';
        $serviceName   = !empty($rfqData['service_name']) ? trim($rfqData['service_name']) : 'Vận Tải & Logistics';
        $pol           = !empty($rfqData['pol']) ? trim($rfqData['pol']) : '';
        $pod           = !empty($rfqData['pod']) ? trim($rfqData['pod']) : '';
        $shipmentMode  = !empty($rfqData['type']) ? trim($rfqData['type']) : '';
        $notes         = !empty($rfqData['message']) ? trim($rfqData['message']) : '';

        // 1. Email to Sales Team
        $salesSubject = "[TOP BEST GLOBAL RFQ] Báo giá mới: {$serviceName} - {$customerName} ({$companyName})";
        $salesMessage = "Kính gửi Ban Kinh Doanh TOP BEST GLOBAL,\n\n"
            . "Hệ thống vừa tiếp nhận yêu cầu báo giá dịch vụ trực tuyến:\n"
            . "--------------------------------------------------------\n"
            . "- Dịch vụ yêu cầu: " . $serviceName . "\n"
            . "- Khách hàng: " . $customerName . "\n"
            . "- Công ty: " . $companyName . "\n"
            . "- Số điện thoại / Zalo: " . $phone . "\n"
            . "- Email: " . $customerEmail . "\n"
            . (!empty($pol) ? "- Cảng/Điểm đi (POL): " . $pol . "\n" : "")
            . (!empty($pod) ? "- Cảng/Điểm đến (POD): " . $pod . "\n" : "")
            . (!empty($shipmentMode) ? "- Loại hình / Container: " . $shipmentMode . "\n" : "")
            . "- Nội dung yêu cầu: " . ($notes ?: 'Yêu cầu tư vấn báo giá cước vận chuyển tối ưu') . "\n"
            . "- Thời gian gửi: " . date('d/m/Y H:i:s') . "\n"
            . "--------------------------------------------------------\n"
            . "Vui lòng liên hệ phản hồi khách hàng trong thời gian sớm nhất.";

        $this->sendEmail([
            'subject'       => $salesSubject,
            'message'       => $salesMessage,
            'to'            => $salesEmail,
            'template_path' => "email/email_newsletter",
            'subscriber'    => "",
        ]);

        // 2. Confirmation Email to Customer
        if (!empty($customerEmail) && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $custSubject = "[TOP BEST GLOBAL Logistics] Xác nhận đã tiếp nhận yêu cầu báo giá: {$serviceName}";
            $custMessage = "Kính gửi " . $customerName . ",\n\n"
                . "TOP BEST GLOBAL Corporation xin chân thành cảm ơn Quý khách đã gửi yêu cầu báo giá dịch vụ {$serviceName}.\n\n"
                . "Thông tin yêu cầu của Quý khách đã được chuyển tới chuyên viên định giá phụ trách tuyến:\n"
                . "- Dịch vụ: " . $serviceName . "\n"
                . (!empty($pol) && !empty($pod) ? "- Tuyến vận chuyển: " . $pol . " -> " . $pod . "\n" : "")
                . (!empty($shipmentMode) ? "- Phương thức: " . $shipmentMode . "\n" : "")
                . "- Hotline hỗ trợ 24/7: " . (!empty($this->settings->contact_phone) ? $this->settings->contact_phone : '028 3997 1199') . "\n\n"
                . "Chuyên viên báo giá cước của TOP BEST GLOBAL sẽ liên hệ trực tiếp với Quý khách qua Số điện thoại (" . $phone . ") hoặc Email trong vòng 15 - 30 phút để cung cấp phương án vận chuyển tối ưu nhất.\n\n"
                . "Trân trọng,\n"
                . "Đội ngũ TOP BEST GLOBAL Corporation\n"
                . "Website: " . base_url();

            $this->sendEmail([
                'subject'       => $custSubject,
                'message'       => $custMessage,
                'to'            => $customerEmail,
                'template_path' => "email/email_newsletter",
                'subscriber'    => "",
            ]);
        }
    }

    //send email newsletter
    public function sendEmailNewsletter($subscriber, $subject, $message)
    {
        if (!empty($subscriber)) {
            if (empty($subscriber->token)) {
                $modelNewsletter = new NewsletterModel();
                $modelNewsletter->updateSubscriberToken($subscriber->email);
                $subscriber = $modelNewsletter->getSubscriber($subscriber->email);
            }
            $data = [
                'subject' => $subject,
                'message' => $message,
                'to' => $subscriber->email,
                'template_path' => "email/email_newsletter",
                'subscriber' => $subscriber,
            ];
            return $this->sendEmail($data);
        }
    }



    //send email
    public function sendEmail($data)
    {
        $protocol = $this->generalSettings->mail_protocol;
        if ($protocol != 'smtp' && $protocol != 'mail') {
            $protocol = 'smtp';
        }
        $encryption = $this->generalSettings->mail_encryption;
        if ($encryption != 'tls' && $encryption != 'ssl') {
            $encryption = 'tls';
        }
        if ($this->generalSettings->mail_service == 'mailjet') {
            return $this->sendEmailMailjet($data);
        } elseif ($this->generalSettings->mail_service == 'swift') {
            return $this->sendEmailSwift($encryption, $data);
        } else {
            return $this->sendEmailPHPMailer($protocol, $encryption, $data);
        }
    }

    //send email with swift mailer
    public function sendEmailSwift($encryption, $data)
    {
        try {
            // Create the Transport
            $transport = (new \Swift_SmtpTransport($this->generalSettings->mail_host, $this->generalSettings->mail_port, $encryption))
                ->setUsername($this->generalSettings->mail_username)
                ->setPassword($this->generalSettings->mail_password);
            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);
            // Create a message
            $message = (new \Swift_Message($this->generalSettings->mail_title))
                ->setFrom(array($this->generalSettings->mail_reply_to => $this->generalSettings->mail_title))
                ->setTo([$data['to'] => ''])
                ->setSubject($data['subject'])
                ->setBody(view($data['template_path'], $data), 'text/html');
            
            // Anti-spam RFC headers
            $headers = $message->getHeaders();
            $headers->addTextHeader('Precedence', 'bulk');
            $headers->addTextHeader('List-Unsubscribe', '<' . base_url('unsubscribe?email=' . urlencode($data['to'])) . '>');
            $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            $headers->addTextHeader('X-Mailer', 'TOP BEST GLOBAL Enterprise Mailer 2.4');

            //Send the message
            $result = $mailer->send($message);
            if ($result) {
                return true;
            }
        } catch (\Swift_TransportException $Ste) {
            $this->session->setFlashdata('error', $Ste->getMessage());
            return false;
        } catch (\Swift_RfcComplianceException $Ste) {
            $this->session->setFlashdata('error', $Ste->getMessage());
            return false;
        }
    }

    //send email with php mailer
    public function sendEmailPHPMailer($protocol, $encryption, $data)
    {
        $mail = new PHPMailer(true);
        try {
            if ($protocol == "mail") {
                $mail->isMail();
                $mail->setFrom($this->generalSettings->mail_reply_to, $this->generalSettings->mail_title);
                $mail->addAddress($data['to']);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $data['subject'];
                $mail->Body = view($data['template_path'], $data);
            } else {
                $mail->isSMTP();
                $mail->Host = $this->generalSettings->mail_host;
                $mail->SMTPAuth = true;
                $mail->Username = $this->generalSettings->mail_username;
                $mail->Password = $this->generalSettings->mail_password;
                $mail->SMTPSecure = $encryption;
                $mail->CharSet = 'UTF-8';
                $mail->Port = $this->generalSettings->mail_port;
                $mail->setFrom($this->generalSettings->mail_reply_to, $this->generalSettings->mail_title);
                $mail->addAddress($data['to']);
                $mail->isHTML(true);
                $mail->Subject = $data['subject'];
                $mail->Body = view($data['template_path'], $data);
            }

            // Anti-spam RFC headers
            $mail->addCustomHeader('Precedence', 'bulk');
            $mail->addCustomHeader('List-Unsubscribe', '<' . base_url('unsubscribe?email=' . urlencode($data['to'])) . '>');
            $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            $mail->addCustomHeader('X-Mailer', 'TOP BEST GLOBAL Enterprise Mailer 2.4');
            $mail->addCustomHeader('X-Auto-Response-Suppress', 'All');

            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->session->setFlashdata('error', $mail->ErrorInfo);
            return false;
        }
        return false;
    }

    //send email with Mailjet
    public function sendEmailMailjet($data)
    {
        $mj = new \Mailjet\Client($this->generalSettings->mailjet_api_key, $this->generalSettings->mailjet_secret_key, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->generalSettings->mailjet_email_address,
                        'Name' => $this->generalSettings->mail_title
                    ],
                    'To' => [
                        [
                            'Email' => $data['to'],
                            'Name' => $this->generalSettings->mail_title
                        ]
                    ],
                    'Subject' => $data['subject'],
                    'HTMLPart' => view($data['template_path'], $data)
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            return true;
        }
        return false;
    }
}