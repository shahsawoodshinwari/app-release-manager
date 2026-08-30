---
layout: default
title: Usage
description: How to use the AppReleaseManager facade
nav_order: 4
---

# Usage
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## The Facade

Import the facade in your code:

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;
```

All methods are accessed through this facade, which proxies to the `AppReleaseManager` service class.

## Resolve an Application

Look up an application by its slug:

```php
$application = AppReleaseManager::application('paline');
```

Returns an `Application` model or `null` if not found.

## Resolve a Platform

Look up a platform by its slug:

```php
$platform = AppReleaseManager::platform('android');
```

Returns a `Platform` model or `null` if not found.

## Resolve an Application Platform

Look up the connection between an application and a platform:

```php
$appPlatform = AppReleaseManager::applicationPlatform('paline', 'android');
```

Returns an `ApplicationPlatform` model with the platform-specific configuration for that application.

## Get the Release Policy

Retrieve the release policy for an application platform:

```php
$policy = AppReleaseManager::policy('paline', 'android');
```

Returns a `ReleasePolicy` model with `minimum_build_number` and `recommended_build_number`.

## Get the Latest Release

Get the most recent release for an application platform:

```php
$release = AppReleaseManager::latestRelease('paline', 'android');
```

Optionally filter by distribution channel:

```php
$release = AppReleaseManager::latestRelease('paline', 'android', 'google-play');
```

Returns a `Release` model or `null`.

## Get the Latest Published Distribution

Get the most recent published distribution for a specific channel:

```php
$distribution = AppReleaseManager::latestPublishedDistribution(
    'paline',
    'android',
    'google-play'
);
```

Returns a `ReleaseDistribution` model with status `published`.

## Check if a Build is Supported

Determine if a client build meets the minimum requirement:

```php
$supported = AppReleaseManager::isSupported('paline', 'android', '220');
// Returns: true or false
```

### How it works

- If no policy exists → always returns `true`
- If `build_number >= minimum_build_number` → returns `true`
- If `build_number < minimum_build_number` → returns `false`

## Check if an Update is Required

Determine if a client build needs an optional update:

```php
$requiresUpdate = AppReleaseManager::requiresUpdate('paline', 'android', '219');
// Returns: true or false
```

### How it works

- If no policy exists → always returns `false`
- If `build_number >= recommended_build_number` → returns `false`
- If `build_number >= minimum_build_number` but `< recommended_build_number` → returns `true`
- If `build_number < minimum_build_number` → returns `false` (use `isSupported` instead)

## Check Build Status

Get the full build status as a string:

```php
$status = AppReleaseManager::checkBuild('paline', 'android', '219');
// Returns: "supported", "update-available", or "unsupported"
```

### Status values

| Return Value | Meaning |
|--------------|---------|
| `"supported"` | Build meets or exceeds the recommended build |
| `"update-available"` | Build meets minimum but is below recommended |
| `"unsupported"` | Build is below the minimum supported build |

## Complete Example

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;

// Check client status
$buildNumber = 219;
$app = 'paline';
$platform = 'android';

if (!AppReleaseManager::isSupported($app, $platform, $buildNumber)) {
    // Force update required
    return response()->json([
        'supported' => false,
        'requires_update' => true,
        'force_update' => true,
    ], 403);
}

if (AppReleaseManager::requiresUpdate($app, $platform, $buildNumber)) {
    // Optional update available
    $latest = AppReleaseManager::latestRelease($app, $platform);

    return response()->json([
        'supported' => true,
        'requires_update' => true,
        'force_update' => false,
        'latest_version' => $latest?->version,
        'latest_build' => $latest?->build_number,
    ]);
}

// Client is up to date
return response()->json([
    'supported' => true,
    'requires_update' => false,
    'force_update' => false,
]);
```

## Next Steps

- [API Reference](api.md) — Full API documentation for all methods
- [Release Policies](release-policies.md) — Configure version requirements
- [Mobile Integration](mobile-integration.md) — Integrate with mobile apps
