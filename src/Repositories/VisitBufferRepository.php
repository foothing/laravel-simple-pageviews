<?php namespace Foothing\Laravel\Visits\Repositories;

use Foothing\Laravel\Visits\Models\Visit;
use Foothing\Laravel\Visits\Models\VisitBuffer;
use Foothing\Repository\Eloquent\EloquentRepository;

class VisitBufferRepository extends EloquentRepository {

    public function __construct(VisitBuffer $visit) {
        parent::__construct($visit);
    }

    public function update($visit) {
        $unique = [
            'session' => $visit->session,
            'ip' => $visit->ip,
            'url' => $visit->url,
            'date' => $visit->date
        ];

        $values = [
            'count' => \DB::raw('count+1')
        ];

        return $this->model->updateOrCreate($unique, $values);
    }

    public function dump() {
        \DB::transaction(function(){
            $this->executeDump();
        });
    }

    public function executeDump() {
        // Dump records.
        foreach ($this->all() as $bufferRecord) {
            $visit = new Visit($bufferRecord->toArray());
            $visit->save();
        }

        // Cleanup buffer.
        \DB::table('visits_buffer')->truncate();
    }
}
