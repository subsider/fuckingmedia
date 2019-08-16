<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Support\Arr;

class ArtistRepository
{
    /**
     * @var array
     */
    public $sizes = [
        'small' => 'icon',
        'medium' => 'avatar',
        'large' => 'thumb',
        'extralarge' => 'cover',
    ];

    /**
     * @var Provider
     */
    private $provider;

    /**
     * ArtistRepository constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function create(array $attributes, array $overrides = []): Artist
    {
        $artist = Artist::firstOrNew(['name' => $attributes['name']]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $artist->mbid = $attributes['mbid'];
        }

        collect($overrides)->each(function ($override, $key) use ($artist) {
            $artist->$key = ['lastfm' => (int)$override];
        });

        $artist->save();

        return $artist;
    }

    public function addService(Artist $artist, array $attributes): self
    {
        /** @var Service $service */
        $service = $artist->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'name' => $artist->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $service->internal_id = $attributes['mbid'];
        }

        $service->web_url = $attributes['url'];
        $service->save();

        return $this;
    }

    public function addImages(Artist $artist, array $images)
    {
        collect($images)
            ->reject(function ($result) {
                return $result['#text'] == '' ||
                    $result['size'] == 'mega' ||
                    $result['size'] == '';
            })
            ->each(function ($result) use ($artist) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $this->sizes[$result['size']],
                    'path' => $result['#text'],
                ]);

                $artist->images()->syncWithoutDetaching($image);
            });
    }
}
