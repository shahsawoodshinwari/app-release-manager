---
layout: default
title: API Reference
description: Complete API reference for the AppReleaseManager facade
nav_order: 5
---

# API Reference
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Service Class

**Namespace:** `Shaka\AppReleaseManager`

**File:** `src/AppReleaseManager.php`

All methods are accessible through the `AppReleaseManager` facade.

---

## application

```php
application(string $slug): ?Application
```

Look up an application by its slug.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$slug` | `string` | The application slug |

**Returns:** `Application|null`

**Example:**

```php
$application = AppReleaseManager::application('paline');

if ($application) {
    echo $application->name; // "Paline"
}
```

---

## platform

```php
platform(string $slug): ?Platform
```

Look up a platform by its slug.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$slug` | `string` | The platform slug |

**Returns:** `Platform|null`

**Example:**

```php
$platform = AppReleaseManager::platform('android');
```

---

## applicationPlatform

```php
applicationPlatform(
    string $applicationSlug,
    string $platformSlug
): ?ApplicationPlatform
```

Look up the connection between an application and a platform.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |

**Returns:** `ApplicationPlatform|null`

**Example:**

```php
$appPlatform = AppReleaseManager::applicationPlatform('paline', 'android');

if ($appPlatform) {
    echo $appPlatform->identifier; // "com.paline.app"
}
```

---

## policy

```php
policy(
    string $applicationSlug,
    string $platformSlug
): ?ReleasePolicy
```

Retrieve the release policy for an application platform.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |

**Returns:** `ReleasePolicy|null`

**Example:**

```php
$policy = AppReleaseManager::policy('paline', 'android');

if ($policy) {
    echo $policy->minimum_build_number;    // 220
    echo $policy->recommended_build_number; // 221
}
```

---

## latestRelease

```php
latestRelease(
    string $applicationSlug,
    string $platformSlug,
    ?string $channelSlug = null
): ?Release
```

Get the latest release for an application platform, optionally filtered by distribution channel.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |
| `$channelSlug` | `string|null` | Optional distribution channel slug |

**Returns:** `Release|null`

**Example:**

```php
// Latest release across all channels
$release = AppReleaseManager::latestRelease('paline', 'android');

// Latest release on a specific channel
$release = AppReleaseManager::latestRelease('paline', 'android', 'google-play');

if ($release) {
    echo $release->version;       // "2.4.0"
    echo $release->build_number;  // 220
}
```

---

## latestPublishedDistribution

```php
latestPublishedDistribution(
    string $applicationSlug,
    string $platformSlug,
    string $channelSlug
): ?ReleaseDistribution
```

Get the latest published distribution for a specific channel.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |
| `$channelSlug` | `string` | The distribution channel slug |

**Returns:** `ReleaseDistribution|null`

**Example:**

```php
$distribution = AppReleaseManager::latestPublishedDistribution(
    'paline',
    'android',
    'google-play'
);

if ($distribution) {
    echo $distribution->store_version;      // "2.4.0"
    echo $distribution->store_build_number;  // 220
    echo $distribution->store_url;           // "https://play.google.com/..."
}
```

---

## isSupported

```php
isSupported(
    string $applicationSlug,
    string $platformSlug,
    int $buildNumber
): bool
```

Determine if a client build meets the minimum supported build number.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |
| `$buildNumber` | `int` | The client's build number |

**Returns:** `bool`

**Logic:**

| Condition | Result |
|-----------|--------|
| No policy exists | `true` |
| `buildNumber >= minimum_build_number` | `true` |
| `buildNumber < minimum_build_number` | `false` |

**Example:**

```php
// Policy: minimum_build_number = 220

AppReleaseManager::isSupported('paline', 'android', 220); // true
AppReleaseManager::isSupported('paline', 'android', 221); // true
AppReleaseManager::isSupported('paline', 'android', 219); // false
```

---

## requiresUpdate

```php
requiresUpdate(
    string $applicationSlug,
    string $platformSlug,
    int $buildNumber
): bool
```

