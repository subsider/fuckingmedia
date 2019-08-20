<?php

namespace App\Jobs\Lastfm\Album;

use App\Concerns\GuardAgainstEmptyAlbum;
use App\Concerns\GuardAgainstEmptyArtist;
use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\AlbumRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAlbumTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use GuardAgainstEmptyArtist, GuardAgainstEmptyAlbum;

    /**
     * @var string
     */
    private $albumName;

    /**
     * @var string
     */
    private $artistName;

    /**
     * Create a new job instance.
     *
     * @param string $albumName
     * @param string $artistName
     */
    public function __construct(string $albumName, string $artistName)
    {
        $this->albumName = $albumName;
        $this->artistName = $artistName;
    }

    /**
     * @param LastfmClient $client
     * @param AlbumRepository $albumRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client, AlbumRepository $albumRepository)
    {
        $artist = $this->checkArtist($this->artistName);
        $album = $this->checkAlbum($artist, $this->albumName);

        $results = $client
            ->album()
            ->tags($this->albumName, $this->artistName)
            ->get();

        collect($results['toptags']['tag'])
            ->each(function($result) use ($albumRepository, $album) {
                $albumRepository->addTag($album, $result, $result['count']);
            });
    }
}
