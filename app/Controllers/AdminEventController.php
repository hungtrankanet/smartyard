<?php namespace App\Controllers;

use App\Models\EventModel;

class AdminEventController extends BaseAdminController
{
    protected $eventModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->eventModel = new EventModel();
    }

    public function events()
    {
        checkPermission('events');
        $data['title'] = "Quản Lý Sự Kiện & Hội Thảo B2B";
        $status = inputGet('status') ?: 'all';
        $data['status'] = $status;
        $data['events'] = $this->eventModel->getEvents($status, 100);

        echo view('admin/includes/_header', $data);
        echo view('admin/event/events', $data);
        echo view('admin/includes/_footer');
    }

    public function addEvent()
    {
        checkPermission('events');
        $data['title'] = "Thêm Sự Kiện / Hội Thảo Mới";

        echo view('admin/includes/_header', $data);
        echo view('admin/event/add_event', $data);
        echo view('admin/includes/_footer');
    }

    public function addEventPost()
    {
        checkPermission('events');
        $title = trim(inputPost('title'));
        $eventDate = trim(inputPost('event_date'));
        $eventTime = trim(inputPost('event_time'));
        $location = trim(inputPost('location'));

        if (empty($title) || empty($eventDate) || empty($location)) {
            setErrorMessage("Vui lòng điền đầy đủ Tiêu đề, Ngày diễn ra và Địa điểm sự kiện.");
            return redirect()->back();
        }

        $image = inputPost('image_url');
        $file = $this->request->getFile('image_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/images', $newName);
            $image = base_url('uploads/images/' . $newName);
        }

        $eventData = [
            'title' => $title,
            'slug' => inputPost('slug') ?: strSlug($title),
            'summary' => inputPost('summary'),
            'content' => inputPost('content'),
            'event_date' => $eventDate,
            'event_time' => $eventTime ?: '08:30 - 11:30',
            'location' => $location,
            'location_type' => inputPost('location_type') ?: 'offline',
            'organizer' => inputPost('organizer') ?: 'TOP BEST GLOBAL Business Alliance',
            'fee' => inputPost('fee') ?: 'Miễn phí cho Đối tác TOP BEST GLOBAL',
            'max_seats' => (int)inputPost('max_seats') ?: 200,
            'registered_count' => 0,
            'speakers_json' => $this->buildSpeakersJson(),
            'agenda_json' => $this->buildAgendaJson(),
            'image' => $image,
            'status' => inputPost('status') ?: 'upcoming',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $eventId = $this->eventModel->addEvent($eventData);
        setSuccessMessage("Thêm sự kiện thành công.");
        return redirect()->to(adminUrl('events'));
    }

    public function editEvent($id)
    {
        checkPermission('events');
        $event = $this->eventModel->getEventById($id);
        if (!$event) {
            setErrorMessage("Không tìm thấy sự kiện.");
            return redirect()->to(adminUrl('events'));
        }

        $data['title'] = "Chỉnh Sửa Sự Kiện: " . esc($event->title);
        $data['event'] = $event;
        $data['speakers'] = !empty($event->speakers_json) ? json_decode($event->speakers_json) : [];
        $data['agenda'] = !empty($event->agenda_json) ? json_decode($event->agenda_json) : [];

        echo view('admin/includes/_header', $data);
        echo view('admin/event/edit_event', $data);
        echo view('admin/includes/_footer');
    }

    public function editEventPost()
    {
        checkPermission('events');
        $id = inputPost('id');
        $event = $this->eventModel->getEventById($id);
        if (!$event) {
            setErrorMessage("Không tìm thấy sự kiện.");
            return redirect()->to(adminUrl('events'));
        }

        $title = trim(inputPost('title'));
        $eventDate = trim(inputPost('event_date'));
        $location = trim(inputPost('location'));

        if (empty($title) || empty($eventDate) || empty($location)) {
            setErrorMessage("Vui lòng điền đầy đủ Tiêu đề, Ngày diễn ra và Địa điểm.");
            return redirect()->back();
        }

        $image = inputPost('image_url') ?: $event->image;
        $file = $this->request->getFile('image_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/images', $newName);
            $image = base_url('uploads/images/' . $newName);
        }

        $eventData = [
            'title' => $title,
            'slug' => inputPost('slug') ?: strSlug($title),
            'summary' => inputPost('summary'),
            'content' => inputPost('content'),
            'event_date' => $eventDate,
            'event_time' => inputPost('event_time') ?: $event->event_time,
            'location' => $location,
            'location_type' => inputPost('location_type') ?: 'offline',
            'organizer' => inputPost('organizer') ?: $event->organizer,
            'fee' => inputPost('fee') ?: $event->fee,
            'max_seats' => (int)inputPost('max_seats') ?: 200,
            'speakers_json' => $this->buildSpeakersJson(),
            'agenda_json' => $this->buildAgendaJson(),
            'image' => $image,
            'status' => inputPost('status') ?: 'upcoming'
        ];

        $this->eventModel->editEvent($id, $eventData);
        setSuccessMessage("Cập nhật sự kiện thành công.");
        return redirect()->to(adminUrl('events'));
    }

    public function deleteEventPost()
    {
        checkPermission('events');
        $id = inputPost('id');
        $this->eventModel->deleteEvent($id);
        setSuccessMessage("Xóa sự kiện thành công.");
        return redirect()->to(adminUrl('events'));
    }

    public function registrations($eventId = null)
    {
        checkPermission('events');
        $data['title'] = "Danh Sách Đăng Ký Tham Gia Sự Kiện";
        $data['selectedEventId'] = $eventId;
        $data['events'] = $this->eventModel->getEvents('all', 100);
        $data['registrations'] = $this->eventModel->getRegistrations($eventId, 200);

        echo view('admin/includes/_header', $data);
        echo view('admin/event/registrations', $data);
        echo view('admin/includes/_footer');
    }

    public function deleteRegistrationPost()
    {
        checkPermission('events');
        $id = inputPost('id');
        $this->eventModel->deleteRegistration($id);
        setSuccessMessage("Xóa đăng ký thành công.");
        return redirect()->back();
    }

    private function buildSpeakersJson()
    {
        $speakers = [];
        $names = $this->request->getPost('speaker_names') ?: [];
        $titles = $this->request->getPost('speaker_titles') ?: [];
        $companies = $this->request->getPost('speaker_companies') ?: [];
        if (is_array($names)) {
            for ($i = 0; $i < count($names); $i++) {
                $name = trim($names[$i] ?? '');
                if (!empty($name)) {
                    $speakers[] = [
                        'name' => $name,
                        'title' => trim($titles[$i] ?? ''),
                        'company' => trim($companies[$i] ?? '')
                    ];
                }
            }
        }
        return json_encode($speakers, JSON_UNESCAPED_UNICODE);
    }

    private function buildAgendaJson()
    {
        $agenda = [];
        $times = $this->request->getPost('agenda_times') ?: [];
        $titles = $this->request->getPost('agenda_titles') ?: [];
        $speakers = $this->request->getPost('agenda_speakers') ?: [];
        if (is_array($times)) {
            for ($i = 0; $i < count($times); $i++) {
                $time = trim($times[$i] ?? '');
                $title = trim($titles[$i] ?? '');
                if (!empty($time) || !empty($title)) {
                    $agenda[] = [
                        'time' => $time,
                        'title' => $title,
                        'speaker' => trim($speakers[$i] ?? '')
                    ];
                }
            }
        }
        return json_encode($agenda, JSON_UNESCAPED_UNICODE);
    }
}
