<?php

namespace App\Repositories\Lastfm;

use App\Models\Album;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Support\Arr;

class AlbumRepository
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

    public function addService(Album $album, array $attributes): self
    {
        /** @var Service $service */
        $service = $album->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'name' => $album->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $service->internal_id = $attributes['mbid'];
        }

        $service->web_url = $attributes['url'];
        $service->save();

        return $this;
    }

    public function addImages(Album $album, array $images)
    {
        collect($images)
            ->reject(function ($result) {
                return $result['#text'] == '' ||
                    $result['size'] == 'mega' ||
                    $result['size'] == '';
            })
            ->each(function ($result) use ($album) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $this->sizes[$result['size']],
                    'path' => $result['#text'],
                ]);

                $album->images()->syncWithoutDetaching($image);
            });

        return $this;
    }
}
