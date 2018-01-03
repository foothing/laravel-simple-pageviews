<?php namespace Foothing\Tests\Laravel\Visits\Reports;

use Carbon\Carbon;
use Foothing\Laravel\Visits\DateFilter;
use Foothing\Laravel\Visits\Parser;
use Foothing\Laravel\Visits\Reports\ReportManager;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ReportManagerTest extends \Orchestra\Testbench\TestCase {

    /**
     * @var ReportManager
     */
    protected $manager;

    /**
     * @var \Mockery
     */
    protected $repository;

    /**
     * @var Parser
     */
    protected $parser;

    protected function getPackageProviders($app) {
        return ['Foothing\Laravel\Visits\ServiceProvider'];
    }

    protected function getPackageAliases($app) {
        return ['config' => 'Illuminate\Config\Repository'];
    }

    public function setUp() {
        parent::setUp();
        $this->repository = \Mockery::mock(VisitRepository::class);
        $this->parser = \Mockery::mock(Parser::class);
        $this->manager = new ReportManager($this->repository, $this->parser);
    }

    public function test_get_visits_without_args() {
        $this->parser->shouldReceive('parse')->once()->andReturn(new DateFilter('default', Carbon::now(), null));
        $this->repository->shouldReceive('aggregate')->once();
        $this->manager->getVisits();
    }

    public function test_get_visits_with_args() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn(new DateFilter('default', $now, null));
        $this->repository->shouldReceive('aggregate')->once()->with($now, null, 50);
        $this->manager->getVisits($now, null);
    }

    public function testCountOverallVisits() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn(new DateFilter('default', $now, null));
        $this->repository->shouldReceive('countOverallVisits')->once();
        $this->manager->countOverallVisits();
    }

    public function testCountUniqueVisits() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn(new DateFilter('default', $now, null));
        $this->repository->shouldReceive('countUniqueVisits')->once();
        $this->manager->countUniqueVisits();
    }

    public function testGetVisitsTrendDaily() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn(new DateFilter('default', $now, null));
        $this->repository->shouldReceive('getVisitsTrendDaily')->once();
        $this->manager->getVisitsTrendDaily();
    }

    /**
     * @dataProvider cacheKey
     */
    public function testCacheKey($result, $method, $preset, $url) {
        $this->assertEquals($result, $this->manager->cacheKey($method, $preset, $url));
    }

    public function cacheKey() {
        return [
            ["pageviews:m:p", "m", "p", null],
            ["pageviews:m:p:u", "m", "p", "u"],
            ["pageviews::", "", "", ""],
            ["pageviews:0:0", 0, 0, 0],
        ];
    }

    public function test_cache_miss() {
        $manager = \Mockery::mock("Foothing\Laravel\Visits\Reports\ReportManager[cacheGet,cacheSet]", [
            $this->repository,
            $this->parser
        ]);

        Config::shouldReceive('get')->andReturn(true);
        $manager->shouldReceive('cacheGet')->once()->andReturn(null);
        $this->repository->shouldReceive('aggregate');
        $manager->shouldReceive('cacheSet')->once();

        $manager->call("aggregate", [Carbon::now(), null], 'currentYear', 'foo/bar');
    }

    public function test_cache_hit() {
        $manager = \Mockery::mock("Foothing\Laravel\Visits\Reports\ReportManager[cacheGet,cacheSet]", [
            $this->repository,
            $this->parser
        ]);

        Config::shouldReceive('get')->andReturn(true);
        $manager->shouldReceive('cacheGet')->once()->andReturn('foo');
        $this->repository->shouldNotReceive('aggregate');
        $manager->call("aggregate", [Carbon::now(), null], 'currentYear', 'foo/bar');
    }

    public function testCacheGet_miss() {
        Cache::shouldReceive('has')->once()->andReturnNull();
        $this->assertNull($this->manager->cacheGet("a", "b", "c"));
    }

    public function testCacheGet_hit() {
        Cache::shouldReceive('has')->once()->andReturn(true);
        Cache::shouldReceive('get')->once()->andReturn("foo");
        $this->assertEquals("foo", $this->manager->cacheGet("a", "b", "c"));
    }

    public function testCacheSet() {
        Cache::shouldReceive('put')->once();
        $this->assertEquals("value", $this->manager->cacheSet("value", "a", "b", "c"));
    }

    public function test_config() {
        Config::shouldReceive('get')->once()->andReturn(false);
        Config::shouldReceive('offsetGet');
        $this->repository->shouldReceive('aggregate');
        $this->manager->call("aggregate", [Carbon::now(), null], 'currentYear', 'foo/bar');
    }

    public function tearDown() {
        \Mockery::close();
    }
}
