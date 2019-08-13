<?php

namespace App\Jobs\Lastfm\Artist;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $page = 1;

        do {
            $results = $client
                ->artist()
                ->search($this->artistName)
                ->limit(1000)
                ->page($page)
                ->get();

            dump("Page: {$page}");

            collect($results['results']['artistmatches']['artist'])
                ->each(function($result) use ($artistRepository) {
                    $artist = $artistRepository->create($result, [
                        'listeners' => $result['listeners']
                    ]);

                    $artistRepository->addService($artist, $result);
                });

            $page++;
        } while (! empty($results['results']['artistmatches']['artist']));
    }
}
