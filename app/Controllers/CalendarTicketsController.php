<?php

namespace App\Controllers;

use App\Models\RefTypeOccurencesModel;
use App\Models\UserModel;
use App\Models\ProjectModel;
use App\Controllers\BaseController;

class CalendarTicketsController extends BaseController
{
	protected $referentiels;
    protected $userModel;
    protected $projectModel;
	public function __construct()
    {
        $this->referentiels = new RefTypeOccurencesModel();
        $this->userModel = new UserModel();
        $this->projectModel = new ProjectModel();
    }
	public function index()
    {
        mb_internal_encoding('UTF-8');

        if (!$this->user) {
            return redirect()->to('login');
        }

        $events = $this->getEvents();
        $this->view_data['events_list'] = $this->generateEventList($events);
        $this->view_data['projects'] = $this->projectModel->findAll();
        $this->view_data['salaries'] = $this->userModel->where(['status' => 'active', 'admin' => '0'])->findAll();

        return view('calendar_tickets/full', $this->view_data);
    }

	private function getEvents(): array
    {
        $query = $this->db->table('tickets')
            ->select('tickets.*, users.firstname, users.lastname')
            ->join('users', 'users.id = tickets.collaborater_id')
            ->where('collaborater_id IS NOT NULL')
            ->where('start IS NOT NULL')
            ->where('end IS NOT NULL')
            ->orderBy('users.id');

        return $query->get()->getResult();
    }

    private function generateEventList(array $events): string
    {
        $eventList = [];

        foreach ($events as $event) {
            $user = $this->getUser($event->collaborater_id);
            $project = $this->getProject($event->project_id);
            $color = $this->getColor($event->collaborater_id);
            $service = $this->getService($project->type_projet);
            $url = base_url("ctickets/view/{$event->id}");

            $eventList[] = [
                'title' => addslashes(ucwords(strtolower("{$user->lastname} {$user->firstname} - Tâche : {$event->id} - $service - {$event->subject} - Projet : N°{$project->project_num} - {$project->name}"))),
                'start' => $event->start,
                'end' => date('Y-m-d', strtotime($event->end . ' + 1 day')),
                'modal' => 'true',
                'className' => $user->classname,
                'description' => $project->name,
                'service' => $service,
                'user' => ucwords(strtolower("{$user->lastname} {$user->firstname}")),
                'color' => $color,
                'url' => $url,
            ];
        }

        return json_encode($eventList);
    }

    private function getUser(int $id)
    {
        return $this->db->table('users')->select('firstname, lastname, classname')->where('id', $id)->get()->getRow();
    }

    private function getProject(int $id)
    {
        return $this->db->table('projects')->select('name, type_projet, project_num')->where('id', $id)->get()->getRow();
    }

    private function getColor(int $id): string
    {
        return match ($id) {
            63 => '#FF0000',
            76 => '#FF00FF',
            44 => '#9acd32',
            61 => '#00ced1',
            78 => '#0000cd',
            79 => '#fa8072',
            77 => '#7b68ee',
            92 => '#33FF68',
            93 => '#FF3352',
            94 => '#E98C24',
            46 => '#00ffff',
            68 => '#808000',
            40 => '#ff8c00',
            85 => '#8b008b',
            37 => '#900C3F',
            36 => '#cd853f',
            71 => '#FF5733',
            67 => '#ff6347',
            69 => '#C70039',
            default => '#000000', // Default color if no match
        };
    }

    private function getService(string $type): string
    {
        return match ($type) {
            "96" => "MMS",
            "95" => "BIM 2D",
            "130" => "BIM 3D",
            default => "Unknown Service",
        };
    }

}