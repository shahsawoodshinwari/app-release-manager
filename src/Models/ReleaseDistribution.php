<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $release_id
 * @property int $application_distribution_channel_id
 * @property int $status_id
 * @property string|null $store_version
 * @property int|null $store_build_number
 * @property string|null $store_url
 * @property Carbon|null $published_at
 */
class ReleaseDistribution extends Model
{
    use HasFactory;

    protected $table = 'release_distributions';

    protected $guarded = ['id'];

    protected $casts = [
        'store_build_number' => 'integer',
        'published_at' => 'datetime',
    ];

    public function release(): BelongsTo
    {
        return $this->belongsTo(Release::class);
    }

    public function applicationDistributionChannel(): BelongsTo
    {
        return $this->belongsTo(ApplicationDistributionChannel::class, 'application_distribution_channel_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ReleaseDistributionStatus::class, 'status_id');
    }
}
