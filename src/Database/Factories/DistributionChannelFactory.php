<?php

namespace Shaka\AppReleaseManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Shaka\AppReleaseManager\Models\DistributionChannel;

class DistributionChannelFactory extends Factory
{
    protected $model = DistributionChannel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => 'channel-'.$this->uniqueSlug(),
            'type_id' => DistributionChannelTypeFactory::new(),
            'is_active' => true,
        ];
    }

    protected function uniqueSlug(): string
    {
        return fake()->unique()->regexify('[a-z]{4}[0-9]{4}');
    }
}
