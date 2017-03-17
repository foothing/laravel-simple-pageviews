<?php namespace Foothing\Laravel\Visits\Commands;

use Foothing\Laravel\Visits\Visits;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DumpVisitsBuffer extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'visits:buffer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps the visits buffer in the visits database.';

    /**
     * @var \Foothing\Laravel\Visits\Visits
     */
    protected $manager;

    public function __construct(Visits $manager) {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $this->manager->dumpBuffer();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }

}