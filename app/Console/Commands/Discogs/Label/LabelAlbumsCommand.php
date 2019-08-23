<?php

namespace App\Console\Commands\Discogs\Label;

use App\Jobs\Discogs\Label\ProcessLabelAlbums;
use Illuminate\Console\Command;

class LabelAlbumsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:label:albums {id}';

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
        ProcessLabelAlbums::dispatch($this->argument('id'));
    }
}
