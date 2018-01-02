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

    /**
     * Applies where clause on date column.
     *
     * @param Carbon $start
     * @param Carbon $end
     *
     * @return mixed
     */
    public function filterDate(Carbon $start, Carbon $end = null) {
        if ($end) {
            return $this->model->whereBetween("date", [$start, $end]);
        }

        return $this->model->where("date", $start);
    }

    /**
     * Return the list of urls with best performance, ordered by hits.
     *
     * @param Carbon|null $start
     * @param Carbon|null $end
     * @param int  $limit
     *
     * @return mixed
     */
    public function aggregate(Carbon $start = null, Carbon $end = null, $limit = 50) {
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
     * @param Carbon|null $start
     * @param Carbon|null $end
     *
     * @return mixed
     */
    public function countOverallVisits(Carbon $start = null, Carbon $end = null) {
        return $this->filterDate($start, $end)->sum('count');
    }

    /**
     * Count records in the given period.
     *
     * @param Carbon|null $start
     * @param Carbon|null $end
     *
     * @return mixed
     */
    public function countUniqueVisits(Carbon $start = null, Carbon $end = null) {
        return $this->filterDate($start, $end)->count('count');
    }

    /**
     * Return an array of date/hits in the given period.
     * @TODO sample size, i.e. when requested period is a year, sample is month
     *
     * @param Carbon|null $start
     * @param Carbon|null   $end
     *
     * @return mixed
     */
    public function getVisitsTrend(Carbon $start = null, Carbon $end = null) {
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
