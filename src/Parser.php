<?php namespace Foothing\Laravel\Visits;

use Carbon\Carbon;

class Parser {

    public function parse($startOrShortcut = null, $end = null) {
        if (! $startOrShortcut) {
            $startOrShortcut = 'currentYear';
        }

        if ($startOrShortcut == 'currentWeek') {
            return [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ];
        }

        elseif ($startOrShortcut == 'currentMonth') {
            return [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ];
        }

        elseif ($startOrShortcut == 'currentYear') {
            return [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ];
        }

        elseif ($end) {
            return [
                new Carbon($startOrShortcut),
                new Carbon($end),
            ];
        }

        return [
            new Carbon($startOrShortcut),
            null
        ];
    }

}
