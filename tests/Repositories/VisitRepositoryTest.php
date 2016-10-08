<?php namespace Foothing\Tests\Laravel\Visits\Repositories;

use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Repositories\VisitRepository;

class VisitRepositoryTest extends \Orchestra\Testbench\TestCase {

    use EnableDatabaseTesting;

    /**
     * @var VisitRepository
     */
    protected $repository;

    protected function init() {
        $this->repository = new VisitRepository(new Visit());
    }

    //
    //
    //  Insert / update tests.
    //
    //

    public function test_update_insert_record() {
        $visit = $this->data();
        $this->repository->update($visit);
        $persisted = $this->repository->findOneBy('ip', 'ip');

        $this->assertNotNull($persisted);
        $this->assertEquals('sessionId', $persisted->session);
        $this->assertEquals('ip', $persisted->ip);
        $this->assertEquals('foo/bar', $persisted->url);
        $this->assertEquals(date('YmdH'), $persisted->date);
        $this->assertEquals(1, $persisted->count);
    }

    public function test_update_ignores_model_count() {
        $visit = $this->data();
        $visit->count = 100;
        $this->repository->update($visit);
        $persisted = $this->repository->findOneBy('ip', 'ip');

        $this->assertEquals(1, $persisted->count);
    }

    public function test_update_actually_updates_on_duplicate() {
        $visit = $this->data();
        $this->repository->update($visit);
        $this->repository->update($visit);

        $this->assertEquals(1, $this->repository->all()->count());
        $this->assertEquals(2, $this->repository->find(1)->count);
    }

    /**
     * @dataProvider insertDataSet
     */
    public function test_update_insert_on_key_attributes_change($visit, $changed) {
        $this->repository->update($visit);
        $this->repository->update($changed);

        $this->assertEquals(2, $this->repository->all()->count());
        $this->assertEquals(1, $this->repository->find(1)->count);
        $this->assertEquals(1, $this->repository->find(2)->count);
    }

    //
    //
    //  Query data.
    //
    //

    public function test_aggregate_query() {
        $this->buildScenario();
        $aggregate = $this->repository->aggregate();
        $this->assertEquals(3, $aggregate->count());
        $this->assertEquals('foo/bar', $aggregate[0]->url);
        $this->assertEquals(2, $aggregate[0]->hits);
        $this->assertEquals('foo/bar/baz', $aggregate[1]->url);
        $this->assertEquals(2, $aggregate[1]->hits);
        $this->assertEquals('baz', $aggregate[2]->url);
        $this->assertEquals(1, $aggregate[2]->hits);
    }

    public function test_count_overall() {
        $this->buildScenario();
        $this->assertEquals(6, $this->repository->countOverallVisits());
    }

    public function test_count_visits() {
        $this->buildScenario();
        $this->assertEquals(5, $this->repository->countVisits());
    }

    public function test_count_unique_visits() {
        $this->buildScenario();
        $this->assertEquals(4, $this->repository->countUniqueVisits());
    }

    public function test_visit_trend() {
        $this->buildScenario();
        $this->assertEquals(1, $this->repository->getVisitsTrend('today')->count());
        $this->assertEquals(5, $this->repository->getVisitsTrend('today')[0]->hits);
        $this->assertEquals(date('Ymd'), $this->repository->getVisitsTrend('today')[0]->day);
    }

    // @TODO test date filters

    public function insertDataSet() {
        $original = $this->data();
        return [
            [$original, $original->replicate()->fill(['session' => 'changed'])],
            [$original, $original->replicate()->fill(['ip' => 'changed'])],
            [$original, $original->replicate()->fill(['url' => 'changed'])],
            [$original, $original->replicate()->fill(['date' => 'changed'])],
        ];
    }

    protected function data() {
        return new Visit([
            'session' => 'sessionId',
            'ip' => 'ip',
            'url' => 'foo/bar',
            'date' => date('YmdH')
        ]);
    }

    protected function buildScenario() {
        // Scenario:
        // - 1 visit from user A to foo/bar
        // - 2 visits from user A to foo/bar/baz
        // - 1 visit from user B to foo/bar
        // - 1 visit from user C to baz
        // - 1 visit from user A to another/date

        $visit1 = $this->data();
        $visit2 = $visit1->replicate()->fill(['url' => 'foo/bar/baz']);
        $visit3 = $visit1->replicate()->fill(['url' => 'foo/bar', 'session' => 'userB']);
        $visit4 = $visit1->replicate()->fill(['url' => 'baz', 'session' => 'userC']);
        $visit5 = $visit1->replicate()->fill(['url' => 'another/date', 'date' => '2016010100']);

        $this->repository->update($visit1);
        $this->repository->update($visit2);
        $this->repository->update($visit2);
        $this->repository->update($visit3);
        $this->repository->update($visit4);
        $this->repository->update($visit5);
    }
}