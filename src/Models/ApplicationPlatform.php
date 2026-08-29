<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $application_id
 * @property int $platform_id
 * @property string $identifier
 * @property bool $is_active
 */
class ApplicationPlatform extends Model
{
    use HasFactory;

    protected $table = 'application_platforms';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    public function applicationDistributionChannels(): HasMany
    {
        return $this->hasMany(ApplicationDistributionChannel::class);
    }

    public function releasePolicy(): HasMany
    {
        return $this->hasMany(ReleasePolicy::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
