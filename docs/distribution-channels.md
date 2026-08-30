---
layout: default
title: Distribution Channels
description: Managing distribution channels for app releases
nav_order: 7
---

# Distribution Channels
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Overview

Distribution channels represent where your application releases are published. A single release can be distributed across multiple channels, each with its own status and store metadata.

## Channel Structure

Distribution channels are organized in two layers:

1. **Distribution Channel Types** — Categories like Store, Direct, Enterprise, Internal
2. **Distribution Channels** — Specific channels like Google Play, Apple App Store

```
Distribution Channel Type
    |
    +-- Distribution Channel
            |
            +-- Application Distribution Channel
                    |
                    +-- Release Distribution
```

## Default Channels

| Channel | Type |
|---------|------|
| Google Play | Store |
| Apple App Store | Store |
| Huawei AppGallery | Store |
| Microsoft Store | Store |
| Direct Download | Direct |
| Firebase App Distribution | Internal |

## Setting Up Channels

### 1. Create Distribution Channels

If the default channels don't cover your needs, create custom ones:

```php
use Shaka\AppReleaseManager\Models\DistributionChannel;
use Shaka\AppReleaseManager\Models\DistributionChannelType;

$storeType = DistributionChannelType::where('slug', 'store')->first();

DistributionChannel::create([
    'name' => 'TestFlight',
    'slug' => 'testflight',
    'type_id' => $storeType->id,
    'is_active' => true,
]);
```

### 2. Link Channels to Application Platforms

Connect distribution channels to your application platform:

```php
use Shaka\AppReleaseManager\Models\ApplicationDistributionChannel;

$googlePlay = DistributionChannel::where('slug', 'google-play')->first();
$appPlatform = AppReleaseManager::applicationPlatform('paline', 'android');

ApplicationDistributionChannel::create([
    'application_platform_id' => $appPlatform->id,
    'distribution_channel_id' => $googlePlay->id,
    'store_identifier' => 'com.paline.app',
    'store_url' => 'https://play.google.com/store/apps/details?id=com.paline.app',
    'is_active' => true,
]);
```

## Tracking Distribution Status

Each release distribution has a status. The default statuses are:

| Status | Description |
|--------|-------------|
| `Draft` | Not yet submitted |
| `Submitted` | Submitted to the channel |
| `In Review` | Under review |
| `Published` | Live and available |
| `Rejected` | Rejected by the channel |
| `Withdrawn` | Withdrawn from distribution |

### Creating a Distribution

```php
use Shaka\AppReleaseManager\Models\ReleaseDistribution;
use Shaka\AppReleaseManager\Models\ReleaseDistributionStatus;

$publishedStatus = ReleaseDistributionStatus::where('slug', 'published')->first();

ReleaseDistribution::create([
    'release_id' => $release->id,
    'application_distribution_channel_id' => $adc->id,
    'status_id' => $publishedStatus->id,
    'store_version' => '2.4.0',
    'store_build_number' => 220,
    'store_url' => 'https://play.google.com/store/apps/details?id=com.paline.app',
    'published_at' => now(),
]);
```

## Querying Published Distributions

### Get Latest Published Distribution

```php
$distribution = AppReleaseManager::latestPublishedDistribution(
    'paline',
    'android',
    'google-play'
);

if ($distribution) {
    echo $distribution->store_version;      // "2.4.0"
    echo $distribution->store_build_number;  // 220
    echo $distribution->published_at;        // "2024-01-15 10:30:00"
}
```

### Get Latest Release on a Channel

```php
$release = AppReleaseManager::latestRelease('paline', 'android', 'google-play');
```

## Store Metadata

Distribution channels can store metadata about the release on the store:

| Property | Description |
|----------|-------------|
| `store_identifier` | App ID on the store (e.g., `com.paline.app`) |
| `store_url` | Direct link to the store listing |
| `store_version` | Version string as displayed on the store |
| `store_build_number` | Build number as displayed on the store |

This is useful when store versions differ from your internal versioning.

## Example: Multi-Channel Release

```php
// Release v2.4.0 distributed to multiple channels
$release = Release::create([
    'application_platform_id' => $appPlatform->id,
    'release_type_id' => $minorType->id,
    'version' => '2.4.0',
    'build_number' => 220,
    'released_at' => now(),
]);

// Published on Google Play
$release->releaseDistributions()->create([
    'application_distribution_channel_id' => $googlePlayAdc->id,
    'status_id' => $publishedStatus->id,
    'store_version' => '2.4.0',
    'store_build_number' => 220,
    'published_at' => now(),
]);

// In Review on Apple App Store
$release->releaseDistributions()->create([
    'application_distribution_channel_id' => $appStoreAdc->id,
    'status_id' => $inReviewStatus->id,
    'store_version' => '2.4.0',
    'store_build_number' => 220,
]);
```
