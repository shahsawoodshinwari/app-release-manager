# App Release Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)
[![GitHub Tests Action Status](https://github.com/shaka/app-release-manager/actions/workflows/run-tests.yml/badge.svg)](https://github.com/shaka/app-release-manager/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://github.com/shaka/app-release-manager/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/shaka/app-release-manager/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)

App Release Manager is a platform-agnostic Laravel package for tracking application releases across platforms and
distribution channels, and for enforcing per-application release and update policies (minimum build, minimum version,
and force-update) based on semantic versions and build numbers.

> **Status:** Stable. Supported versions are **PHP 8.1 / 8.2 / 8.3 / 8.4** and **Laravel 10**. Other
> PHP/Laravel versions are not tested or supported.

## Requirements

- PHP `^8.1`
- Laravel `^10.0`

## Installation

You can install the package via composer:

```bash
composer require shaka/app-release-manager
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="app-release-manager-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="app-release-manager-config"
```

The config holds the reference data (platforms, distribution channel types, distribution channels, release types,
and release distribution statuses) that is inserted by the seeder.

## Seeding reference data

Run the seeder to populate the reference tables:

```bash
php artisan arm:seed-reference-data
```

## Usage

The package exposes a `AppReleaseManager` facade with a small, convention-based query API.

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;

// Resolve the application and a platform binding
$app = AppReleaseManager::application('paline');
$platform = AppReleaseManager::applicationPlatform('paline', 'android');

// Latest release for an application/platform
$latest = AppReleaseManager::latestRelease('paline', 'android');

// Latest published distribution on a given channel
$published = AppReleaseManager::latestPublishedDistribution('paline', 'android', 'google-play');

// Policy checks (build-number based)
$supported = AppReleaseManager::isSupported('paline', 'android', '220');   // true
$requires = AppReleaseManager::requiresUpdate('paline', 'android', '219'); // true
```

### Data model

- `applications` — the apps you ship (e.g. `paline`).
- `platforms` — `android`, `ios`, etc.
- `distribution_channel_types` / `distribution_channels` — e.g. `app-store`, `google-play`, `github`.
- `application_platforms` — pivot linking an application to a platform, carrying its release policy.
- `releases` — a build/version for an application/platform.
- `release_distributions` — a release pushed to a specific channel, with a `release_distribution_status`.
- `release_policies` — min build / min version / force-update rules per application/platform.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Shah Sawood](https://github.com/shahsawoodshinwari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
