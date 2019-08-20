<?php

namespace App\Repositories\Discogs;

use App\Models\Artist;
use App\Models\Bio;
use App\Models\Image;
use App\Models\Provider;

class ArtistRepository
{
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

    public function create(array $result)
    {
        $artist = Artist::firstOrNew([
            'name' => $result['title'],
        ]);

        if (isset($result['type'])) {
            $artist->type = $result['type'];
        }

        if (isset($result['realname'])) {
            $artist->real_name = $result['realname'];
        }

        $artist->save();

        return $artist;
    }

    public function addService(Artist $artist, array $result)
    {
        $service = $artist->services()->firstOrNew([
            'provider_id' => $this->provider->id,
            'internal_id' => $result['id'],
            'name' => $artist->name,
            'api_url' => $result['resource_url'],
        ]);

        if (isset($result['uri'])) {
            $service->web_url = config('services.discogs.web_uri') . $result['uri'];
        }

        $service->save();

        return $this;
    }

    public function addImages($artist, $result)
    {
        $sizes = [
            'thumb' => 'thumb',
            'landscape' => 'cover_image',
        ];

        collect($sizes)
            ->reject(function ($size) use ($result) {
                return $result[$size] == '';
            })
            ->each(function ($size, $key) use ($artist, $result) {
                $image = $this->provider->images()->updateOrCreate([
                    'type' => $key,
                    'path' => $result[$size],
                ]);

                $artist->images()->syncWithoutDetaching($image);
            });

        return $this;
    }

    public function addImagesFromResource($artist, $result)
    {
        collect($result)->each(function ($result) use ($artist) {
            $image = Image::updateOrCreate([
                'provider_id' => $this->provider->id,
                'width' => $result['width'],
                'height' => $result['height'],
                'type' => $result['width'] . 'x' . $result['height'],
                'path' => $result['uri'],
                'thumb_path' => $result['uri150'],
                'resource_type' => $result['type'],
            ]);

            $artist->images()->syncWithoutDetaching($image);
        });

        return $this;
    }

    public function addUrls(Artist $artist, array $result)
    {
        collect($result)->each(function ($url) use ($artist) {
            $artist->urls()->updateOrCreate([
                'path' => $url,
            ]);
        });

        return $this;
    }

    public function addAliases(Artist $artist, array $result)
    {
        if (isset($result['aliases'])) {
            collect($result)->each(function ($result) use ($artist) {
                $result['title'] = $result['name'];
                $aliasedArtist = $this->create($result);
                $this->addService($aliasedArtist, $result);
                $artist->aliases()->updateOrCreate([
                    'name' => $result['name'],
                ]);
            });
        }

        return $this;
    }

    public function addNameVariations(Artist $artist, array $result)
    {
        collect($result)->each(function ($name) use ($artist) {
            $artist->nameVariations()->updateOrCreate([
                'name' => $name,
            ]);
        });

        return $this;
    }

    public function addBio(Artist $artist, $result)
    {
        $bio = Bio::firstOrNew([
            'provider_id' => $this->provider->id,
            'model_id' => $artist->id,
            'model_type' => get_class($artist),
        ]);

        $lang = app()->getLocale();

        $bio->setTranslation('summary', $lang, $result);
        $bio->save();

        return $this;
    }

    public function addMembers(Artist $artist, $result)
    {
        if (isset($result['members'])) {
            collect($result['members'])->each(function ($result) use ($artist) {
                $result['title'] = $result['name'];
                $member = $this->create($result);
                $this->addService($member, $result);
                $this->addMember($artist, $member, $result);
            });
        }

        return $this;
    }

    public function addGroups(Artist $artist, $result)
    {
        if (isset($result['groups'])) {
            collect($result['groups'])->each(function ($result) use ($artist) {
                $result['title'] = $result['name'];
                $group = $this->create($result);
                $this->addService($group, $result);
                $this->addMember($group, $artist, $result);
            });
        }

        return $this;
    }

    public function addMember(Artist $artist, Artist $member, $result)
    {
        try {
            $artist->members()->save($member, ['active' => $result['active']]);
        } catch (\Exception $e) {
            $artist->members()->updateExistingPivot($member, ['active' => $result['active']]);
        }

        return $this;
    }
}
