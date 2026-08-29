<?php

namespace Shaka\AppReleaseManager;

use Shaka\AppReleaseManager\Models\Application;
use Shaka\AppReleaseManager\Models\ApplicationPlatform;
use Shaka\AppReleaseManager\Models\Platform;
use Shaka\AppReleaseManager\Models\Release;
use Shaka\AppReleaseManager\Models\ReleaseDistribution;
use Shaka\AppReleaseManager\Models\ReleasePolicy;

class AppReleaseManager
{
    public function application(string $slug): ?Application
    {
        return Application::whereSlug($slug)->first();
    }

    public function platform(string $slug): ?Platform
    {
        return Platform::whereSlug($slug)->first();
    }

    public function applicationPlatform(string $applicationSlug, string $platformSlug): ?ApplicationPlatform
    {
        $application = $this->application($applicationSlug);

        if (! $application) {
            return null;
        }

        $platform = Platform::whereSlug($platformSlug)->first();

        if (! $platform) {
            return null;
        }

        return ApplicationPlatform::query()
            ->where('application_id', $application->id)
            ->where('platform_id', $platform->id)
            ->first();
    }

    public function policy(string $applicationSlug, string $platformSlug): ?ReleasePolicy
    {
        $applicationPlatform = $this->applicationPlatform($applicationSlug, $platformSlug);

        if (! $applicationPlatform) {
            return null;
        }

        return ReleasePolicy::query()
            ->where('application_platform_id', $applicationPlatform->id)
            ->first();
    }

    public function latestRelease(
        string $applicationSlug,
        string $platformSlug,
        ?string $channelSlug = null
    ): ?Release {
        $applicationPlatform = $this->applicationPlatform($applicationSlug, $platformSlug);

        if (! $applicationPlatform) {
            return null;
        }

        return Release::query()
            ->where('application_platform_id', $applicationPlatform->id)
            ->when(
                $channelSlug,
                fn ($query) => $query->whereHas(
                    'releaseDistributions.applicationDistributionChannel.distributionChannel',
                    fn ($channel) => $channel->whereSlug($channelSlug)
                )
            )
            ->orderByDesc('build_number')
            ->first();
    }

    public function latestPublishedDistribution(
        string $applicationSlug,
        string $platformSlug,
        string $channelSlug
    ): ?ReleaseDistribution {
        $applicationPlatform = $this->applicationPlatform($applicationSlug, $platformSlug);

        if (! $applicationPlatform) {
            return null;
        }

        return ReleaseDistribution::query()
            ->whereHas('release', fn ($release) => $release->where('application_platform_id', $applicationPlatform->id))
            ->whereHas(
                'applicationDistributionChannel.distributionChannel',
                fn ($channel) => $channel->whereSlug($channelSlug)
            )
            ->whereHas('status', fn ($status) => $status->whereSlug('published'))
            ->orderByDesc('store_build_number')
            ->first();
    }

    public function isSupported(string $applicationSlug, string $platformSlug, int $buildNumber): bool
    {
        return $this->checkBuild($applicationSlug, $platformSlug, $buildNumber) !== 'unsupported';
    }

    public function requiresUpdate(string $applicationSlug, string $platformSlug, int $buildNumber): bool
    {
        return $this->checkBuild($applicationSlug, $platformSlug, $buildNumber) === 'update-available';
    }

    public function checkBuild(string $applicationSlug, string $platformSlug, int $buildNumber): string
    {
        $policy = $this->policy($applicationSlug, $platformSlug);

        if (! $policy) {
            return 'supported';
        }

        if ($buildNumber < $policy->minimum_build_number) {
            return 'unsupported';
        }

        if ($buildNumber < $policy->recommended_build_number) {
            return 'update-available';
        }

        return 'supported';
    }
}
