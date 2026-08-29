<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $slug
 */
class DistributionChannelType extends Model
{
    use HasFactory;

    protected $table = 'distribution_channel_types';

    protected $guarded = ['id'];

    public function distributionChannels(): HasMany
    {
        return $this->hasMany(DistributionChannel::class, 'type_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
