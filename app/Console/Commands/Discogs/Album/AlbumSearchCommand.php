<?php

namespace App\Console\Commands\Discogs\Album;

use App\Jobs\Discogs\Album\ProcessAlbumSearch;
use Illuminate\Console\Command;

class AlbumSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:album:search {album}';

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
        ProcessAlbumSearch::dispatch($this->argument('album'));
    }
}