Determine if a client build needs an optional update.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |
| `$buildNumber` | `int` | The client's build number |

**Returns:** `bool`

**Logic:**

| Condition | Result |
|-----------|--------|
| No policy exists | `false` |
| `buildNumber >= recommended_build_number` | `false` |
| `buildNumber >= minimum_build_number` but `< recommended` | `true` |
| `buildNumber < minimum_build_number` | `false` |

**Example:**

```php
// Policy: minimum = 220, recommended = 221

AppReleaseManager::requiresUpdate('paline', 'android', 221); // false
AppReleaseManager::requiresUpdate('paline', 'android', 220); // true
AppReleaseManager::requiresUpdate('paline', 'android', 219); // false (unsupported)
```

---

## checkBuild

```php
checkBuild(
    string $applicationSlug,
    string $platformSlug,
    int $buildNumber
): string
```

Get the full build status as a string. This is the core method used internally by `isSupported` and `requiresUpdate`.

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$applicationSlug` | `string` | The application slug |
| `$platformSlug` | `string` | The platform slug |
| `$buildNumber` | `int` | The client's build number |

**Returns:** `string`

**Return Values:**

| Value | Meaning |
|-------|---------|
| `"supported"` | Build meets or exceeds the recommended build |
| `"update-available"` | Build meets minimum but is below recommended |
| `"unsupported"` | Build is below the minimum supported build |

**Example:**

```php
// Policy: minimum = 220, recommended = 221

AppReleaseManager::checkBuild('paline', 'android', 221); // "supported"
AppReleaseManager::checkBuild('paline', 'android', 220); // "update-available"
AppReleaseManager::checkBuild('paline', 'android', 219); // "unsupported"
```

---

## Models

### Application

| Property | Type | Description |
|----------|------|-------------|
| `name` | `string` | Application name |
| `slug` | `string` | Unique slug identifier |
| `description` | `string|null` | Optional description |
| `is_active` | `bool` | Whether the application is active |

**Relationships:**

- `applicationPlatforms()` — HasMany ApplicationPlatform
- `platforms()` — BelongsToMany Platform

### ApplicationPlatform

| Property | Type | Description |
|----------|------|-------------|
| `application_id` | `int` | FK to applications |
| `platform_id` | `int` | FK to platforms |
| `identifier` | `string` | Platform identifier (e.g., `com.paline.app`) |
| `is_active` | `bool` | Whether active |

**Relationships:**

- `application()` — BelongsTo Application
- `platform()` — BelongsTo Platform
- `releases()` — HasMany Release
- `applicationDistributionChannels()` — HasMany ApplicationDistributionChannel
- `releasePolicy()` — HasMany ReleasePolicy

### Release

| Property | Type | Description |
|----------|------|-------------|
| `application_platform_id` | `int` | FK to application_platforms |
| `release_type_id` | `int` | FK to release_types |
| `version` | `string` | Semantic version (e.g., `2.4.0`) |
| `build_number` | `int` | Numeric build number |
| `title` | `string|null` | Release title |
| `release_notes` | `string|null` | Release notes |
| `released_at` | `datetime|null` | Release timestamp |
| `is_active` | `bool` | Whether active |

**Scopes:**

- `scopeActive()` — Only active releases
- `scopeReleased()` — Only released (released_at is not null and in the past)

### ReleasePolicy

| Property | Type | Description |
|----------|------|-------------|
| `application_platform_id` | `int` | FK to application_platforms |
| `minimum_build_number` | `int` | Minimum supported build |
| `recommended_build_number` | `int` | Recommended build for optional updates |

### ReleaseDistribution

| Property | Type | Description |
|----------|------|-------------|
| `release_id` | `int` | FK to releases |
| `application_distribution_channel_id` | `int` | FK to application_distribution_channels |
| `status_id` | `int` | FK to release_distribution_statuses |
| `store_version` | `string|null` | Version on the store |
| `store_build_number` | `int|null` | Build number on the store |
| `store_url` | `string|null` | URL to the store listing |
| `published_at` | `datetime|null` | When published |
