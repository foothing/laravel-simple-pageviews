<?php namespace Foothing\Laravel\Visits;

use Carbon\Carbon;

class Parser {

    public function parse($startOrShortcut = null, $end = null) {
        if (! $startOrShortcut) {
            $startOrShortcut = 'currentYear';
        }

        if ($startOrShortcut == 'currentWeek') {
            return new DateFilter(
                $startOrShortcut,
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            );
            return [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ];
        }

        elseif ($startOrShortcut == 'currentMonth') {
            return new DateFilter(
                $startOrShortcut,
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            );
            return [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ];
        }

        elseif ($startOrShortcut == 'currentYear') {
            return new DateFilter(
                $startOrShortcut,
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            );
            return [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ];
        }

        elseif ($end) {
            return new DateFilter(
                'default',
                new Carbon($startOrShortcut),
                new Carbon($end)
            );
            return [
                new Carbon($startOrShortcut),
                new Carbon($end),
            ];
        }

        return new DateFilter(
            'default',
            new Carbon($startOrShortcut),
            null
        );

        return [
            new Carbon($startOrShortcut),
            null
        ];
    }

}
