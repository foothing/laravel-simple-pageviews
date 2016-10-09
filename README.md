# Laravel Simple Pageviews

[![Code Climate](https://codeclimate.com/github/foothing/laravel-simple-pageviews/badges/gpa.svg)](https://codeclimate.com/github/foothing/laravel-simple-pageviews)
[![Test Coverage](https://codeclimate.com/github/foothing/laravel-simple-pageviews/badges/coverage.svg)](https://codeclimate.com/github/foothing/laravel-simple-pageviews/coverage)
[![Build Status](https://travis-ci.org/foothing/laravel-simple-pageviews.svg?branch=master)](https://travis-ci.org/foothing/laravel-simple-pageviews)

Tracks page views of your Laravel 5 app for traffic monitoring.

This package is meant for simple request tracking
and not for in-depth traffic analysis. Features:

- track page views
- track unique page views
- customizable whitelist rules
- url filter and crawler filter
- fetch data for reports

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

```

Rules are meant to filter requests that you don't want to track.
Default ones will filter out **crawlers** (thanks to https://github.com/JayBizzle/Crawler-Detect)
and **blacklisted urls**.

## Query methods
```php
$manager = app()->make("Foothing\Laravel\Visits\Reports\ReportManager");

// Get visits with url, hits and day
$manager->getVisits();

[
	{
		"url": "foo/bar",
		"day": "20161009"
		"hits": 129
	},
	{
		"url": "baz",
		"day": "20161009"
		"hits": 40
	}
]

// Return int
$manager->countOverallVisits();

// Return int
$manager->countVisits();

// Return int
$manager->countUniqueVisits();

// Return a data collection meant for chart plotting.
$manager->getVisitsTrend();

[
	{
		"day": "20161008"
		"hits": 129
	},
	{
		"day": "20161009"
		"hits": 40
	}
]
```

Each query method allows for date filtering and will
accept up to 2 arguments.

- first argument can be a *string preset*, or a `Carbon` date
- second argument, if specified, must be a `Carbon` date

Examples:
```php

// Triggers default filter
$manager->getVisits();

// String presets
$manager->getVisits('today');
$manager->getVisits('currentWeek');
$manager->getVisits('currentMonth');
$manager->getVisits('currentYear');

// Single day
$manager->getVisits(\Carbon::now());

// Period
$manager->getVisits(\Carbon::now(), Carbon::tomorrow());

```

More methods might be added later (i.e. fetch and analyze by url).

## License
MIT
