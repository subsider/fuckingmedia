<?php

namespace App\Console\Commands\Lastfm\Track;

use App\Jobs\Lastfm\Track\ProcessTrackTags;
use Illuminate\Console\Command;

class TrackTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:track:tags {track} {artist}';

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
        ProcessTrackTags::dispatch(
            $this->argument('track'),
            $this->argument('artist')
        );
    }
}
