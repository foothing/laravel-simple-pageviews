<?php namespace Foothing\Laravel\Visits;

class DateFilter {

    /**
     * @var string
     */
    public $preset = null;

    /**
     * @var Carbon
     */
    public $start = null;

    /**
     * @var Carbon
     */
    public $end = null;

    public function __construct($preset, $start, $end) {
        $this->preset = $preset;
        $this->start = $start;
        $this->end = $end;
    }

}
