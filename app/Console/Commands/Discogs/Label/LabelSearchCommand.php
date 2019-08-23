<?php

namespace App\Console\Commands\Discogs\Label;

use App\Jobs\Discogs\Label\ProcessLabelSearch;
use Illuminate\Console\Command;

class LabelSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:label:search {label}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProcessLabelSearch::dispatch($this->argument('label'));
    }
}
