<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\ApplicationDistributionChannel;

class ApplicationDistributionChannelFactory extends Factory
{
    protected $model = ApplicationDistributionChannel::class;

    public function definition(): array
    {
        return [
            'application_platform_id' => ApplicationPlatformFactory::new(),
            'distribution_channel_id' => DistributionChannelFactory::new(),
            'store_identifier' => fake()->word(),
            'store_url' => fake()->url(),
            'is_active' => true,
        ];
    }
}
