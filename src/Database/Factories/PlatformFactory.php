<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\Platform;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => 'platform-'.$this->uniqueSlug(),
            'is_active' => true,
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
