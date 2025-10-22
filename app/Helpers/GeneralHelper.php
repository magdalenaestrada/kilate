<?php

namespace App\Helpers;

use Carbon\Carbon;

class GeneralHelper
{
    public static function getPeridOfDateYear($start, $end): array
    {
        $fechaInicio = ($start instanceof Carbon) ? $start : Carbon::parse($start);
        $fechaFin = ($end instanceof Carbon) ? $end : Carbon::parse($end);
        $years = [];
        for ($date = $fechaInicio; $date->lte($fechaFin); $date->addYear()) {
            $years[] = $date->year;
        }
        return $years;
    }
    public static function getMonthsEs(): array
    {
        return array_map(fn($month) => Carbon::create(null, $month)->translatedFormat('F'), range(1, 12));
    }
}
