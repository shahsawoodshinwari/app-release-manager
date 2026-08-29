<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $slug
 * @property int $type_id
 * @property bool $is_active
 */
class DistributionChannel extends Model
{
    use HasFactory;

    protected $table = 'distribution_channels';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DistributionChannelType::class, 'type_id');
    }

    public function applicationDistributionChannels(): HasMany
    {
        return $this->hasMany(ApplicationDistributionChannel::class, 'distribution_channel_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
