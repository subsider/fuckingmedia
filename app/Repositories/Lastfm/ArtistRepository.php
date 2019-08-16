<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;
use App\Models\Bio;
use App\Models\Provider;
use App\Models\Related;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

        if (Arr::has($attributes, 'ontour') && $attributes['ontour'] != '') {
            $artist->on_tour = $attributes['ontour'];
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

        return $this;
    }

    public function addRelatedCollection(Artist $artist, array $results)
    {
        collect($results)->each(function ($result) use ($artist) {
            $this->addRelatedItem($artist, $result);
        });

        return $this;
    }

    public function addRelatedItem(Artist $artist, $result)
    {
        $relatedItem = $this->create($result);
        $this->addService($relatedItem, $result)
            ->addImages($relatedItem, $result['image']);

        $this->relate($artist, $relatedItem);
        $this->relate($relatedItem, $artist);

        return $relatedItem;
    }

    public function relate(Model $source, Model $related)
    {
        Related::updateOrCreate([
            'source_id' => $source->id,
            'source_type' => get_class($source),
            'related_id' => $related->id,
            'related_type' => get_class($related),
        ]);

        return $this;
    }

    public function addTags(Artist $artist, array $results)
    {
        collect($results)->each(function ($result) use ($artist) {
            $this->addTag($artist, $result);
        });

        return $this;
    }

    public function addTag(Artist $artist, $result)
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
            'taggable_id' => $artist->id,
            'taggable_type' => get_class($artist),
        ]);

        $taggable->url = [$this->provider->name => $result['url']];

        $taggable->save();

        return $this;
    }

    public function addBio(Artist $artist, array $attributes, string $lang = 'en', array $overrides = [])
    {
        if ($attributes) {
            $bio = Bio::firstOrNew([
                'provider_id' => $this->provider->id,
                'model_id' => $artist->id,
                'model_type' => get_class($artist),
            ], $overrides);

            $bio->setTranslation('summary', $lang, $attributes['summary']);
            $bio->setTranslation('content', $lang, $attributes['content']);
            $bio->setTranslation('published_at', $lang, $attributes['published']);

            $bio->save();
        }

        return $this;
    }
}
