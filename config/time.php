<?php

use Illuminate\Support\Carbon;

$currentTimeAndDate = Carbon::now();

return [
    'current' => $currentTimeAndDate,
    'currentYear' => $currentTimeAndDate->translatedFormat('Y'),
    'currentTime' => $currentTimeAndDate->translatedFormat('g:i'),
    'currentDate' => $currentTimeAndDate->translatedFormat('j F Y'),
    'currentDateAndTime' => $currentTimeAndDate->translatedFormat('g:i jS F Y'),
];
