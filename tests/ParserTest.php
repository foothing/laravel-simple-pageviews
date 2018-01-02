<?php namespace Foothing\Tests\Laravel\Visits;

use Carbon\Carbon;
use Foothing\Laravel\Visits\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Parser
     */
    protected $parser;

    public function setUp() {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider data
     */
    public function testParse($start, $end, $result) {
        $parsed = $this->parser->parse($start, $end);
        $this->assertEquals($result[0], $parsed[0]);
        $this->assertEquals($result[1], $parsed[1]);
    }

    public function data() {
        $d0 = Carbon::now();
        $d1 = Carbon::now()->addDays(10)->copy();
        return [
            ['currentWeek', null, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]],
            ['currentMonth', null, [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]],
            [null, null, [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]],
            [null, $d0, [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]],
            ['currentYear', null, [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]],
            [$d0, $d1, [$d0, $d1]],
            [$d0, null, [$d0, null]],
            ['today', $d1, [new Carbon('today'), $d1]],
            ['today', 'tomorrow', [new Carbon('today'), new Carbon('tomorrow')]],
        ];
    }

}
