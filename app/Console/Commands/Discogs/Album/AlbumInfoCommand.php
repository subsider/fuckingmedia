<?php

namespace App\Console\Commands\Discogs\Album;

use App\Jobs\Discogs\Album\ProcessAlbumInfo;
use Illuminate\Console\Command;

class AlbumInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discogs:album:info {id}';

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
        ProcessAlbumInfo::dispatch($this->argument('id'));
    }
}
