<?php

namespace Shaka\AppReleaseManager\Facades;

use Illuminate\Support\Facades\Facade;
use Shaka\AppReleaseManager\Models\Application;
use Shaka\AppReleaseManager\Models\ApplicationPlatform;
use Shaka\AppReleaseManager\Models\Platform;
use Shaka\AppReleaseManager\Models\Release;
use Shaka\AppReleaseManager\Models\ReleaseDistribution;
use Shaka\AppReleaseManager\Models\ReleasePolicy;

/**
 * @see \Shaka\AppReleaseManager\AppReleaseManager
 *
 * @method static Application|null application(string $slug)
 * @method static Platform|null platform(string $slug)
 * @method static ApplicationPlatform|null applicationPlatform(string $applicationSlug, string $platformSlug)
 * @method static ReleasePolicy|null policy(string $applicationSlug, string $platformSlug)
 * @method static Release|null latestRelease(string $applicationSlug, string $platformSlug, ?string $channelSlug = null)
 * @method static ReleaseDistribution|null latestPublishedDistribution(string $applicationSlug, string $platformSlug, string $channelSlug)
 * @method static bool isSupported(string $applicationSlug, string $platformSlug, int $buildNumber)
 * @method static bool requiresUpdate(string $applicationSlug, string $platformSlug, int $buildNumber)
 * @method static string checkBuild(string $applicationSlug, string $platformSlug, int $buildNumber)
 */
class AppReleaseManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shaka\AppReleaseManager\AppReleaseManager::class;
    }
}
