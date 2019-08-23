<?php

namespace App\Jobs\Discogs\Album;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Models\Artist;
use App\Repositories\Discogs\AlbumRepository;
use App\Repositories\Discogs\ArtistRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAlbumSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $albumName;

    /**
     * Create a new job instance.
     *
     * @param string $albumName
     */
    public function __construct(string $albumName)
    {
        $this->albumName = $albumName;
    }

    /**
     * Execute the job.
     *
     * @param DiscogsClient $client
     * @param ArtistRepository $artistRepository
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, ArtistRepository $artistRepository)
    {
        $page = 1;

        do {
            $results = $client->album()
                ->search($this->albumName)
                ->limit(100)
                ->page($page)
                ->get();

            dump("Page {$page}");
            collect($results['results'])->each(function ($result) use ($artistRepository) {
                $artistAlbum = explode(' - ', $result['title']);
                $artist = Artist::updateOrCreate([
                    'name' => $artistAlbum[0],
                ], ['type' => 'artist']);
                $result['title'] = $artistAlbum[1];
                $album = $artistRepository->addAlbum($artist, $result);
                resolve(AlbumRepository::class)
                    ->addService($artist, $album, $result)
                    ->addImage($album, $result, 'cover_image', 'cover')
                    ->addImage($album, $result, 'thumb')
                    ->addLabels($album, $result)
                    ->addStyles($album, $result)
                    ->addGenres($album, $result)
                    ->addFormats($album, $result)
                    ->addBarcodes($album, $result);
            });

            $page++;
        } while ($page <= DiscogsClient::MAX_PAGE);
    }
}
