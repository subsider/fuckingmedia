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

class ProcessTrackSearch implements ShouldQueue
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
     * Create a new job instance.
     *
     * @param string $trackName
     * @param string $artistName
     */
    public function __construct(string $trackName, string $artistName = '')
    {
        $this->trackName = $trackName;
        $this->artistName = $artistName;
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
                ->track()
                ->search($this->trackName, $this->artistName)
                ->limit(1000)
                ->page($page)
                ->get();

            dump("Page: {$page}");

            collect($results['results']['trackmatches']['track'])
                ->each(function($result) use ($artistRepository) {
                    $artistAttributes = ['name' => $result['artist']];
                    $artist = $artistRepository->create($artistAttributes);
                    dump($artist->name);

                    $track = $artistRepository->addTrack($artist, $artistAttributes, [
                        'listeners' => $result['listeners']
                    ]);

                    resolve(TrackRepository::class)
                        ->addService($track, $result)
                        ->addImages($track, $result['image']);
                });

            $page++;
        } while (! empty($results['results']['trackmatches']['artist']));
    }
}
