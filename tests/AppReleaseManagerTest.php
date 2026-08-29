<?php

namespace Shaka\AppReleaseManager\Tests;

use Shaka\AppReleaseManager\Database\Seeders\ReferenceDataSeeder;
use Shaka\AppReleaseManager\Facades\AppReleaseManager;
use Shaka\AppReleaseManager\Models\Application;
use Shaka\AppReleaseManager\Models\ApplicationPlatform;
use Shaka\AppReleaseManager\Models\DistributionChannel;
use Shaka\AppReleaseManager\Models\Platform;
use Shaka\AppReleaseManager\Models\Release;
use Shaka\AppReleaseManager\Models\ReleaseDistributionStatus;
use Shaka\AppReleaseManager\Models\ReleasePolicy;
use Shaka\AppReleaseManager\Models\ReleaseType;

class AppReleaseManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ReferenceDataSeeder::class);
    }

    public function test_seeds_reference_data_from_config(): void
    {
        self::assertSame(7, Platform::count());
        self::assertSame(6, ReleaseDistributionStatus::count());
        self::assertTrue(ReleaseType::query()->where('slug', 'feature')->exists());
        self::assertTrue(DistributionChannel::query()->where('slug', 'google-play')->exists());
    }

    public function test_models_a_full_application_release_hierarchy(): void
    {
        $application = Application::factory()->create(['slug' => 'paline', 'name' => 'Paline']);
        $platform = Platform::where('slug', 'android')->firstOrFail();

        $applicationPlatform = ApplicationPlatform::factory()->create([
            'application_id' => $application->id,
            'platform_id' => $platform->id,
            'identifier' => 'com.paline.app',
        ]);

        $releaseType = ReleaseType::where('slug', 'feature')->firstOrFail();

        $release = Release::factory()->create([
            'application_platform_id' => $applicationPlatform->id,
            'release_type_id' => $releaseType->id,
            'version' => '2.4.0',
            'build_number' => 220,
            'title' => 'Feature ABC',
        ]);

        self::assertTrue($applicationPlatform->application->is($application));
        self::assertTrue($applicationPlatform->platform->is($platform));
        self::assertTrue($release->applicationPlatform->is($applicationPlatform));
        self::assertTrue($release->releaseType->is($releaseType));
        self::assertCount(1, $application->applicationPlatforms);
    }

    public function test_resolves_latest_release_and_enforces_build_policy(): void
    {
        $application = Application::factory()->create(['slug' => 'paline']);
        $platform = Platform::where('slug', 'android')->firstOrFail();

        $applicationPlatform = ApplicationPlatform::factory()->create([
            'application_id' => $application->id,
            'platform_id' => $platform->id,
            'identifier' => 'com.paline.app',
        ]);

        $releaseType = ReleaseType::where('slug', 'feature')->firstOrFail();

        Release::factory()->create([
            'application_platform_id' => $applicationPlatform->id,
            'release_type_id' => $releaseType->id,
            'version' => '2.4.0',
            'build_number' => 220,
        ]);

        Release::factory()->create([
            'application_platform_id' => $applicationPlatform->id,
            'release_type_id' => $releaseType->id,
            'version' => '2.4.1',
            'build_number' => 221,
        ]);

        ReleasePolicy::factory()->create([
            'application_platform_id' => $applicationPlatform->id,
            'minimum_build_number' => 220,
            'recommended_build_number' => 221,
        ]);

        self::assertSame(221, AppReleaseManager::latestRelease('paline', 'android')->build_number);
        self::assertSame('supported', AppReleaseManager::checkBuild('paline', 'android', 221));
        self::assertSame('update-available', AppReleaseManager::checkBuild('paline', 'android', 220));
        self::assertSame('unsupported', AppReleaseManager::checkBuild('paline', 'android', 219));
        self::assertTrue(AppReleaseManager::isSupported('paline', 'android', 221));
        self::assertFalse(AppReleaseManager::isSupported('paline', 'android', 219));
        self::assertTrue(AppReleaseManager::requiresUpdate('paline', 'android', 220));
        self::assertFalse(AppReleaseManager::requiresUpdate('paline', 'android', 221));
    }

    public function test_returns_supported_when_no_policy_exists(): void
    {
        $application = Application::factory()->create(['slug' => 'paline']);
        $platform = Platform::where('slug', 'ios')->firstOrFail();

        ApplicationPlatform::factory()->create([
            'application_id' => $application->id,
            'platform_id' => $platform->id,
            'identifier' => 'com.paline.ios',
        ]);

        self::assertSame('supported', AppReleaseManager::checkBuild('paline', 'ios', 1));
    }
}
