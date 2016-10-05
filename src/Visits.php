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
        $visit = new Visit();
        $visit->session = $request->getSession()->getId();
        $visit->ip = $request->getClientIp();
        $visit->url = $request->getPath();
        $visit->date = date('YmdH');

        $this->visits->update($visit);
    }

}
