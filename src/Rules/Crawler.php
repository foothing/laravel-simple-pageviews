<?php namespace Foothing\Laravel\Visits\Rules;


use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class Crawler implements RuleInterface {

    /**
     * @var \Jaybizzle\CrawlerDetect\CrawlerDetect
     */
    protected $detector;

    public function __construct(CrawlerDetect $detector) {
        $this->detector = $detector;
    }

    public function passes(Request $request) {
        if ($this->detector->isCrawler($request->header('User-Agent'))) {
            \Log::debug("Crawler blocked");
            return false;
        }
        \Log::debug("Crawler passes");
        return true;
    }
}
