<?php

use Shaka\AppReleaseManager\Database\Seeders\ReferenceDataSeeder;
use Shaka\AppReleaseManager\Facades\AppReleaseManager;
use Shaka\AppReleaseManager\Models\Application;
use Shaka\AppReleaseManager\Models\ApplicationPlatform;
use Shaka\AppReleaseManager\Models\Platform;
use Shaka\AppReleaseManager\Models\Release;
use Shaka\AppReleaseManager\Models\ReleaseDistributionStatus;
use Shaka\AppReleaseManager\Models\ReleasePolicy;
use Shaka\AppReleaseManager\Models\ReleaseType;

beforeEach(function () {
    $this->seed(ReferenceDataSeeder::class);
});

it('seeds reference data from config', function () {
    expect(Platform::count())->toBe(7);
    expect(ReleaseDistributionStatus::count())->toBe(6);
    expect(ReleaseType::query()->where('slug', 'feature')->exists())->toBeTrue();
    expect(\Shaka\AppReleaseManager\Models\DistributionChannel::query()->where('slug', 'google-play')->exists())->toBeTrue();
});

it('models a full application release hierarchy', function () {
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

    expect($applicationPlatform->application->is($application))->toBeTrue();
    expect($applicationPlatform->platform->is($platform))->toBeTrue();
    expect($release->applicationPlatform->is($applicationPlatform))->toBeTrue();
    expect($release->releaseType->is($releaseType))->toBeTrue();
    expect($application->applicationPlatforms)->toHaveCount(1);
});

it('resolves the latest release and enforces the build policy', function () {
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

    expect(AppReleaseManager::latestRelease('paline', 'android')->build_number)->toBe(221);
    expect(AppReleaseManager::checkBuild('paline', 'android', 221))->toBe('supported');
    expect(AppReleaseManager::checkBuild('paline', 'android', 220))->toBe('update-available');
    expect(AppReleaseManager::checkBuild('paline', 'android', 219))->toBe('unsupported');
    expect(AppReleaseManager::isSupported('paline', 'android', 221))->toBeTrue();
    expect(AppReleaseManager::isSupported('paline', 'android', 219))->toBeFalse();
    expect(AppReleaseManager::requiresUpdate('paline', 'android', 220))->toBeTrue();
    expect(AppReleaseManager::requiresUpdate('paline', 'android', 221))->toBeFalse();
});

it('returns supported when no policy exists', function () {
    $application = Application::factory()->create(['slug' => 'paline']);
    $platform = Platform::where('slug', 'ios')->firstOrFail();

    ApplicationPlatform::factory()->create([
        'application_id' => $application->id,
        'platform_id' => $platform->id,
        'identifier' => 'com.paline.ios',
    ]);

    expect(AppReleaseManager::checkBuild('paline', 'ios', 1))->toBe('supported');
});
