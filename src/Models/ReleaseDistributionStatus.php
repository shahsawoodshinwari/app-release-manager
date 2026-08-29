<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $slug
 */
class ReleaseDistributionStatus extends Model
{
    use HasFactory;

    protected $table = 'release_distribution_statuses';

    protected $guarded = ['id'];

    public function releaseDistributions(): HasMany
    {
        return $this->hasMany(ReleaseDistribution::class, 'status_id');
    }
}
