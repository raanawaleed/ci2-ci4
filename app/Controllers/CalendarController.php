<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\ProjectModel;
use App\Models\UsersModel;
use App\Models\SettingModel;

use App\Controllers\BaseController;
class CalendarController extends BaseController
{

	private  $eventModel;
	private  $projectModel;
	private  $usersModel;
	private  $settingModel;
	public function __construct()
    {
		$this->eventModel = new EventModel();
		$this->projectModel = new ProjectModel();
		$this->usersModel = new UsersModel();
		$this->settingModel = new SettingModel();

        $this->handleRedirects();
        $this->setSubmenu();
    }

	private function handleRedirects(): void
    {
        if ($this->client) {
            $link = $this->request->getCookie('fc2_link');
            return $link ? redirect()->to(str_replace("/tickets/", "/ctickets/", $link)) : redirect()->to('cprojects');
        }

        if (!$this->user) {
            return redirect()->to('login');
        }

        if (!$this->hasAccessToCalendar()) {
            return $this->handleNoAccess();
        }
    }

	private function hasAccessToCalendar(): bool
    {
        return !empty(array_filter($this->view_data['menu'], fn($item) => $item->link === "calendar"));
    }

	private function setSubmenu(): void
    {
        $this->view_data['submenu'] = [
            $this->lang->line('application_all') => 'projects/filter/all',
            $this->lang->line('application_open') => 'projects/filter/open',
            $this->lang->line('application_closed') => 'projects/filter/closed'
        ];
    }

	public function index(): void
    {
        $projects = $this->getUserProjects();

        $projectEvents = $this->generateProjectEvents($projects);
        $eventsList = $this->generateEventsList();

        $this->view_data['core_settings'] = $this->settingModel->find(['id_vcompanies' => $_SESSION['current_company']]);
        $this->view_data['project_events'] = $projectEvents;
        $this->view_data['events_list'] = $eventsList;

        $this->content_view = 'calendar/full';
    }

	private function getUserProjects(): array
    {
        if ($this->user->admin === 0) {
            $compIds = array_column($this->user->companies, 'id');

            if (!empty($compIds)) {
                $projectsByClientAdmin = $this->projectModel->whereIn('company_id', $compIds)->findAll();
                $result = array_merge($projectsByClientAdmin, $this->user->projects);
                return array_unique($result, SORT_REGULAR);
            } else {
                return $this->projectModel->hasWorker($this->user->id)->findAll();
            }
        }

        return $this->projectModel->findAll();
    }

    private function generateProjectEvents(array $projects): string
    {
        return array_reduce($projects, function ($carry, $project) {
            $descr = preg_replace("/\r|\n/", "", $project->description);
            $carry .= json_encode([
                'title' => $this->lang->line('application_project') . ": " . addslashes($project->name),
                'start' => $project->start,
                'end' => $project->end . "T23:59:00",
                'url' => base_url("projects/view/{$project->id}"),
                'className' => 'project-event',
                'description' => addslashes($descr),
            ]) . ",";
            return $carry;
        }, '');
    }

    private function generateEventsList(): string
    {
        $events = $this->eventModel->all();
        return array_reduce($events, function ($carry, $event) {
            $user = $this->usersModel->find($event->user_id);
            $event->user_id = $user;

            $carry .= json_encode([
                'title' => addslashes("{$user->firstname} -- {$event->title}"),
                'start' => $event->start,
                'end' => $event->end,
                'url' => base_url("calendar/edit_event/{$event->id}"),
                'className' => $event->classname,
                'modal' => 'true',
                'description' => addslashes(preg_replace("/\r|\n/", "", $event->description)),
            ]) . ",";
            return $carry;
        }, '');
    }

	public function create(): void
    {
        if ($this->request->getMethod() === 'post') {
            $this->createEvent();
        } else {
            $this->view_data['title'] = $this->lang->line('application_create_event');
            $this->view_data['form_action'] = 'calendar/create';
            $this->theme_view = 'modal';
            $this->content_view = 'calendar/_event';
        }
    }

	private function createEvent(): void
    {
        $data = $this->request->getPost();
        unset($data['send']);
        $data['title'] = htmlspecialchars($data['title']);
        $data['start'] = (new \DateTime($data['start']))->format('Y-m-d H:i');
        $data['end'] = (new \DateTime($data['end']))->format('Y-m-d H:i');
        $data['description'] = htmlspecialchars($data['description']);
        $data['user_id'] = $this->user->id;

        $event = $this->eventModel->create($data);
        $this->setFlashMessage($event, 'messages_create_event');
        redirect('calendar');
    }

	public function edit_event($id = null): void
    {
        if ($this->request->getMethod() === 'post') {
            $this->updateEvent($id);
        } else {
            $this->view_data['event'] = $this->eventModel->find($id);
            $this->view_data['title'] = $this->lang->line('application_update_event');
            $this->view_data['form_action'] = 'calendar/edit_event';
            $this->theme_view = 'modal';
            $this->content_view = 'calendar/_event';
        }
    }

	private function updateEvent($id): void
    {
        $data = $this->request->getPost();
        unset($data['send']);
        $event = $this->eventModel->find($data['id']);
        unset($data['id']);
        $data['title'] = htmlspecialchars($data['title']);
        $data['start'] = (new \DateTime($data['start']))->format('Y-m-d H:i');
        $data['end'] = (new \DateTime($data['end']))->format('Y-m-d H:i');
        $data['description'] = htmlspecialchars($data['description']);

        $event->update($data);
        $this->setFlashMessage($event, 'messages_update_event');
        redirect('calendar');
    }


	public function delete($id): void
    {
        $event = $this->eventModel->find($id);
        if ($event) {
            $event->delete();
            $this->setFlashMessage($event, 'messages_delete_event');
        } else {
            $this->session->setFlashdata('message', 'error:' . $this->lang->line('messages_delete_event_error'));
        }
        redirect('calendar');
    }

    private function setFlashMessage($event, string $messageKey): void
    {
        $status = $event ? 'success' : 'error';
        $this->session->setFlashdata('message', "{$status}:" . $this->lang->line($messageKey . ($status === 'error' ? '_error' : '_success')));
    }

}