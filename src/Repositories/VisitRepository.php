<?php namespace Foothing\Laravel\Visits\Repositories;

use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Repository\Eloquent\EloquentRepository;

class VisitRepository extends EloquentRepository {

    public function __construct(Visit $visit) {
        parent::__construct($visit);
    }

    public function update($visit) {
        $unique = [
            'session' => $visit->session,
            'ip' => $visit->ip,
            'url' => $visit->url,
            'date' => $visit->date
        ];

        $values = [
            'count' => \DB::raw('count+1')
        ];

        return $this->model->updateOrCreate($unique, $values);
    }

    // @TODO date filters, the method belows needs filtering
    // and they are only placeholders yet.

    public function aggregate() {
        return $this->model
            ->select('*', \DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->where('date', 'like', date('Ymd') . "%")
            ->groupBy('url', 'day')
            ->orderBy('hits', 'desc')
            ->limit(50)
            ->get();
    }

    public function countVisits() {
        return $this->model
            ->select('*', \DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->where('date', 'like', date('Ymd') . "%")
            ->groupBy('day')
            ->first()
            ->hits;
    }

    public function countUniqueVisits() {
        return $this->model
            ->select(\DB::raw('SUBSTRING(date, 1, 8) as day'))
            ->where('date', 'like', date('Ymd') . "%")
            ->groupBy('session', 'url', 'day')
            ->get()->count();
    }

    public function getVisitsSerie() {
        return $this->model
            ->select(\DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }
}
