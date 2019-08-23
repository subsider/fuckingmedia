<?php

namespace App\Repositories\Discogs;

use App\Models\Company;
use App\Models\Provider;

class LabelRepository
{
    /**
     * @var Provider
     */
    public $provider;

    /**
     * LabelRepository constructor.
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function create($result)
    {
        $label = Company::updateOrCreate([
            'name' => $result['title']
        ]);

        return $label;
    }

    public function addService(Company $label, $result)
    {
        $service = $label->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'internal_id' => $result['id'],
            'name' => $label->name,
            'api_url' => $result['resource_url'],
        ]);

        if (isset($result['uri'])) {
            $service->web_url = config('services.discogs.web_uri') . $result['uri'];
        }

        $service->save();

        return $this;
    }

    public function addImages($label, $result)
    {
        $sizes = [
            'thumb' => 'thumb',
            'landscape' => 'cover_image',
        ];

        collect($sizes)
            ->reject(function ($size) use ($result) {
                return $result[$size] == '';
            })
            ->each(function ($size, $key) use ($label, $result) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $key,
                    'path' => $result[$size],
                ]);

                $label->images()->syncWithoutDetaching($image);
            });

        return $this;
    }
}
