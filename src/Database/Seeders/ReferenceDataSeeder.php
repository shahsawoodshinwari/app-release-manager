<?php

namespace Shaka\AppReleaseManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Shaka\AppReleaseManager\Models\DistributionChannel;
use Shaka\AppReleaseManager\Models\DistributionChannelType;
use Shaka\AppReleaseManager\Models\Platform;
use Shaka\AppReleaseManager\Models\ReleaseDistributionStatus;
use Shaka\AppReleaseManager\Models\ReleaseType;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = config('app-release-manager.reference_data', []);

        foreach ($data['platforms'] ?? [] as $name) {
            Platform::query()->firstOrCreate(['slug' => str()->slug($name)], [
                'name' => $name,
                'slug' => str()->slug($name),
            ]);
        }

        foreach ($data['distribution_channel_types'] ?? [] as $name) {
            DistributionChannelType::query()->firstOrCreate(['slug' => str()->slug($name)], [
                'name' => $name,
                'slug' => str()->slug($name),
            ]);
        }

        $types = DistributionChannelType::all()->keyBy('slug');

        foreach ($data['distribution_channels'] ?? [] as $channel) {
            $typeSlug = str()->slug($channel['type'] ?? 'store');
            $type = $types->get($typeSlug) ?? $types->get('store');

            DistributionChannel::query()->firstOrCreate(['slug' => $channel['slug']], [
                'name' => $channel['name'],
                'slug' => $channel['slug'],
                'type_id' => $type?->id,
            ]);
        }

        foreach ($data['release_types'] ?? [] as $name) {
            ReleaseType::query()->firstOrCreate(['slug' => str()->slug($name)], [
                'name' => $name,
                'slug' => str()->slug($name),
            ]);
        }

        foreach ($data['release_distribution_statuses'] ?? [] as $name) {
            ReleaseDistributionStatus::query()->firstOrCreate(['slug' => str()->slug($name)], [
                'name' => $name,
                'slug' => str()->slug($name),
            ]);
        }
    }
}
