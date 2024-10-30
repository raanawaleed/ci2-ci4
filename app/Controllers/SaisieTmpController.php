<?php

namespace App\Controllers;

use App\Models\RefTypeModel;
use App\Models\RefTypeOccurencesModel;
use App\Models\UserModel;
use App\Controllers\BaseController;

class SaisieTmpController extends BaseController
{
    private string $formatDate = 'm-Y';

    private array $optionsMois = [], $view_data = [], $joursTravailMois = [];
    private ?string $moisCourant = null;

    private RefTypeModel $refType;
    private RefTypeOccurencesModel $referentiels;
    private UserModel $userModel;

    public function __construct()
    {
        $this->refType = new RefTypeModel();
        $this->referentiels = new RefTypeOccurencesModel();
        $this->userModel = new UserModel();

        // Check authentication
        if (!$this->client && !$this->user) {
            return redirect()->to('login');
        }

        $this->initializeSubmenus();
        $this->initializeTicketTypes();
    }

    private function initializeSubmenus(): void
    {
        $idType = $this->refType->getRefTypeByName("ticket")->id;
        $submenus = $this->referentiels->getReferentielsByIdType($idType);

        foreach ($submenus as $submenu) {
            $this->view_data['submenu'][$this->lang->line('application_' . $submenu->name)] = 'ctickets/filter/' . $submenu->id;
        }
    }

    private function initializeTicketTypes(): void
    {
        $typeId = config('App\Config')->type_id_type_ticket; // Adjusted for CI 4.x config access
        $this->type_ticket_projet = $this->referentiels->getReferentiels($typeId, config('App\Config')->ticket_projet);
        $this->type_ticket_defaut = $this->referentiels->getReferentiels($typeId, config('App\Config')->ticket_par_defaut);

        $this->type_ticket_projet->alias = 'P';
        $this->type_ticket_defaut->alias = 'D';
    }

    public function index(?int $mois = null, ?int $annee = null): void
    {
        $this->getData(true, $mois, $annee);
    }

    private function getData(bool $is_saisie, ?int $mois = null, ?int $annee = null, $xuser = null): void
    {
        $idadmin = $this->user->id;

        if ($idadmin === 1) {
            $this->content_view = 'saisietemps/vi2';
        } else {
            $ids = $this->user->salaries_id;
            $this->view_data['data'] = $this->userModel->idsal($ids);
            $this->content_view = 'saisietemps/vi';
        }
    }
}