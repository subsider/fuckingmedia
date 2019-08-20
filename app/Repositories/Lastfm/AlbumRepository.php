<?php

namespace App\Repositories\Lastfm;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Bio;
use App\Models\Provider;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\Track;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function addTrack(Album $album, Track $track, array $attributes)
    {
        $payload['position'] = $attributes['@attr']['rank']
            ?? $attributes['@attr']['position'];

        if (isset($attributes['duration'])) {
            $payload['duration'] = $attributes['duration'];
        }

        try {
            $album->tracks()->save($track, $payload);
        } catch (\Exception $e) {
            $album->tracks()->updateExistingPivot($track, $payload);
        }

        return $this;
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

    public function addTags(Album $album, array $results)
    {
        collect($results)->each(function ($result) use ($album) {
            $this->addTag($album, $result);
        });

        return $this;
    }

    public function addTag(Album $album, $result, int $match = null)
    {
        $tag = Tag::firstOrNew([
            'type' => 'tag',
            'slug' => Str::slug($result['name']),
        ], [
            'name' => $result['name'],
        ]);

        $tag->save();

        $taggable = Taggable::firstOrNew([
            'tag_id' => $tag->id,
            'taggable_id' => $album->id,
            'taggable_type' => get_class($album),
        ]);

        if ($match) {
            $taggable->match = [$this->provider->name => (int)$result['count']];
        }

        $taggable->url = [$this->provider->name => $result['url']];

        $taggable->save();

        return $this;
    }

    public function addBio(Album $album, array $attributes, string $lang = 'en', array $overrides = [])
    {
        if ($attributes) {
            $bio = Bio::firstOrNew([
                'provider_id' => $this->provider->id,
                'model_id' => $album->id,
                'model_type' => get_class($album),
            ], $overrides);

            $bio->setTranslation('summary', $lang, $attributes['summary']);
            $bio->setTranslation('content', $lang, $attributes['content']);
            $bio->setTranslation('published_at', $lang, $attributes['published']);

            $bio->save();
        }

        return $this;
    }
}
