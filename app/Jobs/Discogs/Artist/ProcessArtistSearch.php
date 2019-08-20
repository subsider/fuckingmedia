<?php

namespace App\Jobs\Discogs\Artist;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Repositories\Discogs\ArtistRepository;
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
     * @param DiscogsClient $client
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, ArtistRepository $artistRepository)
    {
        $page = 1;

        do {
            $results = $client->artist()
                ->search($this->artistName)
                ->limit(50)
                ->page($page)
                ->get();


            dump("Page {$page}");
            collect($results['results'])->each(function ($result) use ($artistRepository) {
                $artist = $artistRepository->create($result);
                $artistRepository->addService($artist, $result)
                    ->addImages($artist, $result);
            });

            $page++;
        } while ($page <= DiscogsClient::MAX_PAGE);
    }
}
