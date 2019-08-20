<?php

namespace App\Console\Commands\Lastfm\Album;

use App\Jobs\Lastfm\Album\ProcessAlbumTags;
use Illuminate\Console\Command;

class AlbumTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:album:tags {album} {artist}';

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
        ProcessAlbumTags::dispatch(
            $this->argument('album'),
            $this->argument('artist')
        );
    }
}
