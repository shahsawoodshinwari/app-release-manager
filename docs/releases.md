---
layout: default
title: Releases
description: Managing application releases and versions
nav_order: 6
---

# Releases
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Overview

A release represents a specific build of your application for a particular platform. Each release has a semantic version string, a numeric build number, and optional metadata like release notes and a title.

## Creating Releases

Releases are associated with an `ApplicationPlatform`. Create them using Eloquent:

```php
use Shaka\AppReleaseManager\Models\Release;
use Shaka\AppReleaseManager\Models\ReleaseType;

$applicationPlatform = AppReleaseManager::applicationPlatform('paline', 'android');
$releaseType = ReleaseType::where('slug', 'minor')->first();

Release::create([
    'application_platform_id' => $applicationPlatform->id,
    'release_type_id' => $releaseType->id,
    'version' => '2.4.0',
    'build_number' => 220,
    'title' => 'Version 2.4.0',
    'release_notes' => 'Added new features and bug fixes.',
    'released_at' => now(),
    'is_active' => true,
]);
```

## Release Properties

| Property | Type | Description |
|----------|------|-------------|
| `version` | `string` | Semantic version (e.g., `2.4.0`) |
| `build_number` | `int` | Numeric build number for ordering |
| `title` | `string|null` | Human-readable release title |
| `release_notes` | `string|null` | What changed in this release |
| `released_at` | `datetime|null` | When the release was made |
| `is_active` | `bool` | Whether this release is active |

## Build Numbers

Build numbers are the primary ordering key for releases. Each release for an application platform must have a unique build number.

```php
// Build numbers determine the latest release
$latest = AppReleaseManager::latestRelease('paline', 'android');
// Returns the release with the highest build_number
```

## Release Types

Each release is associated with a release type. The default types are:

| Type | Description |
|------|-------------|
| `Major` | Breaking changes, major version bumps |
| `Minor` | New features, backward compatible |
| `Feature` | Feature additions |
| `Patch` | Bug fixes, minor improvements |
| `Hotfix` | Critical fixes requiring immediate release |

You can add custom release types in the configuration.

## Active vs Inactive Releases

Only active releases are considered when querying the latest release:

```php
$release = AppReleaseManager::latestRelease('paline', 'android');
// Only returns releases where is_active = true
```

## Querying Releases

### Get Latest Release

```php
$release = AppReleaseManager::latestRelease('paline', 'android');
```

### Get Latest Release on a Channel

```php
$release = AppReleaseManager::latestRelease('paline', 'android', 'google-play');
```

### Query via Eloquent

```php
use Shaka\AppReleaseManager\Models\Release;

// Get all releases for an application platform
$releases = Release::where('application_platform_id', $appPlatform->id)
    ->orderByDesc('build_number')
    ->get();

// Get only released (non-future) releases
$releases = Release::released()->get();

// Get only active releases
$releases = Release::active()->get();
```

## Release Distributions

A release can be distributed across multiple channels. See [Distribution Channels]({% link distribution-channels.md %}) for details.

## Example Workflow

1. Create a release for the application platform
2. Create release distributions for each channel
3. Update the release policy if the minimum build changes
4. Query the latest release from your API

```php
// 1. Create the release
$release = Release::create([...]);

// 2. Distribute to channels
$release->releaseDistributions()->create([
    'application_distribution_channel_id' => $adc->id,
    'status_id' => $publishedStatus->id,
    'store_version' => '2.4.0',
    'store_build_number' => 220,
    'published_at' => now(),
]);

// 3. The package will now return this as the latest release
$latest = AppReleaseManager::latestRelease('paline', 'android');
```
