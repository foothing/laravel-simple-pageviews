# Laravel Simple Pageviews

Tracks page views of your Laravel 5 app for traffic monitoring.

This package is meant for simple request tracking
and not for in-depth traffic analysis. Features:

- track page views
- track unique page views
- customizable whitelist rules
- url filter and crawler filter

Each log record keeps track of the user session, user ip and date.

## Setup

Install module:

`composer require foothing/laravel-simple-pageviews`

Add the service provider in `config/app.php`:

```php
'providers' => [
	Foothing\Laravel\Visits\ServiceProvider::class,
]
```

Publish migration and configuration files:

`php artisan vendor:publish provider="Foothing\Laravel\Visits\ServiceProvider --tag="config"`

`php artisan vendor:publish provider="Foothing\Laravel\Visits\ServiceProvider --tag="migrations"`

Run the migration:

`php artisan migrate`

Finally, add the middleware in your `app/Http/Kernel.php`:

```php
protected $middleware = [
	'Foothing\Laravel\Visits\Middlewares\CountPageView',
];
```

## Configure

In `config/visits.php`

```php

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

```

Rules are meant to filter requests that you don't want to track.
Default ones will filter out **crawlers** (thanks to https://github.com/JayBizzle/Crawler-Detect)
and **blacklisted urls**.

## Query methods
Read methods are still in developement. They will provide a way
to query the tracked data for analysis, chart plotting, etc.

## License
MIT
