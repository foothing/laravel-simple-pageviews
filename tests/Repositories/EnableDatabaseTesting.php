<?php namespace Foothing\Tests\Laravel\Visits\Repositories;

trait EnableDatabaseTesting {

    public function setUp() {
        parent::setUp();

        $this->artisan('migrate', [
            '--database'	=>	'testing',
            '--realpath'	=> 	realpath(__DIR__.'/../../src/migrations'),
        ]);

        \DB::table('visits')->truncate();

        $this->init();
    }

    protected function getPackageProviders($app) {
        return ['Foothing\Laravel\Visits\ServiceProvider'];
    }

    protected function getPackageAliases($app) {
        return [
            'config' => 'Illuminate\Config\Repository'
        ];
    }

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('database.default', 'testing');

        $app['config']->set('database.connections.testing', [
            'driver'   	=> 'mysql',
            'host' 		=> 'localhost',
            'database' 	=> 'pageviews',
            'username'	=> 'pageviews',
            'password'	=> 'pageviews',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
