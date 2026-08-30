---
layout: default
title: Home
description: Laravel package for mobile app release management, version checking, distribution channels, and force updates for Android and iOS.
nav_order: 1
---

# Laravel App Release Manager
{: .fs-9 }

Manage mobile app releases, versions, distribution channels, and force updates from your Laravel backend.
{: .fs-6 .fw-300 }

[Get Started](#installation){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 }
[View on GitHub](https://github.com/shahsawoodshinwari/app-release-manager){: .btn .fs-5 .mb-4 .mb-md-0 }

---

[![Latest Version](https://img.shields.io/packagist/v/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)
[![Downloads](https://img.shields.io/packagist/dt/shaka/app-release-manager.svg?style=flat-square)](https://packagist.org/packages/shaka/app-release-manager)
[![Tests](https://github.com/shahsawoodshinwari/app-release-manager/actions/workflows/run-tests.yml/badge.svg)](https://github.com/shahsawoodshinwari/app-release-manager/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Code Style](https://github.com/shahsawoodshinwari/app-release-manager/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/shahsawoodshinwari/app-release-manager/actions?query=workflow%3A%22Fix+PHP+code+style+issues%22+branch%3Amaster)

---

## What is App Release Manager?

App Release Manager provides a centralized release-management layer for Laravel applications. Instead of hard-coding release policies inside your mobile application, your Laravel backend can determine whether a client is supported or requires an update.

## Features

- **Multi-application support** — Manage releases for multiple applications from one Laravel installation
- **Android & iOS support** — Manage versions and build numbers for both platforms
- **Distribution channels** — Track releases across Google Play, Apple App Store, GitHub, and custom channels
- **Release policies** — Define minimum supported versions and build numbers
- **Force updates** — Determine when an application version must be updated
- **Semantic versioning** — Compare versions using semantic version rules
- **Database-backed** — Store all release information and policies in your database

## Common Use Cases

### Force Update a Mobile App

Define a minimum supported build and require clients running older versions to update.

### App Version Checking

Check the version or build number reported by Android and iOS clients against your release policy.

### Distribution Channel Tracking

Track releases across channels such as Google Play, Apple App Store, GitHub, or custom distribution channels.

### Multiple Applications

Manage releases for several applications from a single Laravel installation.

## Requirements

- PHP ^8.1
- Laravel ^10.0

## Installation

```bash
composer require shaka/app-release-manager
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="app-release-manager-migrations"
php artisan migrate
```

Seed the reference data:

```bash
php artisan arm:seed-reference-data
```

## Quick Start

```php
use Shaka\AppReleaseManager\Facades\AppReleaseManager;

// Check if a client build is supported
$supported = AppReleaseManager::isSupported('paline', 'android', '220');

// Check if an update is required
$requiresUpdate = AppReleaseManager::requiresUpdate('paline', 'android', '219');
```

## Example API Response

```json
{
    "supported": false,
    "requires_update": true,
    "minimum_version": "2.4.0",
    "minimum_build": 220
}
```

## Architecture

```text
Application
    |
    +-- Application Platform
            |
            +-- Release Policy
            |
            +-- Releases
                    |
                    +-- Release Distributions
                            |
                            +-- Distribution Channel
```
