<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\Release;

class ReleaseFactory extends Factory
{
    protected $model = Release::class;

    public function definition(): array
    {
        return [
            'application_platform_id' => ApplicationPlatformFactory::new(),
            'release_type_id' => ReleaseTypeFactory::new(),
            'version' => fake()->numerify('#.#').'.0',
            'build_number' => fake()->numberBetween(1, 9999),
            'title' => fake()->sentence(3),
            'release_notes' => fake()->paragraph(),
            'released_at' => now(),
            'is_active' => true,
        ];
    }
}
