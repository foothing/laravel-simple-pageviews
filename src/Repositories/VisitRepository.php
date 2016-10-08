<?php namespace Foothing\Laravel\Visits\Repositories;

use Carbon\Carbon;
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

    // this method sucks and need refactor for sure.
    public function filterDate($start = 'today', $end = null) {

        if ($start instanceof Carbon && $end) {
            return $this->model
                ->where("date", ">=", $this->compileDate($start))
                ->where("date", "<=", $this->compileDate($end));
        }

        elseif ($start instanceof Carbon) {
            return $this->model->where("date", "like", $this->compileDate($start) . "%");
        }

        elseif ($start == 'today') {
            return $this->model->where("date", "like", date('Ymd') . "%");
        }

        elseif ($start == 'currentWeek') {
            return $this->model
                ->where("date", ">=", $this->compileDate(Carbon::now()->modify('this week')))
                ->where("date", "<=", $this->compileDate(Carbon::now()->modify('this week +6 days')));
        }

        elseif ($start == 'currentMonth') {
            return $this->model->where("date", "like", date('Ym') . "%");
        }

        elseif ($start == 'currentYear') {
            return $this->model->where("date", "like", date('Y') . "%");
        }

        else {
            return $this->model->where("date", "like", date('Ymd') . "%");
        }
    }

    public function compileDate(\DateTime $date) {
        return $date->format('Ymd');
    }

    public function aggregate($start = null, $end = null) {
        return $this->filterDate($start, $end)
            ->select('*', \DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->groupBy('url')
            ->orderBy('hits', 'desc')
            ->limit(50)
            ->get();
    }

    public function countOverallVisits() {
        return $this->model->sum('count');
    }

    public function countVisits() {
        return $this->filterDate($start = null, $end = null)
            ->select('*', \DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->where('date', 'like', date('Ymd') . "%")
            ->groupBy('day')
            ->first()
            ->hits;
    }

    public function countUniqueVisits() {
        return $this->filterDate($start = null, $end = null)
            ->select(\DB::raw('SUBSTRING(date, 1, 8) as day'))
            ->where('date', 'like', date('Ymd') . "%")
            ->groupBy('session', 'url', 'day')
            ->get()->count();
    }

    // @TODO sample size, i.e. when requested period is a year, sample is month
    public function getVisitsTrend($start = null, $end = null) {
        return $this->filterDate($start, $end)
            ->select(\DB::raw('SUBSTRING(date, 1, 8) as day'), \DB::raw('sum(count) as hits'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();
    }
}
