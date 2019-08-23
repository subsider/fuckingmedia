<?php

namespace App\Jobs\Discogs\Album;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAlbumInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function handle(DiscogsClient $client, ArtistRepository $artistRepository)
    {
        $result = $client->album()
            ->info($this->id)
            ->get();

        $result['title'] = $result['name'];

        $artist = $artistRepository->create($result);
        $artistRepository->addService($artist, $result)
            ->addImagesFromResource($artist, $result['images'])
            ->addUrls($artist, $result['urls'])
            ->addAliases($artist, $result)
            ->addNameVariations($artist, $result['namevariations'])
            ->addBio($artist, $result['profile'])
            ->addMembers($artist, $result)
            ->addGroups($artist, $result);
    }
}
