<?php

namespace App\Console\Commands\Lastfm\Artist;

use App\Http\Clients\Lastfm\LastfmClient;
use Illuminate\Console\Command;

class ArtistSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lastfm:artist:search {artist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /**
     * @var LastfmClient
     */
    private $client;

    /**
     * Create a new command instance.
     *
     * @param LastfmClient $client
     */
    public function __construct(LastfmClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $results = $this->client->searchArtist($this->argument('artist'));

        dd($results);
    }
}
