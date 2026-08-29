<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ReleaseDistributionStatus;

class ReleaseDistributionStatusFactory extends Factory
{
    protected $model = ReleaseDistributionStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => 'status-'.$this->uniqueSlug(),
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
