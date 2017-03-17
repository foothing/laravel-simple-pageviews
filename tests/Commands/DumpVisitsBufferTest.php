<?php namespace Foothing\Tests\Laravel\Visits\Commands;

use Foothing\Laravel\Visits\Commands\DumpVisitsBuffer;
use Foothing\Laravel\Visits\Visits;

class DumpVisitsBufferTest extends \Orchestra\Testbench\TestCase {

    public function test_fire() {
        $visits = \Mockery::mock(Visits::class);
        $command = new DumpVisitsBuffer($visits);
        $visits->shouldReceive('dumpBuffer')->once();
        $command->fire();
    }
}
