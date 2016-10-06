<?php namespace Foothing\Laravel\Visits;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register() {

    }

    public function boot() {
        $this->publishes([
            __DIR__ . '/config/visits.php' => config_path('visits.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
