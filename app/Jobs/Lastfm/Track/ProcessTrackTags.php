<?php

namespace App\Jobs\Lastfm\Track;

use App\Concerns\GuardAgainstEmptyArtist;
use App\Concerns\GuardAgainstEmptyTrack;
use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\ArtistRepository;
use App\Repositories\Lastfm\TrackRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTrackTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use GuardAgainstEmptyArtist, GuardAgainstEmptyTrack;

    /**
     * @var string
     */
    private $trackName;

    /**
     * @var string
     */
    private $artistName;

    /**
     * Create a new job instance.
     *
     * @param string $trackName
     * @param string $artistName
     */
    public function __construct(string $trackName, string $artistName)
    {
        $this->trackName = $trackName;
        $this->artistName = $artistName;
    }

    /**
     * @param LastfmClient $client
     * @param TrackRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client, TrackRepository $artistRepository)
    {
        $artist = $this->checkArtist($this->artistName);
        $track = $this->checkTrack($artist, $this->trackName);

        $results = $client
            ->track()
            ->tags($this->trackName, $this->artistName)
            ->get();

        collect($results['toptags']['tag'])
            ->each(function($result) use ($artistRepository, $track) {
                $artistRepository->addTag($track, $result, $result['count']);
            });
    }
}
