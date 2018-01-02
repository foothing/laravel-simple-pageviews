<?php namespace Foothing\Tests\Laravel\Visits\Reports;

use Carbon\Carbon;
use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Parser;
use Foothing\Laravel\Visits\Reports\ReportManager;
use Foothing\Laravel\Visits\Repositories\VisitRepository;

class ReportManagerTest extends \PHPUnit_Framework_TestCase {

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

    public function setUp() {
        parent::setUp();
        $this->repository = \Mockery::mock(VisitRepository::class);
        $this->parser = \Mockery::mock(Parser::class);
        $this->manager = new ReportManager($this->repository, $this->parser);
    }

    public function test_get_visits_without_args() {
        $this->parser->shouldReceive('parse')->once()->andReturn([Carbon::now(), null]);
        $this->repository->shouldReceive('aggregate')->once();
        $this->manager->getVisits();
    }

    public function test_get_visits_with_args() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn([$now, null]);
        $this->repository->shouldReceive('aggregate')->once()->with($now, null, 50);
        $this->manager->getVisits($now, null);
    }

    public function testCountOverallVisits() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn([$now, null]);
        $this->repository->shouldReceive('countOverallVisits')->once();
        $this->manager->countOverallVisits();
    }

    public function testCountUniqueVisits() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn([$now, null]);
        $this->repository->shouldReceive('countUniqueVisits')->once();
        $this->manager->countUniqueVisits();
    }
    public function testGetVisitsTrendDaily() {
        $now = Carbon::now();
        $this->parser->shouldReceive('parse')->once()->andReturn([$now, null]);
        $this->repository->shouldReceive('getVisitsTrendDaily')->once();
        $this->manager->getVisitsTrendDaily();
    }

    public function tearDown() {
        \Mockery::close();
    }
}
