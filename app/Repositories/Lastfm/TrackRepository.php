<?php

namespace App\Repositories\Lastfm;

use App\Models\Artist;
use App\Models\Bio;
use App\Models\Provider;
use App\Models\Related;
use App\Models\Service;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\Track;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function addTags(Track $track, array $results)
    {
        collect($results)->each(function ($result) use ($track) {
            $this->addTag($track, $result);
        });

        return $this;
    }

    public function addTag(Track $track, $result, int $match = null)
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
            'taggable_id' => $track->id,
            'taggable_type' => get_class($track),
        ]);

        if ($match) {
            $taggable->match = [$this->provider->name => (int) $result['count']];
        }

        $taggable->url = [$this->provider->name => $result['url']];

        $taggable->save();

        return $this;
    }

    public function addBio(Track $track, array $attributes, string $lang = 'en', array $overrides = [])
    {
        if ($attributes) {
            $bio = Bio::firstOrNew([
                'provider_id' => $this->provider->id,
                'model_id' => $track->id,
                'model_type' => get_class($track),
            ], $overrides);

            $bio->setTranslation('summary', $lang, $attributes['summary']);
            $bio->setTranslation('content', $lang, $attributes['content']);
            $bio->setTranslation('published_at', $lang, $attributes['published']);

            $bio->save();
        }

        return $this;
    }

    public function addRelatedCollection(Track $track, array $results)
    {
        collect($results)->each(function ($result) use ($track) {
            $this->addRelatedItem($track, $result);
        });

        return $this;
    }

    public function addRelatedItem(Track $track, $result)
    {
        $relatedItem = $this->create($result);
        $this->addService($relatedItem, $result)
            ->addImages($relatedItem, $result['image']);

        $this->relate($track, $relatedItem);
        $this->relate($relatedItem, $track);

        return $relatedItem;
    }

    public function relate(Model $source, Model $related, float $match = null)
    {
        $related = Related::firstOrNew([
            'source_id' => $source->id,
            'source_type' => get_class($source),
            'related_id' => $related->id,
            'related_type' => get_class($related),
        ]);

        if ($match) {
            $related->match = [$this->provider->name => $match];
        }

        $related->save();

        return $this;
    }
}
