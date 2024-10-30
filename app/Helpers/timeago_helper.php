<?php

use CodeIgniter\I18n\Time; // Load CodeIgniter's Time class

function old_time_ago($timeago, $short = false)
{
    // Check if the input time is a valid timestamp or needs conversion
    if (!is_numeric($timeago)) {
        $timeago = strtotime($timeago); // Convert human-readable format to timestamp
    }

    // Calculate the difference using the Time class
    $time = Time::createFromTimestamp($timeago);
    $now = Time::now();

    // Get the difference between now and the provided time
    $timespan = $time->difference($now);

    // Get the readable format for years, months, days, etc.
    $timeArray = [
        'years'   => $timespan->getYears(),
        'months'  => $timespan->getMonths(),
        'days'    => $timespan->getDays(),
        'hours'   => $timespan->getHours(),
        'minutes' => $timespan->getMinutes(),
        'seconds' => $timespan->getSeconds()
    ];

    $timePieces = [];

    // Build a readable timespan
    foreach ($timeArray as $key => $value) {
        if ($value > 0) {
            $timePieces[] = "$value $key";
        }
    }

    // Get the first timespan part
    $return = $timePieces[0];

    // If there's more than one time part and short mode is disabled, add the second part
    if (isset($timePieces[1]) && !$short) {
        $return .= ' ' . lang('application.and') . ' ' . $timePieces[1];
    }

    // Append the "ago" text
    $return .= ' ' . lang('application.ago');

    return $return;
}

function time_ago($time_ago)
{
    // Use Time class for easier time manipulation
    $time = Time::parse($time_ago);
    $now = Time::now();

    // Calculate the difference between now and the provided time
    $timeElapsed = $time->difference($now);

    // Check and return the appropriate string based on time difference
    if ($timeElapsed->getSeconds() <= 60) {
        return "a l'instant";
    } elseif ($timeElapsed->getMinutes() <= 60) {
        return $timeElapsed->getMinutes() == 1 ? "1 min plus tôt" : "{$timeElapsed->getMinutes()} minutes plus tôt";
    } elseif ($timeElapsed->getHours() <= 24) {
        return $timeElapsed->getHours() == 1 ? "1 heure plus tôt" : "{$timeElapsed->getHours()} hrs plus tôt";
    } elseif ($timeElapsed->getDays() <= 7) {
        return $timeElapsed->getDays() == 1 ? "Hier" : "{$timeElapsed->getDays()} jours plus tôt";
    } elseif ($timeElapsed->getWeeks() <= 4.3) {
        return $timeElapsed->getWeeks() == 1 ? "1 semaine plus tôt" : "{$timeElapsed->getWeeks()} semaines plus tôt";
    } elseif ($timeElapsed->getMonths() <= 12) {
        return $timeElapsed->getMonths() == 1 ? "1 mois plus tôt" : "{$timeElapsed->getMonths()} mois plus tôt";
    } else {
        return $timeElapsed->getYears() == 1 ? "1 année plus tôt" : "{$timeElapsed->getYears()} années plus tôt";
    }
}
