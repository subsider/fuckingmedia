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

class ProcessTrackRelated implements ShouldQueue
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
     * Execute the job.
     *
     * @param LastfmClient $client
     * @param ArtistRepository $artistRepository
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(LastfmClient $client, ArtistRepository $artistRepository)
    {
        $artist = $this->checkArtist($this->artistName);
        $track = $this->checkTrack($artist, $this->trackName);

        $results = $client
            ->track()
            ->related($this->trackName, $this->artistName)
            ->limit(1000)
            ->get();

        collect($results['similartracks']['track'])
            ->each(function ($result) use ($artistRepository, $artist, $track) {
                $relatedArtist = $artistRepository
                    ->create($result['artist']);

                $relatedTrack = $artistRepository->addService($relatedArtist, $result)
                    ->addTrack($relatedArtist, $result);

                resolve(TrackRepository::class)
                    ->addService($relatedTrack, $result)
                    ->addImages($relatedTrack, $result['image'])
                    ->relate($track, $relatedTrack, $result['match'])
                    ->relate($relatedTrack, $track, $result['match']);
            });
    }
}
