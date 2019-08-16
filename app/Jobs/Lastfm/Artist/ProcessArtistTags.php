<?php

namespace App\Jobs\Lastfm\Artist;

use App\Concerns\GuardAgainstEmptyArtist;
use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistTags implements ShouldQueue
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
        $artist = $this->checkArtist($this->artistName);

        $results = $client
            ->artist()
            ->tags($this->artistName)
            ->get();

        collect($results['toptags']['tag'])
            ->each(function($result) use ($artistRepository, $artist) {
                $artistRepository->addTag($artist, $result, $result['count']);
            });
    }
}
