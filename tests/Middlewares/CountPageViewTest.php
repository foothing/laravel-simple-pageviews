<?php namespace Foothing\Tests\Laravel\Visits\Middlewares;

use Foothing\Laravel\Visits\Middlewares\CountPageView;
use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Repositories\VisitRepository;
use Foothing\Laravel\Visits\Visits;
use Illuminate\Http\Request;

class CountPageViewTest extends \PHPUnit_Framework_TestCase {

    public function testHandle() {
        $manager = \Mockery::mock(Visits::class);
        $request = \Mockery::mock(Request::class);
        $middleware = new CountPageView($manager);

        $manager->shouldReceive('track')->once()->with($request);
        $middleware->handle($request, function(){});
    }

    public function tearDown() {
        \Mockery::close();
    }
}
