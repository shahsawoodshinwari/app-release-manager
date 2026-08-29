<?php

namespace Shaka\AppReleaseManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $slug
 */
class ReleaseType extends Model
{
    use HasFactory;

    protected $table = 'release_types';

    protected $guarded = ['id'];

    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }
}
