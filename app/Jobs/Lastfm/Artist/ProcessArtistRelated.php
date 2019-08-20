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

class ProcessArtistRelated implements ShouldQueue
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
            ->related($this->artistName)
            ->limit(1000)
            ->get();

        collect($results['similarartists']['artist'])
            ->each(function($result) use ($artistRepository, $artist) {
                $relatedArtist = $artistRepository->create($result, [
                    'streamable' => !! $result['streamable'],
                ]);
                dump($relatedArtist->name);

                $artistRepository->addService($relatedArtist, $result)
                    ->addImages($relatedArtist, $result['image'])
                    ->relate($artist, $relatedArtist, (float) $result['match'])
                    ->relate($relatedArtist, $artist, (float) $result['match']);
            });
    }
}
