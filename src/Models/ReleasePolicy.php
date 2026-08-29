<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $application_platform_id
 * @property int $minimum_build_number
 * @property int $recommended_build_number
 */
class ReleasePolicy extends Model
{
    use HasFactory;

    protected $table = 'release_policies';

    protected $guarded = ['id'];

    protected $casts = [
        'minimum_build_number' => 'integer',
        'recommended_build_number' => 'integer',
    ];

    public function applicationPlatform(): BelongsTo
    {
        return $this->belongsTo(ApplicationPlatform::class);
    }
}
