<?php namespace Foothing\Laravel\Visits\Rules;

use Illuminate\Http\Request;

class UrlWhitelist implements RuleInterface {

    public function passes(Request $request) {
        if (! $blacklist = config('visits.blacklist')) {
            return true;
        }

        // @TODO cache
        foreach ($blacklist as $rule) {
            if (preg_match($rule, $request->path())) {
                //\Log::debug("Blacklist: " . $request->path());
                return false;
            }
        }
        //\Log::debug("Whitelist: " . $request->path());
        return true;
    }
}
