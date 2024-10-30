<?php

namespace App\Controllers;

use App\Models\PaieModel;
use CodeIgniter\Controller;
use App\Libraries\PDF; // Adjust this line based on your PDF library

class GestionPaieController extends Controller
{
    protected $paieModel;

    private array $PARAM_IRPP = [
        'limite_50k' => ['quota' => 50000, 'quota_produit' => 0.35, 'quota_somme' => 13100],
        'limite_30k' => ['quota' => 30000, 'quota_produit' => 0.32, 'quota_somme' => 6700],
        'limite_20k' => ['quota' => 20000, 'quota_produit' => 0.28, 'quota_somme' => 3900],
        'limite_5k' => ['quota' => 5000, 'quota_produit' => 0.26, 'quota_somme' => 0],
        'limite_0k' => ['quota' => 0, 'quota_produit' => 0, 'quota_somme' => 0]
    ];

    public function __construct()
    {
        $this->paieModel = new PaieModel();

        if (!session()->has('user_id')) {
            return redirect('login');
        }
    }

    public function index()
    {
        $annee = $this->request->getPost('annee') ?? date("Y");
        $data['paies'] = $this->paieModel->paieAnnee($annee);
        $data['libelleMois'] = libelleMois();
        $data['annee'] = $annee;
        $data['selectYears'] = range(2017, 2021);
        $data['backlink'] = "gestionpaie/paiemois/";
        return view('rhpaie/paie/viewpaie', $data);
    }

    public function paiemois()
    {
        $data['title'] = "Choisir le mois de la paie";
        $data['form_action'] = 'gestionpaie/creerpaie/';
        $data['libelleMonth'] = libelleMois();
        $data['selectYears'] = range(2017, 2021);
        $data['annee'] = date("Y");
        $data['mois'] = date("m");

        return view('rhpaie/paie/selectmois', $data);
    }

    public function afficher($paie_calcule_avant, $mois, $annee)
    {
        if (!in_array($paie_calcule_avant, [0, 1])) {
            return redirect()->to('404');
        }

        $data['paie_calcule_avant'] = $paie_calcule_avant;
        $data['paies'] = $this->paieModel->recupPaie("$annee-$mois-01 00:00:00");
        $data['annee'] = $annee;
        $data['mois'] = $mois;
        $data['libelleMois'] = libelleMois();
        $data['selectYears'] = range(2017, 2021);
        $data['backlink'] = "gestionpaie/paiemois/";

        return view("rhpaie/paie/listepaies", $data);
    }

    public function creerpaie()
    {
        $mois = $this->request->getPost('mois');
        $annee = $this->request->getPost('annee');

        $date_paie = "$annee-$mois-01 00:00:00";
        $paies = $this->paieModel->recupPaie($date_paie);

        if (count($paies) > 0) {
            return redirect("gestionpaie/afficher/1/$mois/$annee");
        } else {
            $this->calculerpaie($annee, $mois);
        }
    }

    public function calculerpaie($annee, $mois)
    {
        $date_paie = "$annee-$mois-01 00:00:00";

        $this->paieModel->deletePaie($date_paie);

        $cnss = $this->paieModel->tauxCnss();
        if (!$cnss) {
            session()->setFlashdata('message', 'error:Veuillez paramétrer le taux CNSS');
            return redirect("gestionpaie");
        }

        $date_creation = date("Y-m-d H:i:s");
        $this->paieModel->creerPaie($date_paie, $date_creation, $date_creation, session()->get('user_id'), $cnss);

        $paies = $this->paieModel->recupPaie($date_paie);
        $param_paie = $this->paieModel->recupParamPaie()[0];

        foreach ($paies as $paie) {
            $this->calculer_net($paie, $param_paie, $mois);
        }

        return redirect("gestionpaie/afficher/0/$mois/$annee");
    }

    public function genererFdp($mois = null, $annee = null)
    {
        $fdp = $this->request->getPost('fdp') ?? [];

        if (empty($fdp)) {
            session()->setFlashdata('message', 'error:Veuillez cocher des éléments du tableau.');
            return redirect("gestionpaie/creerpaie/$mois/$annee");
        }

        $data['paies'] = $this->paieModel->recupPaie(null, $fdp);

        if (count($data['paies']) > 0) {
            $data['core_settings'] = Setting::find(['id_vcompanies' => session()->get('current_company')]);
            $data['employeur'] = $this->paieModel->getEmployeur(session()->get('current_company'));
            $html = view($data["core_settings"]->template . '/rhpaie/paie/_fichepaie', $data);
            return $this->generatePDF($html, "Fiches de paies - $mois-$annee-" . time());
        } else {
            session()->setFlashdata('message', 'error:Aucune paie n\'a été trouvée pour ' . $mois . '-' . $annee);
            return redirect("gestionpaie");
        }
    }

    public function calculer_irpp($paie, $param_paie, $mois)
    {
        $v1 = $paie->salaire_imposable * 12 * 0.9;
        $v2 = ($paie->salaire_imposable * 12) - 2000;
        $base_irpp = max($v1, $v2);

        $prime_chef_famille = $paie->chef_famille == '1' ? $param_paie->prime_marie : 0;
        $nbenfants = match ($paie->nb_enfants) {
            0 => $param_paie->prime_zero_enfant,
            1 => $param_paie->prime_un_enfant,
            2 => $param_paie->prime_deux_enfant,
            3 => $param_paie->prime_trois_enfant,
            default => $param_paie->prime_quatre_plus_enfant,
        };

        return $base_irpp - $prime_chef_famille - $nbenfants;
    }

    private function generatePDF(string $html, string $filename)
    {
        $pdf = new PDF();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        return $pdf->stream($filename);
    }
}
