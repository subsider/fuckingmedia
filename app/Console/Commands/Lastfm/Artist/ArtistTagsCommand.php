<?php

namespace App\Console\Commands\Lastfm\Artist;

use App\Jobs\Lastfm\Artist\ProcessArtistTags;
use Illuminate\Console\Command;

class ArtistTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:artist:tags {artist}';

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
        ProcessArtistTags::dispatch($this->argument('artist'));
    }
}
