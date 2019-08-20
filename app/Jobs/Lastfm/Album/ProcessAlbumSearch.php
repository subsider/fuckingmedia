<?php

namespace App\Jobs\Lastfm\Album;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\AlbumRepository;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAlbumSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var string
     */
    private $albumName;

    /**
     * Create a new job instance.
     *
     * @param string $albumName
     */
    public function __construct(string $albumName)
    {
        $this->albumName = $albumName;
    }

    /**
     * @param LastfmClient $client
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client, ArtistRepository $artistRepository)
    {
        $page = 1;

        do {
            $results = $client
                ->album()
                ->search($this->albumName)
                ->limit(1000)
                ->page($page)
                ->get();

            dump("Page: {$page}");

            collect($results['results']['albummatches']['album'])
                ->each(function($result) use ($artistRepository) {
                    $artistAttributes = ['name' => $result['artist']];
                    $artist = $artistRepository->create($artistAttributes);
                    dump($artist->name);

                    $album = $artistRepository->addService($artist, $artistAttributes)
                        ->addAlbum($artist, $result);

                    resolve(AlbumRepository::class)
                        ->addService($album, $result)
                        ->addImages($album, $result['image']);
                });

            $page++;
        } while (! empty($results['results']['albummatches']['artist']));
    }
}
