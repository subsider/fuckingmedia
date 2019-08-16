<?php

namespace App\Repositories\Lastfm;

use App\Models\Provider;
use App\Models\Service;
use App\Models\Track;
use Illuminate\Support\Arr;

class TrackRepository
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
     * TrackRepository constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function addService(Track $track, array $attributes): self
    {
        /** @var Service $service */
        $service = $track->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'name' => $track->name,
        ]);

        if (Arr::has($attributes, 'mbid') && $attributes['mbid'] != '') {
            $service->internal_id = $attributes['mbid'];
        }

        $service->web_url = $attributes['url'];
        $service->save();

        return $this;
    }

    public function addImages(Track $track, array $images)
    {
        collect($images)
            ->reject(function ($result) {
                return $result['#text'] == '' ||
                    $result['size'] == 'mega' ||
                    $result['size'] == '';
            })
            ->each(function ($result) use ($track) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $this->sizes[$result['size']],
                    'path' => $result['#text'],
                ]);

                $track->images()->syncWithoutDetaching($image);
            });

        return $this;
    }
}
