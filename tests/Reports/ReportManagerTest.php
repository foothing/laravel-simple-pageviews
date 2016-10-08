<?php namespace Foothing\Tests\Laravel\Visits\Reports;

use Foothing\Laravel\Visits\Models\Visit;
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

    public function setUp() {
        parent::setUp();
        $this->repository = \Mockery::mock(VisitRepository::class);
        $this->manager = new ReportManager($this->repository);
    }

    public function test_get_visits_without_args() {
        $this->repository->shouldReceive('aggregate')->once();
        $this->manager->getVisits();
    }

    public function test_get_visits_with_args() {
        $this->repository->shouldReceive('aggregate')->once()->with(null, null);
        $this->manager->getVisits(null, null);
    }

    public function testCountOverallVisits() {
        $this->repository->shouldReceive('countOverallVisits')->once();
        $this->manager->countOverallVisits();
    }

    public function testCountVisits() {
        $this->repository->shouldReceive('countVisits')->once();
        $this->manager->countVisits();
    }

    public function testCountUniqueVisits() {
        $this->repository->shouldReceive('countUniqueVisits')->once();
        $this->manager->countUniqueVisits();
    }
    public function testGetVisitsTrend() {
        $this->repository->shouldReceive('getVisitsTrend')->once();
        $this->manager->getVisitsTrend();
    }

    public function tearDown() {
        \Mockery::close();
    }
}
