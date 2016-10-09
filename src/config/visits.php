<?php
return [

    // Enable or disable tracking.
    "enabled" => true,

    // Add patterns to be blacklisted (ignored and not tracked).
    // These patterns will apply if the UrlWhitelist rule is
    // enabled in the chain.
    "blacklist" => [
        // i.e. '/^(admin|api|auth).*/'
    ],

    // Rules chain.
    "rules" => [
        "Foothing\Laravel\Visits\Rules\Crawler",
        "Foothing\Laravel\Visits\Rules\UrlWhitelist",
    ],

];
