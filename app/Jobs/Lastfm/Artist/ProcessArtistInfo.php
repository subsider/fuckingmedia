<?php

namespace App\Jobs\Lastfm\Artist;

use App\Http\Clients\Lastfm\LastfmClient;
use App\Repositories\Lastfm\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @param string $artistName
     * @param string $lang
     */
    public function __construct(string $artistName, string $lang = 'es')
    {
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
            ->artist()
            ->info($this->artistName)
            ->get()['artist'];

        $artist = $artistRepository->create($result, [
            'listeners' => $result['stats']['listeners'],
            'playcount' => $result['stats']['playcount'],
            'streamable' => !! $result['streamable'],
        ]);
        $artistRepository->addService($artist, $result)
            ->addImages($artist, $result['image'])
            ->addRelatedCollection($artist, $result['similar']['artist'])
            ->addTags($artist, $result['tags']['tag'])
            ->addBio($artist, $result['bio'], $this->lang, [
                'url' => $result['bio']['links']['link']['href']
            ]);
    }
}
