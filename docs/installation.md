---
layout: default
title: Installation
description: How to install and set up Laravel App Release Manager
nav_order: 2
---

# Installation
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Requirements

- PHP ^8.1
- Laravel ^10.0

## Install via Composer

```bash
composer require shaka/app-release-manager
```

The package will automatically register its service provider and facade through Laravel's package auto-discovery.

## Publish Migrations

Publish the database migrations to your application:

```bash
php artisan vendor:publish --tag="app-release-manager-migrations"
```

This publishes 11 migration files to `database/migrations/`:

| Migration | Table |
|-----------|-------|
| `create_applications_table` | `applications` |
| `create_platforms_table` | `platforms` |
| `create_distribution_channel_types_table` | `distribution_channel_types` |
| `create_distribution_channels_table` | `distribution_channels` |
| `create_application_platforms_table` | `application_platforms` |
| `create_application_distribution_channels_table` | `application_distribution_channels` |
| `create_release_types_table` | `release_types` |
| `create_releases_table` | `releases` |
| `create_release_distribution_statuses_table` | `release_distribution_statuses` |
| `create_release_distributions_table` | `release_distributions` |
| `create_release_policies_table` | `release_policies` |

Run the migrations:

```bash
php artisan migrate
```

## Publish Configuration

Publish the configuration file to customize the package:

```bash
php artisan vendor:publish --tag="app-release-manager-config"
```

This publishes `config/app-release-manager.php` to your application's config directory.

## Seed Reference Data

Run the seeder to populate the reference tables with default data:

```bash
php artisan arm:seed-reference-data
```

This seeds the following reference data from your published config:

- **Platforms** — Android, iOS, macOS, watchOS, Windows, Linux, Web
- **Distribution Channel Types** — Store, Direct, Enterprise, Internal
- **Distribution Channels** — Google Play, Apple App Store, Huawei AppGallery, Microsoft Store, Direct Download, Firebase App Distribution
- **Release Types** — Major, Minor, Feature, Patch, Hotfix
- **Release Distribution Statuses** — Draft, Submitted, In Review, Published, Rejected, Withdrawn

{: .note }
The seeder is idempotent — it uses `firstOrCreate` and is safe to run multiple times.

## Verify Installation

After installation, verify everything is working:

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;

// Should return null if no applications exist yet
$application = AppReleaseManager::application('my-app');
```

## Next Steps

- [Configuration](configuration.md) — Customize reference data and package settings
- [Usage](usage.md) — Learn how to use the facade API
- [API Reference](api.md) — Full API documentation
