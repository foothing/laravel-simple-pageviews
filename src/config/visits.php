<?php
return [

    // Enable or disable tracking.
    "enabled" => true,

    // Add patterns to be blacklisted (ignored and not tracked).
    "blacklist" => [
        // i.e. '/^(admin|api|auth).*/'
    ],

    // Rules chain.
    "rules" => [
        "Foothing\Laravel\Visits\Rules\Crawler",
        "Foothing\Laravel\Visits\Rules\UrlWhitelist",
    ],

];
