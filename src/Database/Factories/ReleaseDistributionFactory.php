<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ReleaseDistribution;

class ReleaseDistributionFactory extends Factory
{
    protected $model = ReleaseDistribution::class;

    public function definition(): array
    {
        return [
            'release_id' => ReleaseFactory::new(),
            'application_distribution_channel_id' => ApplicationDistributionChannelFactory::new(),
            'status_id' => ReleaseDistributionStatusFactory::new(),
            'store_version' => fake()->numerify('#.#').'.0',
            'store_build_number' => fake()->numberBetween(1, 9999),
            'store_url' => fake()->url(),
            'published_at' => now(),
        ];
    }
}
