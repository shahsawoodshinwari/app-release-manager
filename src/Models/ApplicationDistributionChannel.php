<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $application_platform_id
 * @property int $distribution_channel_id
 * @property string|null $store_identifier
 * @property string|null $store_url
 * @property bool $is_active
 */
class ApplicationDistributionChannel extends Model
{
    use HasFactory;

    protected $table = 'application_distribution_channels';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function applicationPlatform(): BelongsTo
    {
        return $this->belongsTo(ApplicationPlatform::class, 'application_platform_id');
    }

    public function distributionChannel(): BelongsTo
    {
        return $this->belongsTo(DistributionChannel::class, 'distribution_channel_id');
    }

    public function releaseDistributions(): HasMany
    {
        return $this->hasMany(ReleaseDistribution::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
