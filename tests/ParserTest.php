<?php namespace Foothing\Tests\Laravel\Visits;

use Carbon\Carbon;
use Foothing\Laravel\Visits\DateFilter;
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
        $this->assertEquals($result->start, $parsed->start);
        $this->assertEquals($result->end, $parsed->end);
        $this->assertEquals($result->preset, $parsed->preset);
    }

    public function data() {
        $d0 = Carbon::now();
        $d1 = Carbon::now()->addDays(10)->copy();
        return [
            ['currentWeek', null,  new DateFilter('currentWeek', Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek())],
            ['currentMonth', null,  new DateFilter('currentMonth', Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth())],
            [null, null,  new DateFilter('currentYear', Carbon::now()->startOfYear(), Carbon::now()->endOfYear())],
            [null, $d0,  new DateFilter('currentYear', Carbon::now()->startOfYear(), Carbon::now()->endOfYear())],
            ['currentYear', null,  new DateFilter('currentYear', Carbon::now()->startOfYear(), Carbon::now()->endOfYear())],
            [$d0, $d1,  new DateFilter('default', $d0, $d1)],
            [$d0, null,  new DateFilter('default', $d0, null)],
            ['today', $d1,  new DateFilter('default', new Carbon('today'), $d1)],
            ['today', 'tomorrow',  new DateFilter('default', new Carbon('today'), new Carbon('tomorrow'))],
        ];
    }

}
