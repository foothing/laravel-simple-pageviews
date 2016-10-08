<?php namespace Foothing\Laravel\Visits\Rules;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UrlWhitelist implements RuleInterface {

    public function passes(Request $request) {
        if (! $blacklist = Config::get('visits.blacklist')) {
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
