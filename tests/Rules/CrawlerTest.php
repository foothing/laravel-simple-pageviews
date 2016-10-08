<?php namespace Foothing\Tests\Laravel\Visits\Rules;

use Foothing\Laravel\Visits\Rules\Crawler;
use Illuminate\Http\Request;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerTest extends \PHPUnit_Framework_TestCase {

    protected $request;

    /**
     * @var \Mockery
     */
    protected $detector;

    /**
     * @var Crawler
     */
    protected $rule;

    public function setUp() {
        $this->request = new Request();
        $this->detector = \Mockery::mock(CrawlerDetect::class);
        $this->rule = new Crawler($this->detector);
    }

    public function test_wraps_jaybizzle_success() {
        $this->detector->shouldReceive('isCrawler')->once()->andReturn(false);
        $this->assertTrue($this->rule->passes($this->request));
    }

    public function test_wraps_jaybizzle_fail() {
        $this->detector->shouldReceive('isCrawler')->once()->andReturn(true);
        $this->assertFalse($this->rule->passes($this->request));
    }


}