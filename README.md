# Laravel Simple Pageviews

[![Code Climate](https://codeclimate.com/github/foothing/laravel-simple-pageviews/badges/gpa.svg)](https://codeclimate.com/github/foothing/laravel-simple-pageviews)
[![Test Coverage](https://codeclimate.com/github/foothing/laravel-simple-pageviews/badges/coverage.svg)](https://codeclimate.com/github/foothing/laravel-simple-pageviews/coverage)
[![Build Status](https://travis-ci.org/foothing/laravel-simple-pageviews.svg?branch=master)](https://travis-ci.org/foothing/laravel-simple-pageviews)

Tracks page views of your Laravel 5.x app for traffic monitoring.

To date, it has been tested with Laravel up to 5.5 and PHP 7.

> **IMPORTANT** If you are upgrading from previous 0.x version please [read the release notes](https://github.com/foothing/laravel-simple-pageviews/releases/tag/1.0.0).

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

// Get visits with url and hits. This should be used to
// get an overview of the best performer urls.
$manager->getVisits();

[
	{
		"url": "foo/bar",
		"hits": 129
	},
	{
		"url": "baz",
		"hits": 40
	}
]

// Return int
$manager->countOverallVisits();
$manager->countOverallVisits('today', 'tomorrow', 'foo/bar');

// Return int
$manager->countUniqueVisits('currentWeek');
$manager->countUniqueVisits('currentWeek', null, 'foo/bar');

// Return a data collection meant for chart plotting.
$manager->getVisitsTrendDaily();

[
	{
		"day": "2016-10-08"
		"hits": 129
	},
	{
		"day": "2016-10-09"
		"hits": 40
	}
]
```

Each query method allows for date filtering and will
accept up to 3 arguments.

- first argument can be a *string preset*, a string that `Carbon` can parse or a `Carbon` date
- second argument as the first one
- third argument can be an url

Only exception is the `aggregate()` method, which will accept a `limit` argument.

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

## Performances
Performances have been tested in a ~5 million records database with good results.
However, i recommend to partition your database tables if size grows nasty, i.e. 1 milion record
per partition. Also, a good practice would be to tune your partition to be consistent with
date periods (i.e. full year in single partition, single quarter in single partition, etc.) according
to your traffic and report type.

## License
MIT
