<?php

namespace App\Helpers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Services;



if (!function_exists('calcul_heure')) {
    function calcul_heure(array $subj): array
    {
        $Heures = [];

        foreach ($subj as $content) {
            $heures_end = strtotime($content['end']);
            $heures_start = strtotime($content['start']);
            $nbJoursTimestamp = $heures_end - $heures_start;

            // Assuming an 8-hour workday
            $Heures[] = ($nbJoursTimestamp / 86400) * 8;
        }

        return $Heures;
    }
}
