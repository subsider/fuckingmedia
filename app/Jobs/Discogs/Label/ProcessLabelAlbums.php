<?php

namespace App\Jobs\Discogs\Label;

use App\Http\Clients\Discogs\DiscogsClient;
use App\Models\Artist;
use App\Models\Company;
use App\Models\Service;
use App\Repositories\Discogs\AlbumRepository;
use App\Repositories\Discogs\ArtistRepository;
use App\Repositories\Discogs\LabelRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessLabelAlbums implements ShouldQueue
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
     * @param LabelRepository $labelRepository
     * @param ArtistRepository $artistRepository
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(DiscogsClient $client, LabelRepository $labelRepository, ArtistRepository $artistRepository)
    {
        $service = Service::where('internal_id', $this->id)
            ->where('provider_id', $labelRepository->provider->id)
            ->where('model_type', 'App\Models\Company')
            ->first();

        if (!$service) return;

        $label = Company::find($service->model_id);

        $results = $client->label()
            ->albums($this->id)
            ->limit(100)
            ->get();

        collect($results['releases'])->each(function ($result) use ($label, $labelRepository, $artistRepository) {
            $artist = Artist::updateOrCreate(['name' => $result['artist']]);
            dump($artist->name);
            $result['type'] = 'release';
            $album = $artistRepository->addAlbum($artist, $result);
            resolve(AlbumRepository::class)->addService($artist, $album, $result)
                ->addImage($album, $result, 'thumb')
                ->addFormats($album, $result)
                ->syncLabel($album, $label)
                ->addStyles($album, $result);
        });
    }
}
