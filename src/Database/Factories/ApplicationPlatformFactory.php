<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ApplicationPlatform;

class ApplicationPlatformFactory extends Factory
{
    protected $model = ApplicationPlatform::class;

    public function definition(): array
    {
        return [
            'application_id' => ApplicationFactory::new(),
            'platform_id' => PlatformFactory::new(),
            'identifier' => fake()->word(),
            'is_active' => true,
        ];
    }
}
