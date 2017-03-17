<?php namespace Foothing\Tests\Laravel\Visits;

use Foothing\Laravel\Visits\Repositories\VisitBufferRepository;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Foothing\Laravel\Visits\Visits;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Config;

class VisitsTest extends \PHPUnit_Framework_TestCase {

    protected $request;

    /**
     * @var \Mockery
     */
    protected $repository;

    /**
     * @var Visits
     */
    protected $visits;

    public function setUp() {
        parent::setUp();
        $this->repository = \Mockery::mock(VisitRepository::class);
        $this->request = new Request();
        $this->visits = new Visits($this->repository);
    }

    public function test_track_return_false_if_not_trackable() {
        $visits = \Mockery::mock("Foothing\Laravel\Visits\Visits[trackable]", [$this->repository]);
        $visits->shouldReceive('trackable')->once()->andReturn(false);
        $this->assertFalse($visits->track($this->request));
    }

    public function test_track_updates_with_repository() {
        $visits = \Mockery::mock("Foothing\Laravel\Visits\Visits[trackable]", [$this->repository]);

        $request = \Mockery::mock(Request::class);
        $session = \Mockery::mock(Store::class);
        $session->shouldReceive('getId')->once();
        $request->shouldReceive('getSession')->once()->andReturn($session);
        $request->shouldReceive('getClientIp')->once();
        $request->shouldReceive('path')->once();

        $visits->shouldReceive('trackable')->once()->andReturn(true);
        $this->repository->shouldReceive('update')->once();
        $visits->track($request);
    }

    public function test_trackable_return_false_when_not_configured() {
        Config::shouldReceive('get')->once()->with('visits.enabled')->andReturnNull();
        $this->assertFalse($this->visits->trackable($this->request));
    }

    public function test_trackable_return_false_when_configured_false() {
        Config::shouldReceive('get')->once()->with('visits.enabled')->andReturn(false);
        $this->assertFalse($this->visits->trackable($this->request));
    }

    public function test_trackable_return_true_when_no_rules() {
        Config::shouldReceive('get')->once()->with('visits.enabled')->andReturn(true);
        Config::shouldReceive('get')->once()->with('visits.rules')->andReturnNull();
        $this->assertTrue($this->visits->trackable($this->request));
    }

    public function test_trackable_return_true_if_rule_passes() {
        $visits = \Mockery::mock("Foothing\Laravel\Visits\Visits[passes, makeRule]", [$this->repository]);
        $rule = \Mockery::mock(Foothing\Laravel\Visits\Rules\RuleInterface::class);

        $visits->shouldReceive('makeRule')->andReturn($rule);
        Config::shouldReceive('get')->once()->with('visits.enabled')->andReturn(true);
        Config::shouldReceive('get')->once()->with('visits.rules')->andReturn(['foo']);

        $rule->shouldReceive('passes')->once()->andReturn(true);
        $this->assertTrue($visits->trackable($this->request));
    }

    public function test_trackable_return_false_if_rule_fails() {
        $visits = \Mockery::mock("Foothing\Laravel\Visits\Visits[passes, makeRule]", [$this->repository]);
        $rule = \Mockery::mock(Foothing\Laravel\Visits\Rules\RuleInterface::class);

        $visits->shouldReceive('makeRule')->andReturn($rule);
        Config::shouldReceive('get')->once()->with('visits.enabled')->andReturn(true);
        Config::shouldReceive('get')->once()->with('visits.rules')->andReturn(['foo']);

        $rule->shouldReceive('passes')->once()->andReturn(false);
        $this->assertFalse($visits->trackable($this->request));
    }

    public function test_dump_buffer() {
        $this->repository->shouldReceive('dump')->once();
        $this->visits->dumpBuffer();
    }

    public function tearDown() {
        \Mockery::close();
    }
}
