<?php namespace Foothing\Laravel\Visits;

use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Visits {

    /**
     * @var Repositories\VisitRepository
     */
    protected $visits;

    public function __construct(VisitRepository $visits) {
        $this->visits = $visits;
    }

    /**
     * Tracks this request.
     *
     * @param Request $request
     * @return bool
     */
    public function track(Request $request) {
        if (! $this->trackable($request)) {
            return false;
        }

        $visit = new Visit();
        $visit->session = $request->getSession()->getId();
        $visit->ip = $request->getClientIp();
        $visit->url = $request->path();
        $visit->date = date('YmdH');

        $this->visits->update($visit);
    }

    /**
     * Check whether this request should be tracked or not.
     *
     * @param Request $request
     * @return bool
     */
    public function trackable(Request $request) {
        if (! Config::get('visits.enabled')) {
            return false;
        }

        if (! $rules = Config::get('visits.rules')) {
            return true;
        }

        foreach ($rules as $ruleNamespace) {
            $rule = $this->makeRule($ruleNamespace);

            if (! $rule->passes($request)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Wrapper to App::make() for unit tests mocking.
     *
     * @param string $ruleNamespace
     * @return RuleInterface
     */
    public function makeRule($ruleNamespace) {
        return \App::make($ruleNamespace);
    }
}
