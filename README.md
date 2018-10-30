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

`php artisan vendor:publish --provider="Foothing\Laravel\Visits\ServiceProvider" --tag="config"`

`php artisan vendor:publish --provider="Foothing\Laravel\Visits\ServiceProvider" --tag="migrations"`

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

## The input buffer
This package has been tested in a moderate traffic website, like ~20k pageviews / day
which makes about 17k database records per day. The visits table will grow up pretty
quick and the database might suffer performance issues when it comes to execute an
`insert or update` statement on a table which count hundred thousands (or millions)
rows.

For this reason an insert/update buffer has been added. Basically, each visit is tracked
in a temporary table that is only used on write operations, while the report and read
operations are performed on a separate table.

An `artisan` command has been added to handle the periodic data dump from the
write table to the read table. A good practice might be dumping data each day.

TL;DR configure as follows in your `app/Console/Kernel.php` (please refer to Laravel docs for scheduling info):
```php
/**
 * The Artisan commands provided by your application.
 *
 * @var array
 */
protected $commands = [
	'Foothing\Laravel\Visits\Commands\DumpVisitsBuffer',
];

/**
 * Define the application's command schedule.
 *
 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
 * @return void
 */
protected function schedule(Schedule $schedule)
{
	// Adjust this with your needs.
	$schedule->command('visits:buffer')->dailyAt("00:00");
}
```


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
