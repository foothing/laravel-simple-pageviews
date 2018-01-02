<?php namespace Foothing\Laravel\Visits\Reports;

use Foothing\Laravel\Visits\Repositories\VisitRepository;

class ReportManager {

    /**
     * @var Repositories\VisitRepository
     */
    protected $visits;

    public function __construct(VisitRepository $visits) {
        $this->visits = $visits;
    }

    /**
     * Return all the visit records.
     *
     * @param null $periodStart
     * @param null $periodEnd
     *
     * @return mixed
     */
    public function getVisits($periodStart = null, $periodEnd = null) {
        return $this->visits->aggregate($periodStart, $periodEnd);
    }

    /**
     * Return total page hits.
     *
     * @param $periodStart
     * @param $periodEnd
     * @return int
     */
    public function countOverallVisits($periodStart = null, $periodEnd = null) {
        return $this->visits->countOverallVisits($periodStart, $periodEnd);
    }

    /**
     * Return unique visits in the given period.
     *
     * @param string|DateTime $periodStart
     * @param DateTime $periodEnd
     *
     * @return int
     */
    public function countUniqueVisits($periodStart = null, $periodEnd = null) {
        return $this->visits->countUniqueVisits($periodStart, $periodEnd);
    }

    /**
     * Return visits trend for the given period.
     *
     * @param string|DateTime $periodStart
     * @param DateTime $periodEnd
     *
     * @return mixed
     */
    public function getVisitsTrend($periodStart = null, $periodEnd = null) {
        return $this->visits->getVisitsTrend($periodStart, $periodEnd);
    }
}
