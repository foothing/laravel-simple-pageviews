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

    // @TODO date filters

    public function countVisits() {
        return $this->model->where('date', 'like', date('Ymd') . "%")->groupBy('date')->sum('count');
    }

    public function countUniqueVisits() {
        return $this->model->where('date', 'like', date('Ymd') . "%")->groupBy('date')->count();
    }
}
