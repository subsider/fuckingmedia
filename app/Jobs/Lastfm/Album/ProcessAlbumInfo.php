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

class ProcessAlbumInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $albumName;

    /**
     * @var string
     */
    private $artistName;

    /**
     * @var string
     */
    private $lang;

    /**
     * Create a new job instance.
     *
     * @param string $albumName
     * @param string $artistName
     * @param string $lang
     */
    public function __construct(string $albumName, string $artistName, string $lang = 'es')
    {
        $this->albumName = $albumName;
        $this->artistName = $artistName;
        $this->lang = $lang;
    }

    /**
     * @param LastfmClient $client
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client, ArtistRepository $artistRepository)
    {
        $result = $client
            ->album()
            ->info($this->albumName, $this->artistName)
            ->get()['album'];

        $artistAttributes = ['name' => $result['artist']];
        $artist = $artistRepository->create($artistAttributes);

        $album = $artistRepository->addService($artist, $artistAttributes)
            ->addAlbum($artist, $result);

        $albumRepository = resolve(AlbumRepository::class)
            ->addService($album, $result)
            ->addImages($album, $result['image'])
            ->addTags($album, $result['tags']['tag'])
            ->addBio($album, $result['wiki'], $this->lang);

        collect($result['tracks']['track'])
            ->each(function ($result) use ($album, $albumRepository, $artist, $artistRepository) {
                $track = $artistRepository->addService($artist, $result['artist'])
                    ->addTrack($artist, $result);
                $albumRepository->addTrack($album, $track, $result);
            });
    }
}
