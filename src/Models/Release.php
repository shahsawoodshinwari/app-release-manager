<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $application_platform_id
 * @property int $release_type_id
 * @property string $version
 * @property int $build_number
 * @property string|null $title
 * @property string|null $release_notes
 * @property \Illuminate\Support\Carbon|null $released_at
 * @property bool $is_active
 */
class Release extends Model
{
    use HasFactory;

    protected $table = 'releases';

    protected $guarded = ['id'];

    protected $casts = [
        'build_number' => 'integer',
        'released_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function applicationPlatform(): BelongsTo
    {
        return $this->belongsTo(ApplicationPlatform::class);
    }

    public function releaseType(): BelongsTo
    {
        return $this->belongsTo(ReleaseType::class);
    }

    public function releaseDistributions(): HasMany
    {
        return $this->hasMany(ReleaseDistribution::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeReleased(Builder $query): Builder
    {
        return $query->whereNotNull('released_at')->where('released_at', '<=', now());
    }
}
