<?php

namespace App\Jobs\Lastfm\Artist;

use App\Concerns\GuardAgainstEmptyArtist;
use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\AlbumRepository;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistAlbums implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use GuardAgainstEmptyArtist;

    /**
     * @var string
     */
    private $artistName;

    /**
     * Create a new job instance.
     *
     * @param string $artistName
     */
    public function __construct(string $artistName)
    {
        $this->artistName = $artistName;
    }

    /**
     * @param LastfmClient $client
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(
        LastfmClient $client,
        ArtistRepository $artistRepository
    )
    {
        $this->checkArtist($this->artistName);

        $results = $client
            ->artist()
            ->albums($this->artistName)
            ->get();

        collect($results['topalbums']['album'])
            ->each(function($result) use ($artistRepository) {
                $artist = $artistRepository->create($result['artist']);
                $album = $artistRepository->addService($artist, $result['artist'])
                    ->addAlbum($artist, $result);

                resolve(AlbumRepository::class)
                    ->addService($album, $result)
                    ->addImages($album, $result['image']);
            });
    }
}
