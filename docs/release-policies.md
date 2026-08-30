---
layout: default
title: Release Policies
description: Configuring version requirements and force updates
nav_order: 8
---

# Release Policies
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Overview

Release policies define the version requirements for an application platform. They determine which client builds are supported, which need updates, and which are unsupported.

Each policy has two key values:

- **Minimum build number** — The lowest build number that is still supported
- **Recommended build number** — The build number that triggers optional update prompts

## Policy Logic

```
Client Build Number
        |
        v
    No policy? → Supported
        |
        v
    build < minimum → Unsupported (force update)
        |
        v
    build < recommended → Update available (optional)
        |
        v
    build >= recommended → Supported
```

## Creating a Policy

```php
use Shaka\AppReleaseManager\Models\ReleasePolicy;

$appPlatform = AppReleaseManager::applicationPlatform('paline', 'android');

ReleasePolicy::create([
    'application_platform_id' => $appPlatform->id,
    'minimum_build_number' => 220,
    'recommended_build_number' => 221,
]);
```

## Policy Properties

| Property | Type | Description |
|----------|------|-------------|
| `application_platform_id` | `int` | FK to application_platforms (unique) |
| `minimum_build_number` | `int` | Minimum supported build |
| `recommended_build_number` | `int` | Build that triggers optional updates |

{: .warning }
Each application platform can have only one release policy. Creating a new policy for the same platform will replace the existing one.

## Checking Build Status

### Using the Facade

```php
// Check if supported
$supported = AppReleaseManager::isSupported('paline', 'android', 219);
// false — below minimum

// Check if update required
$requiresUpdate = AppReleaseManager::requiresUpdate('paline', 'android', 220);
// true — meets minimum but below recommended

// Get full status
$status = AppReleaseManager::checkBuild('paline', 'android', 221);
// "supported" — meets recommended
```

### Status Results

| Build vs Policy | `isSupported` | `requiresUpdate` | `checkBuild` |
|-----------------|---------------|------------------|--------------|
| `build < minimum` | `false` | `false` | `"unsupported"` |
| `build >= minimum` and `< recommended` | `true` | `true` | `"update-available"` |
| `build >= recommended` | `true` | `false` | `"supported"` |
| No policy exists | `true` | `false` | `"supported"` |

## Example Scenarios

### Scenario 1: Force Update

```php
// Policy: minimum = 220, recommended = 221
// Client build: 219

AppReleaseManager::isSupported('paline', 'android', 219);
// false — client must update

AppReleaseManager::checkBuild('paline', 'android', 219);
// "unsupported"
```

Your API should return a force-update response:

```json
{
    "supported": false,
    "requires_update": true,
    "force_update": true
}
```

### Scenario 2: Optional Update

```php
// Policy: minimum = 220, recommended = 221
// Client build: 220

AppReleaseManager::isSupported('paline', 'android', 220);
// true — client is supported

AppReleaseManager::requiresUpdate('paline', 'android', 220);
// true — update available

AppReleaseManager::checkBuild('paline', 'android', 220);
// "update-available"
```

Your API should return an optional-update response:

```json
{
    "supported": true,
    "requires_update": true,
    "force_update": false,
    "latest_version": "2.4.0",
    "latest_build": 221
}
```

### Scenario 3: Up to Date

```php
// Policy: minimum = 220, recommended = 221
// Client build: 221

AppReleaseManager::isSupported('paline', 'android', 221);
// true

AppReleaseManager::requiresUpdate('paline', 'android', 221);
// false

AppReleaseManager::checkBuild('paline', 'android', 221);
// "supported"
```

## Updating Policies

Update the policy when you change version requirements:

```php
$policy = AppReleaseManager::policy('paline', 'android');

$policy->update([
    'minimum_build_number' => 225,
    'recommended_build_number' => 226,
]);
```

{: .note }
Changing the minimum build number will immediately affect all clients below that build. Clients will receive a force-update response on their next check.

## Per-Platform Policies

Each application platform has its own policy, allowing different requirements for Android and iOS:

```php
// Android policy
ReleasePolicy::create([
    'application_platform_id' => $androidPlatform->id,
    'minimum_build_number' => 220,
    'recommended_build_number' => 221,
]);

// iOS policy — different requirements
ReleasePolicy::create([
    'application_platform_id' => $iosPlatform->id,
    'minimum_build_number' => 150,
    'recommended_build_number' => 151,
]);
```

## Querying Policies

```php
// Via facade
$policy = AppReleaseManager::policy('paline', 'android');

// Via Eloquent
use Shaka\AppReleaseManager\Models\ReleasePolicy;

$policy = ReleasePolicy::where('application_platform_id', $appPlatform->id)->first();
```
