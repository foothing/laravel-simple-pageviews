<?php namespace Foothing\Laravel\Visits\Rules;

use Illuminate\Http\Request;

interface RuleInterface {

    public function passes(Request $request);

}