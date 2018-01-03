<?php namespace Foothing\Laravel\Visits\Reports;

use Carbon\Carbon;
use Foothing\Laravel\Visits\Parser;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

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
     * @param string $preset
     * @param string $url
     *
     * @return mixed
     * @throws \Exception If repository method doesn't exist.
     */
    public function call($method, array $args, $preset, $url = null) {
        if (! method_exists($this->visits, $method)) {
            throw new \Exception("Method $method does not exists.");
        }

        if (Config::get("visits.cache")) {
            if ($preset != 'default' && $value = $this->cacheGet($method, $preset, $url)) {
                return $value;
            }

            $value = call_user_func_array([$this->visits, $method], $args);

            return $this->cacheSet($value, $method, $preset, $url);
        }

        return call_user_func_array([$this->visits, $method], $args);
    }

    /**
     * Return visit records.
     *
     * @param null $periodStart
     * @param null $periodEnd
     * @param int $limit
     *
     * @return mixed
     */
    public function getVisits($periodStart = null, $periodEnd = null, $limit = 50) {
        $filters = $this->parser->parse($periodStart, $periodEnd);

        return $this->call('aggregate', [
            $filters->start,
            $filters->end,
            $limit
        ], $filters->preset);
    }

    /**
     * Return total page hits.
     *
     * @param $periodStart
     * @param $periodEnd
     * @param string $url
     * @return int
     */
    public function countOverallVisits($periodStart = null, $periodEnd = null, $url = null) {
        $filters = $this->parser->parse($periodStart, $periodEnd);

        return $this->call('countOverallVisits', [
            $filters->start,
            $filters->end,
            $url
        ], $filters->preset, $url);
    }

    /**
     * Return unique visits in the given period.
     *
     * @param string|DateTime $periodStart
     * @param DateTime $periodEnd
     * @param string $url
     *
     * @return int
     */
    public function countUniqueVisits($periodStart = null, $periodEnd = null, $url = null) {
        $filters = $this->parser->parse($periodStart, $periodEnd);

        return $this->call('countUniqueVisits', [
            $filters->start,
            $filters->end,
            $url
        ], $filters->preset, $url);
    }

    /**
     * Return daily visits trend for the given period.
     *
     * @param string|DateTime $periodStart
     * @param DateTime $periodEnd
     * @param string $url
     *
     * @return mixed
     */
    public function getVisitsTrendDaily($periodStart = null, $periodEnd = null, $url = null) {
        $filters = $this->parser->parse($periodStart, $periodEnd);

        return $this->call('getVisitsTrendDaily', [
            $filters->start,
            $filters->end,
            $url
        ], $filters->preset, $url);
    }

    /**
     * Return monthly visits trend for the given period.
     *
     * @param string|DateTime $periodStart
     * @param DateTime $periodEnd
     * @param string $url
     *
     * @return mixed
     */
    public function getVisitsTrendMonthly($periodStart = null, $periodEnd = null, $url = null) {
        $filters = $this->parser->parse($periodStart, $periodEnd);

        return $this->call('getVisitsTrendMonthly', [
            $filters->start,
            $filters->end,
            $url
        ], $filters->preset, $url);
    }

    /**
     * Set value in cache.
     *
     * @param      $value
     * @param      $method
     * @param      $preset
     * @param null $url
     *
     * @return mixed
     */
    public function cacheSet($value, $method, $preset, $url = null) {
        $key = $this->cacheKey($method, $preset, $url);

        Cache::put($key, $value, Carbon::now()->addHour());
        \Log::debug("Cache set $key");

        return $value;
    }

    /**
     * Retrieve value from cache.
     *
     * @param      $method
     * @param      $preset
     * @param null $url
     *
     * @return null
     */
    public function cacheGet($method, $preset, $url = null) {
        $key = $this->cacheKey($method, $preset, $url);

        if (Cache::has($key)) {
            \Log::debug("Cache hit $key");
            return Cache::get($key);
        }

        return null;
    }

    /**
     * Generate cache key.
     *
     * @param      $method
     * @param      $preset
     * @param null $url
     *
     * @return string
     */
    public function cacheKey($method, $preset, $url = null) {
        if ($url) {
            return "pageviews:{$method}:{$preset}:{$url}";
        }

        return "pageviews:{$method}:{$preset}";
    }
}
