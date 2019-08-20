<?php

namespace App\Jobs\Discogs\Artist;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Repositories\Discogs\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistInfo implements ShouldQueue
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

    /**
     * @param DiscogsClient $client
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, ArtistRepository $artistRepository)
    {
        $result = $client->artist()
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
