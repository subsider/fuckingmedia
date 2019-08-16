<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Service
 *
 * @property int $id
 * @property int $provider_id
 * @property string|null $internal_id
 * @property string $model_type
 * @property int $model_id
 * @property string $name
 * @property string|null $web_url
 * @property string|null $api_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $model
 * @property-read \App\Models\Provider $provider
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereInternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereWebUrl($value)
 * @mixin \Eloquent
 */
class Service extends BaseModel
{
    protected $dates = [
        'crawled_at',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
