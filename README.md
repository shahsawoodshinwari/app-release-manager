# Laravel App Release Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)
[![GitHub Tests Action Status](https://github.com/shahsawoodshinwari/app-release-manager/actions/workflows/run-tests.yml/badge.svg)](https://github.com/shahsawoodshinwari/app-release-manager/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://github.com/shahsawoodshinwari/app-release-manager/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/shahsawoodshinwari/app-release-manager/actions?query=workflow%3A%22Fix+PHP+code+style+issues%22+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)

**App Release Manager** is a Laravel package for managing mobile and application releases, versions, distribution channels, and update policies from your Laravel backend.

It provides a centralized way to manage **Android and iOS application versions**, track releases across distribution channels such as **Google Play and Apple App Store**, and determine whether a client application is supported or requires an update.

You can use it to implement **minimum supported versions, minimum build numbers, optional updates, and force updates** without hard-coding release rules inside your mobile applications.

## Why App Release Manager?

Mobile applications often need backend-controlled release policies.

For example:

* Require users to upgrade to a minimum Android build.
* Require users to upgrade to a minimum iOS version.
* Allow older versions to continue working.
* Force users to update unsupported versions.
* Track which release is currently published on Google Play or the App Store.
* Manage multiple applications from the same Laravel backend.
* Manage different release policies for Android and iOS.
* Keep release and update rules in your database instead of hard-coding them in your mobile application.

App Release Manager provides the backend infrastructure for these use cases.

## Features

* 📱 **Multi-application support** — Manage releases for multiple applications from one Laravel installation.
* 🤖 **Android support** — Manage Android application versions and build numbers.
* 🍎 **iOS support** — Manage iOS application versions and build numbers.
* 📦 **Release management** — Track application releases by semantic version and build number.
* 🚀 **Distribution channels** — Associate releases with channels such as Google Play, Apple App Store, GitHub, or custom channels.
* 🔄 **Update policies** — Define the minimum supported version and minimum supported build.
* ⚠️ **Force updates** — Determine when an application version must be updated.
* 🔢 **Semantic versioning** — Compare application versions using semantic versions.
* 🔢 **Build number checks** — Compare Android/iOS build numbers for release compatibility.
* 🗄️ **Database-backed configuration** — Store release information and policies in your Laravel database.
* 🔌 **Simple Laravel API** — Query release information through a small, convention-based facade.
* 🧩 **Platform agnostic** — Designed to work with mobile applications, desktop applications, or other clients that expose a version/build number.

## Supported Versions

> **Status:** Stable.

Currently tested and supported:

* PHP `8.1`
* PHP `8.2`
* PHP `8.3`
* PHP `8.4`
* Laravel `10`

Other PHP or Laravel versions are currently not tested or officially supported.

## Requirements

* PHP `^8.1`
* Laravel `^10.0`

## Installation

Install the package using Composer:

```bash
composer require shaka/app-release-manager
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="app-release-manager-migrations"
php artisan migrate
```

Publish the configuration file if you need to customize the package:

```bash
php artisan vendor:publish --tag="app-release-manager-config"
```

The configuration contains the reference data used by the package, including platforms, distribution channel types, distribution channels, release types, and release distribution statuses.

## Seed Reference Data

Run the package seeder to populate the reference tables:

```bash
php artisan arm:seed-reference-data
```

This creates the default reference data required by the package.

## Usage

App Release Manager provides an `AppReleaseManager` facade with a small, convention-based API for querying applications, platforms, releases, distributions, and update policies.

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;
```

### Resolve an Application

```php
$app = AppReleaseManager::application('paline');
```

### Resolve an Application Platform

```php
$platform = AppReleaseManager::applicationPlatform(
    'paline',
    'android'
);
```

### Get the Latest Release

```php
$latest = AppReleaseManager::latestRelease(
    'paline',
    'android'
);
```

### Get the Latest Published Distribution

Retrieve the latest published release distributed through a specific channel:

```php
$published = AppReleaseManager::latestPublishedDistribution(
    'paline',
    'android',
    'google-play'
);
```

### Check Whether a Build Is Supported

```php
$supported = AppReleaseManager::isSupported(
    'paline',
    'android',
    '220'
);
```

### Check Whether an Update Is Required

```php
$requiresUpdate = AppReleaseManager::requiresUpdate(
    'paline',
    'android',
    '219'
);
```

For example:

```php
$supported = AppReleaseManager::isSupported(
    'paline',
    'android',
    '220'
); // true

$requiresUpdate = AppReleaseManager::requiresUpdate(
    'paline',
    'android',
    '219'
); // true
```

This allows your Laravel API to determine whether a client is running a supported version without embedding release policy logic directly into the application.

## Example API Flow

A typical mobile application can check its release status when it starts:

```text
Mobile Application
       │
       │ app = paline
       │ platform = android
       │ build = 219
       ▼
Laravel API
       │
       ▼
App Release Manager
       │
       ├── Application
       ├── Platform
       ├── Latest Release
       └── Release Policy
              │
              ├── Minimum Build
              ├── Minimum Version
              └── Force Update
       │
       ▼
Update Required?
       │
       ├── No  → Continue
       │
       └── Yes → Show update screen
```

The package handles the release and policy lookup on the backend. Your mobile application remains responsible for deciding how to present the update experience and where to send the user to update the application.

## Data Model

App Release Manager uses a normalized database structure designed to support multiple applications, platforms, releases, and distribution channels.

### Applications

`applications`

Represents the applications you ship.

Examples:

```text
paline
my-customer-app
driver-app
admin-app
```

### Platforms

`platforms`

Represents the target platform.

Examples:

```text
android
ios
```

### Distribution Channel Types

`distribution_channel_types`

Defines types of distribution channels.

Examples:

```text
app-store
google-play
github
```

### Distribution Channels

`distribution_channels`

Represents the actual channels where releases are distributed.

### Application Platforms

`application_platforms`

Connects an application to a platform and stores platform-specific release configuration and policy.

For example:

```text
Paline
 ├── Android
 └── iOS
```

### Releases

`releases`

Represents a specific application build/version.

A release can contain information such as:

```text
Version: 2.4.0
Build: 220
```

### Release Distributions

`release_distributions`

Associates a release with a distribution channel and tracks its distribution status.

For example:

```text
Release 2.4.0 (Build 220)
       │
       ├── Google Play → Published
       └── App Store   → Published
```

### Release Policies

`release_policies`

Defines the version and build requirements for an application/platform.

Policies can determine:

* Minimum supported version
* Minimum supported build
* Whether an update is required
* Whether an unsupported release should trigger a force update

## Common Use Cases

### Force Update a Mobile App

Use the release policy to define the minimum supported build and determine whether the current client must update.

### Minimum Supported Version

Prevent clients running versions older than your supported version from continuing to use the application.

### Android and iOS Version Management

Maintain separate release information and policies for Android and iOS from the same Laravel backend.

### Multiple Mobile Applications

Manage releases for several applications from a single Laravel installation.

### Distribution Channel Tracking

Track releases across channels such as:

* Google Play
* Apple App Store
* GitHub
* Custom distribution channels

### Backend-Controlled Releases

Keep version and update policies in your Laravel backend so they can be changed without releasing a new version of the mobile application.

## Mobile Application Integration

App Release Manager is backend/framework focused. It does not require a specific mobile framework.

It can be integrated with applications built using:

* Flutter
* React Native
* Native Android
* Native iOS
* Ionic / Capacitor
* Other mobile or client applications

A typical integration consists of:

1. The client sends its application identifier, platform, version, and/or build number to your Laravel API.
2. Laravel uses App Release Manager to evaluate the release policy.
3. Your API returns the update status to the client.
4. The client decides whether to continue, show an optional update, or require a mandatory update.

## Example Response

Your Laravel API can expose the package's result in whatever format is appropriate for your application.

For example:

```json
{
    "supported": false,
    "requires_update": true,
    "minimum_version": "2.4.0",
    "minimum_build": 220
}
```

The mobile application can then use this information to display an update screen or redirect the user to the appropriate distribution channel.

## Architecture

The package separates application release management into several concepts:

```text
Application
    │
    └── Application Platform
            │
            ├── Release Policy
            │
            └── Releases
                    │
                    └── Release Distributions
                            │
                            └── Distribution Channel
```

This allows release policies and distribution information to remain independent from the application code consuming them.

## Testing

Run the test suite:

```bash
composer test
```

You can also run the package's code-style checks through the configured Composer scripts.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for information about changes and releases.

## Contributing

Contributions are welcome.

Please see [CONTRIBUTING](CONTRIBUTING.md) for details on contributing to the project.

## Security

If you discover a security vulnerability, please review our [security policy](../../security/policy) for information on how to report it responsibly.

## Credits

* [Shah Sawood](https://github.com/shahsawoodshinwari)
* [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
