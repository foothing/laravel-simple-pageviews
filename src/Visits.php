<?php namespace Foothing\Laravel\Visits;

use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Illuminate\Http\Request;

class Visits {

    /**
     * @var Repositories\VisitRepository
     */
    protected $visits;

    public function __construct(VisitRepository $visits) {
        $this->visits = $visits;
    }

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

    public function trackable(Request $request) {
        if (! config('visits.enabled')) {
            return false;
        }

        if (! $rules = config('visits.rules')) {
            return true;
        }

        foreach ($rules as $ruleNamespace) {
            $rule = \App::make($ruleNamespace);

            if (! $rule->passes($request)) {
                return false;
            }
        }

        return true;
    }
}
