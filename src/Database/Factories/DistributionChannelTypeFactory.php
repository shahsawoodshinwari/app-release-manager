<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\DistributionChannelType;

class DistributionChannelTypeFactory extends Factory
{
    protected $model = DistributionChannelType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => 'type-'.$this->uniqueSlug(),
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
