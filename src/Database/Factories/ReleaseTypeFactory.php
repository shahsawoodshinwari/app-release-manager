<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ReleaseType;

class ReleaseTypeFactory extends Factory
{
    protected $model = ReleaseType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => 'rtype-'.$this->uniqueSlug(),
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
