<?php namespace Foothing\Laravel\Visits\Middlewares;

use Foothing\Laravel\Visits\Visits;
use Illuminate\Http\Request;

class CountPageView {

    /**
     * @var \Foothing\Laravel\Visits\Visits
     */
    protected $manager;

    public function __construct(Visits $manager) {
        $this->manager = $manager;
    }

    public function handle(Request $request, \Closure $next) {
        $this->manager->track($request);
        return $next($request);
    }
}
