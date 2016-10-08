<?php namespace Foothing\Tests\Laravel\Visits\Rules;

use Foothing\Laravel\Visits\Rules\UrlWhitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UrlWhitelistTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlWhitelist
     */
    protected $rule;

    public function setUp() {
        $this->request = \Mockery::mock(Request::class);
        $this->rule = new UrlWhitelist();
    }

    public function test_rule_skipped_if_not_configured() {
        Config::shouldReceive('get')->once()->andReturnNull();
        $this->assertTrue($this->rule->passes($this->request));
    }

    /**
     * @dataProvider blacklisted
     */
    public function test_rule_blocks($rules, $url) {
        Config::shouldReceive('get')->once()->andReturn($rules);
        $this->request->shouldReceive('path')->once()->andReturn($url);
        $this->assertFalse($this->rule->passes($this->request));
    }

    /**
     * @dataProvider whitelisted
     */
    public function test_rule_passes($rules, $url) {
        Config::shouldReceive('get')->once()->andReturn($rules);
        $this->request->shouldReceive('path')->once()->andReturn($url);
        $this->assertTrue($this->rule->passes($this->request));
    }

    public function blacklisted() {
        return [
            // Patterns, url
            [['/foo\/bar/'], 'foo/bar'],
            [['/foo\/bar/'], 'foo/bar/'],
            [['/foo\/bar/'], 'foo/bar/baz'],
        ];
    }

    public function whitelisted() {
        return [
            // Patterns, url
            [['/foo\/bar/'], 'foo'],
            [['/foo\/bar/'], 'foo/'],
            [['/foo\/bar/'], 'baz'],
        ];
    }

    public function tearDown() {
        \Mockery::close();
    }
}