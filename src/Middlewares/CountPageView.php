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
        //\Log::debug('referer ' . $request->server('HTTP_REFERER'));

        // @TODO bots, whitelist rules.

        if ($this->whitelisted($request) && config('visits.enabled')) {
            $this->manager->track($request);
        }

        return $next($request);
    }

    protected function whitelisted(Request $request) {

        // @TODO config whitelist

        if (preg_match('/^(api|assets|src|admin|account\/permissions|drupal).*/', $request->path())) {
            //\Log::debug("Blacklist: " . $request->path());
            return false;
        }
        //\Log::debug("Whitelisted: " . $request->path());
        return true;
    }

}