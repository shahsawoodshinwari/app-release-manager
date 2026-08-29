<?php

namespace Shaka\AppReleaseManager;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Shaka\AppReleaseManager\Commands\AppReleaseManagerCommand;

class AppReleaseManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('app-release-manager')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('2024_01_01_000001_create_applications_table')
            ->hasMigration('2024_01_01_000002_create_platforms_table')
            ->hasMigration('2024_01_01_000003_create_distribution_channel_types_table')
            ->hasMigration('2024_01_01_000004_create_distribution_channels_table')
            ->hasMigration('2024_01_01_000005_create_application_platforms_table')
            ->hasMigration('2024_01_01_000006_create_application_distribution_channels_table')
            ->hasMigration('2024_01_01_000007_create_release_types_table')
            ->hasMigration('2024_01_01_000008_create_releases_table')
            ->hasMigration('2024_01_01_000009_create_release_distribution_statuses_table')
            ->hasMigration('2024_01_01_000010_create_release_distributions_table')
            ->hasMigration('2024_01_01_000011_create_release_policies_table')
            ->hasCommand(AppReleaseManagerCommand::class);
    }
}
