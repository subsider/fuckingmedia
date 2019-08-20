<?php

namespace App\Jobs\Lastfm\Track;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\AlbumRepository;
use App\Repositories\Lastfm\ArtistRepository;
use App\Repositories\Lastfm\TrackRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTrackInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $trackName;

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
     * @param string $trackName
     * @param string $artistName
     * @param string $lang
     */
    public function __construct(string $trackName, string $artistName, string $lang)
    {
        $this->trackName = $trackName;
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
            ->track()
            ->info($this->trackName, $this->artistName, $this->lang)
            ->get()['track'];

        $artist = $artistRepository->create($result['artist']);
        $album = $artistRepository->addService($artist, $result['artist'])
            ->addAlbum($artist, array_merge(['name' => $result['album']['title']], $result['album']));

        $track = $artistRepository->addTrack($artist, $result);

        resolve(AlbumRepository::class)
            ->addService($album, $result['album'])
            ->addImages($album, $result['album']['image'])
            ->addTrack($album, $track, $result['album']);

        resolve(TrackRepository::class)
            ->addService($track, $result)
            ->addTags($track, $result['toptags']['tag'])
            ->addBio($track, $result['wiki']);
    }
}
