---
layout: default
title: Configuration
description: Configure reference data and package settings
nav_order: 3
---

# Configuration
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Overview

The package configuration contains reference data used to seed the database. This data defines the platforms, distribution channels, release types, and statuses available in your application.

Publish the config file:

```bash
php artisan vendor:publish --tag="app-release-manager-config"
```

This creates `config/app-release-manager.php` in your application.

## Configuration Structure

```php
// config/app-release-manager.php

return [
    'reference_data' => [
        'platforms' => [...],
        'distribution_channel_types' => [...],
        'distribution_channels' => [...],
        'release_types' => [...],
        'release_distribution_statuses' => [...],
    ],
];
```

## Platforms

Define the platforms your applications target.

```php
'platforms' => [
    'Android',
    'iOS',
    'macOS',
    'watchOS',
    'Windows',
    'Linux',
    'Web',
],
```

Each platform is stored in the `platforms` table with a slugified version of its name.

### Adding Custom Platforms

Add new entries to the array:

```php
'platforms' => [
    'Android',
    'iOS',
    'Flutter',
    'React Native',
],
```

Then re-run the seeder:

```bash
php artisan arm:seed-reference-data
```

## Distribution Channel Types

Define the types of distribution channels available.

```php
'distribution_channel_types' => [
    'Store',
    'Direct',
    'Enterprise',
    'Internal',
],
```

| Type | Description |
|------|-------------|
| `Store` | Official app stores (Google Play, Apple App Store) |
| `Direct` | Direct download links |
| `Enterprise` | Enterprise distribution |
| `Internal` | Internal testing distribution |

## Distribution Channels

Define specific distribution channels. Each channel has a name, slug, and associated type.

```php
'distribution_channels' => [
    [
        'name' => 'Google Play',
        'slug' => 'google-play',
        'type' => 'Store',
    ],
    [
        'name' => 'Apple App Store',
        'slug' => 'apple-app-store',
        'type' => 'Store',
    ],
    [
        'name' => 'Huawei AppGallery',
        'slug' => 'huawei-appgallery',
        'type' => 'Store',
    ],
    [
        'name' => 'Microsoft Store',
        'slug' => 'microsoft-store',
        'type' => 'Store',
    ],
    [
        'name' => 'Direct Download',
        'slug' => 'direct-download',
        'type' => 'Direct',
    ],
    [
        'name' => 'Firebase App Distribution',
        'slug' => 'firebase-app-distribution',
        'type' => 'Internal',
    ],
],
```

### Adding Custom Channels

```php
'distribution_channels' => [
    // ... existing channels
    [
        'name' => 'TestFlight',
        'slug' => 'testflight',
        'type' => 'Internal',
    ],
    [
        'name' => 'GitHub Releases',
        'slug' => 'github-releases',
        'type' => 'Direct',
    ],
],
```

## Release Types

Define the types of releases you track.

```php
'release_types' => [
    'Major',
    'Minor',
    'Feature',
    'Patch',
    'Hotfix',
],
```

| Type | Description |
|------|-------------|
| `Major` | Breaking changes, major version bumps |
| `Minor` | New features, backward compatible |
| `Feature` | Feature additions |
| `Patch` | Bug fixes, minor improvements |
| `Hotfix` | Critical fixes requiring immediate release |

## Release Distribution Statuses

Define the statuses a distribution can have.

```php
'release_distribution_statuses' => [
    'Draft',
    'Submitted',
    'In Review',
    'Published',
    'Rejected',
    'Withdrawn',
],
```

| Status | Description |
|--------|-------------|
| `Draft` | Distribution created but not submitted |
| `Submitted` | Submitted to the distribution channel |
| `In Review` | Under review by the channel |
| `Published` | Live and available to users |
| `Rejected` | Rejected by the channel |
| `Withdrawn` | Withdrawn from distribution |

## Re-seeding

After modifying the configuration, re-run the seeder to apply changes:

```bash
php artisan arm:seed-reference-data
```

The seeder is idempotent — it will not create duplicate records.
