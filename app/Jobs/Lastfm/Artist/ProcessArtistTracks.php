<?php

namespace App\Jobs\Lastfm\Artist;

use App\Concerns\GuardAgainstEmptyArtist;
use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\ArtistRepository;
use App\Repositories\Lastfm\TrackRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistTracks implements ShouldQueue
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
    public function handle(LastfmClient $client, ArtistRepository $artistRepository)
    {
        $this->checkArtist($this->artistName);

        $results = $client
            ->artist()
            ->tracks($this->artistName)
            ->get();

        collect($results['toptracks']['track'])
            ->each(function($result) use ($artistRepository) {
                $artist = $artistRepository->create($result['artist']);
                $track = $artistRepository->addService($artist, $result['artist'])
                    ->addTrack($artist, $result, [
                        'listeners' => $result['listeners'],
                        'playcount' => $result['playcount'],
                        'streamable' => !! $result['streamable'],
                    ], $result['@attr']['rank']);

                resolve(TrackRepository::class)
                    ->addService($track, $result)
                    ->addImages($track, $result['image']);
            });
    }
}
