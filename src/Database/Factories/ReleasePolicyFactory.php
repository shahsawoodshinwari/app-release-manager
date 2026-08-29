<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ReleasePolicy;

class ReleasePolicyFactory extends Factory
{
    protected $model = ReleasePolicy::class;

    public function definition(): array
    {
        return [
            'application_platform_id' => ApplicationPlatformFactory::new(),
            'minimum_build_number' => 1,
            'recommended_build_number' => 100,
        ];
    }
}
