<?php namespace Foothing\Laravel\Visits\Reports;

use Carbon\Carbon;
use Foothing\Laravel\Visits\Parser;
use Foothing\Laravel\Visits\Repositories\VisitRepository;

class ReportManager {

    /**
     * @var Repositories\VisitRepository
     */
    protected $visits;

    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(VisitRepository $visits, Parser $parser) {
        $this->visits = $visits;
        $this->parser = $parser;
    }

    /**
     * Wrapper to repository calls.
     *
     * @param       $method
     * @param array $args
     *
     * @return mixed
     */
    public function call($method, array $args) {
        return call_user_func_array([$this->visits, $method], $args);
    }

    /**
     * Return visit records.
     *
     * @param null $periodStart
     * @param null $periodEnd
     *
     * @return mixed
     */
    public function getVisits($periodStart = null, $periodEnd = null) {
        $args = $this->parser->parse($periodStart, $periodEnd);
        return $this->call('aggregate', $args);
    }

    /**
     * Return total page hits.
     *
     * @param $periodStart
     * @param $periodEnd
     * @return int
     */
    public function countOverallVisits($periodStart = null, $periodEnd = null) {
        $args = $this->parser->parse($periodStart, $periodEnd);
        return $this->call('countOverallVisits', $args);
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
        $args = $this->parser->parse($periodStart, $periodEnd);
        return $this->call('countUniqueVisits', $args);
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
        $args = $this->parser->parse($periodStart, $periodEnd);
        return $this->call('getVisitsTrend', $args);
    }
}
