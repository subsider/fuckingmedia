<?php

namespace App\Jobs\Discogs\Artist;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Models\Artist;
use App\Models\Service;
use App\Repositories\Discogs\AlbumRepository;
use App\Repositories\Discogs\ArtistRepository as ArtistRepositoryAlias;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArtistAlbums implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param DiscogsClient $client
     * @param ArtistRepositoryAlias $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, ArtistRepositoryAlias $artistRepository)
    {
        $service = Service::where('internal_id', $this->id)
            ->where('provider_id', $artistRepository->provider->id)
            ->where('model_type', 'App\Models\Artist')
            ->first();

        if (!$service) return;

        $artist = Artist::find($service->model_id);

        $results = $client->artist()
            ->albums($this->id)
            ->limit(100)
            ->get();

        collect($results['releases'])->each(function ($result) use ($artist, $artistRepository) {
            $albumArtist = $artist->updateOrCreate(['name' => $result['artist']]);
            $album = $artistRepository->addAlbum($albumArtist, $result);
            resolve(AlbumRepository::class)
                ->addService($albumArtist, $album, $result)
                ->addImage($album, $result, 'thumb')
                ->addFormats($album, $result)
                ->addCollaboration($artist, $albumArtist, $album, $result)
                ->addTrackInfo($artist, $album, $result)
                ->addLabel($album, $result);
        });
    }
}
