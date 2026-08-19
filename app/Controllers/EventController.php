<?php namespace App\Controllers;

use App\Models\EventModel;
use App\Models\PageModel;
use App\Models\EmailModel;

class EventController extends BaseController
{
    protected $eventModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->eventModel = new EventModel();
    }

    public function events()
    {
        $isEn = ($this->activeLang->short_form ?? 'vi') == 'en';
        $pageModel = new PageModel();
        $page = $pageModel->getPageByLang('events', $this->activeLang->id);
        
        $events = $this->eventModel->getEvents('all');

        $data = [
            'title'       => !empty($page->title) ? $page->title : ($isEn ? 'B2B Logistics Events & Seminars | TOP BEST GLOBAL' : 'Sự Kiện & Hội Thảo Xúc Tiến Thương Mại Chuỗi Cung Ứng'),
            'description' => !empty($page->description) ? $page->description : ($isEn ? 'Join exclusive logistics conferences, trade promotion expos, and B2B supply chain networking events organized by TOP BEST GLOBAL.' : 'Cập nhật lịch hội thảo chuyên đề B2B, sự kiện kết nối giao thương xuất nhập khẩu và các chương trình xúc tiến chuỗi cung ứng của TOP BEST GLOBAL.'),
            'keywords'    => 'sự kiện logistics, hội thảo xuất nhập khẩu, kết nối giao thương b2b, hội nghị chuỗi cung ứng, xúc tiến thương mại top best global',
            'page'        => $page,
            'events'      => $events,
            'userSession' => getUserSession(),
        ];

        return loadView('partials/_header', $data)
            . loadView('events', $data)
            . loadView('partials/_footer', $data);
    }

    public function eventDetail($slug)
    {
        $event = $this->eventModel->getEventBySlug($slug);
        if (empty($event)) {
            return redirect()->to(base_url('events'));
        }

        $speakers = !empty($event->speakers_json) ? json_decode($event->speakers_json, true) : [];
        $agenda = !empty($event->agenda_json) ? json_decode($event->agenda_json, true) : [];
        $otherEvents = $this->eventModel->getFeaturedUpcomingEvents(3);

        $data = [
            'title'       => esc($event->title) . ' | TOP BEST GLOBAL Events',
            'description' => esc($event->summary),
            'keywords'    => 'hội thảo ' . esc($event->title) . ', sự kiện logistics, top best global b2b networking',
            'event'       => $event,
            'speakers'    => $speakers,
            'agenda'      => $agenda,
            'otherEvents' => $otherEvents,
            'ogImage'     => $event->image,
            'userSession' => getUserSession(),
        ];

        return loadView('partials/_header', $data)
            . loadView('event_detail', $data)
            . loadView('partials/_footer', $data);
    }

    public function registerEventAjax()
    {
        $eventId    = clrNum(inputPost('event_id'));
        $name       = trim((string)inputPost('name'));
        $company    = trim((string)inputPost('company'));
        $position   = trim((string)inputPost('position'));
        $phone      = trim((string)inputPost('phone'));
        $email      = trim((string)inputPost('email'));
        $attendees  = max(1, clrNum(inputPost('attendees') ?? 1));
        $notes      = trim((string)inputPost('notes'));

        if (empty($eventId) || empty($name) || (empty($phone) && empty($email))) {
            return $this->response->setJSON([
                'status'     => 'error', 
                'message'    => 'Vui lòng điền đầy đủ Họ tên, Doanh nghiệp và Số điện thoại/Email liên hệ.',
                'csrf_token' => csrf_hash()
            ]);
        }

        $event = $this->eventModel->find($eventId) ?? $this->eventModel->getEventBySlug(inputPost('event_slug'));
        $eventTitle = $event->title ?? 'Sự Kiện B2B Logistics';

        // Increment event registered count
        $this->eventModel->incrementRegisteredCount($eventId);

        // Send Notification Email to Organizers & Confirmation Ticket to Attendee
        try {
            $emailModel = new EmailModel();
            $emailModel->sendEmailServiceRfq([
                'name'         => $name,
                'company'      => $company . (!empty($position) ? ' (' . $position . ')' : '') . ' - ' . $attendees . ' người tham dự',
                'email'        => $email,
                'phone'        => $phone,
                'service_name' => 'Đăng Ký Sự Kiện: ' . $eventTitle,
                'pol'          => $event->event_date ?? '',
                'pod'          => $event->location ?? '',
                'type'         => $attendees . ' Người tham dự',
                'message'      => $notes ?: 'Đăng ký vé tham dự sự kiện và giao thương B2B',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'EventController::registerEventAjax email error: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Đăng ký tham dự sự kiện thành công! Ban tổ chức đã gửi thông tin xác nhận và mã vé tham dự tới Email/Zalo của Quý khách.',
            'csrf_token' => csrf_hash()
        ]);
    }
}
