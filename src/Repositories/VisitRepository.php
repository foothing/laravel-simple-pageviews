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

        // Not using $this->model here, since we need
        // to operate on the buffer table.
        return $visit->updateOrCreate($unique, $values);
    }

    // this method sucks and need refactor for sure.
    public function filterDate($start = 'today', $end = null) {
        // Prevent full-table scan.
        if (! $start) {
            $start = 'currentYear';
        }

        if (! $end instanceof Carbon) {
            $end = new Carbon($end);
        }

        if ($start instanceof Carbon && $end) {
            return $this->model->whereBetween("date", [$start, $end]);
        }

        elseif ($start instanceof Carbon) {
            return $this->model->where("date", $start);
        }

        elseif ($start == 'currentWeek') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();

            return $this->model->whereBetween("date", [$start, $end]);
        }

        elseif ($start == 'currentMonth') {
            // Use date boundaries instead of
            // SQL month() function, to avoid
            // full table scan.
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();

            return $this->model->whereBetween('date', [$start, $end]);
        }

        elseif ($start == 'currentYear') {
            // Use date boundaries instead of
            // SQL month() function, to avoid
            // full table scan.
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();

            return $this->model->whereBetween('date', [$start, $end]);
        }

        elseif ($start) {
            // Try the Carbon parser for strings like
            // today, tomorrow, yesterday, etc.
            return $this->model->where("date", new Carbon($start));
        }

        return $this->model;
    }

    /**
     * Return the list of urls with best performance, ordered by hits.
     *
     * @param null $start
     * @param null $end
     * @param int  $limit
     *
     * @return mixed
     */
    public function aggregate($start = null, $end = null, $limit = 50) {
        return $this->filterDate($start, $end)
            ->select('url', \DB::raw('sum(count) as hits'))
            ->groupBy('url')
            ->orderBy('hits', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Sum hits in the given period.
     *
     * @param null $start
     * @param null $end
     *
     * @return mixed
     */
    public function countOverallVisits($start = null, $end = null) {
        return $this->filterDate($start, $end)->sum('count');
    }

    /**
     * Count records in the given period.
     *
     * @param null $start
     * @param null $end
     *
     * @return mixed
     */
    public function countUniqueVisits($start = null, $end = null) {
        return $this->filterDate($start, $end)->count('count');
    }

    /**
     * Return an array of date/hits in the given period.
     * @TODO sample size, i.e. when requested period is a year, sample is month
     *
     * @param string $start
     * @param null   $end
     *
     * @return mixed
     */
    public function getVisitsTrend($start = 'currentWeek', $end = null) {
        return $this->filterDate($start, $end)
            ->select('date', \DB::raw('sum(count) as hits'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function dump() {
        \DB::transaction(function(){
            $this->executeDump();
        });
    }

    public function executeDump() {
        // Dump records.
        foreach (\DB::table('visits_buffer')->get() as $bufferRecord) {
            $visit = new Visit((array)$bufferRecord);
            $visit->save();
        }

        // Cleanup buffer.
        \DB::table('visits_buffer')->truncate();
    }
}
