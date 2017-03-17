<?php namespace Foothing\Tests\Laravel\Visits\Repositories;

use Foothing\Laravel\Visits\Models\VisitBuffer;
use Foothing\Laravel\Visits\Repositories\VisitBufferRepository;
use Foothing\Laravel\Visits\Repositories\VisitRepository;

class VisitBufferRepositoryTest extends \Orchestra\Testbench\TestCase {

    use EnableDatabaseTesting;

    /**
     * @var VisitRepository
     */
    protected $repository;

    protected function init() {
        $this->repository = new VisitBufferRepository(new VisitBuffer());
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

    public function test_dump() {
        $this->repository->dump();
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
        return new VisitBuffer([
            'session' => 'sessionId',
            'ip' => 'ip',
            'url' => 'foo/bar',
            'date' => date('YmdH')
        ]);
    }
}
