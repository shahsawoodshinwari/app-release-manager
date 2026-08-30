---
layout: default
title: Mobile Integration
description: Integrating App Release Manager with mobile applications
nav_order: 9
---

# Mobile Integration
{: .no_toc }

## Table of contents
{: .no_toc .text-delta }

1. TOC
{:toc}

---

## Overview

App Release Manager is a backend-only package. It does not include mobile SDKs or client libraries. Integration is done through your Laravel API, which the mobile application calls to check its release status.

## How It Works

```
Mobile App                    Laravel API                  App Release Manager
    |                              |                              |
    |  GET /api/release-check      |                              |
    |  { app, platform, build }    |                              |
    |------------------------------->                              |
    |                              |  isSupported()               |
    |                              |  requiresUpdate()            |
    |                              |------------------------------->|
    |                              |                              |
    |                              |  <--- status + metadata      |
    |                              |<------------------------------|
    |  <--- JSON response          |                              |
    |<-------------------------------                              |
    |                              |                              |
    |  Show update screen?         |                              |
```

## Creating the API Endpoint

Create a route and controller in your Laravel application:

### routes/api.php

```php
use App\Http\Controllers\ReleaseCheckController;

Route::post('/release-check', [ReleaseCheckController::class, 'check']);
```

### app/Http/Controllers/ReleaseCheckController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Shaka\AppReleaseManager\Facades\AppReleaseManager;

class ReleaseCheckController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'app' => 'required|string',
            'platform' => 'required|string|in:android,ios',
            'build_number' => 'required|integer|min:1',
        ]);

        $app = $request->input('app');
        $platform = $request->input('platform');
        $buildNumber = (int) $request->input('build_number');

        // Verify the application exists
        $application = AppReleaseManager::application($app);
        if (!$application) {
            return response()->json([
                'error' => 'Application not found',
            ], 404);
        }

        // Verify the platform exists
        $appPlatform = AppReleaseManager::applicationPlatform($app, $platform);
        if (!$appPlatform) {
            return response()->json([
                'error' => 'Platform not found for this application',
            ], 404);
        }

        // Check build status
        $supported = AppReleaseManager::isSupported($app, $platform, $buildNumber);
        $requiresUpdate = AppReleaseManager::requiresUpdate($app, $platform, $buildNumber);

        // Get policy info
        $policy = AppReleaseManager::policy($app, $platform);

        // Get latest release
        $latestRelease = AppReleaseManager::latestRelease($app, $platform);

        $response = [
            'supported' => $supported,
            'requires_update' => $requiresUpdate,
            'force_update' => !$supported,
            'minimum_build' => $policy?->minimum_build_number,
            'recommended_build' => $policy?->recommended_build_number,
        ];

        if ($latestRelease) {
            $response['latest_version'] = $latestRelease->version;
            $response['latest_build'] = $latestRelease->build_number;
            $response['release_notes'] = $latestRelease->release_notes;
        }

        return response()->json($response);
    }
}
```

## API Response Format

### Supported — No Update

```json
{
    "supported": true,
    "requires_update": false,
    "force_update": false,
    "minimum_build": 220,
    "recommended_build": 221,
    "latest_version": "2.4.0",
    "latest_build": 221,
    "release_notes": "Bug fixes and improvements."
}
```

### Supported — Update Available

```json
{
    "supported": true,
    "requires_update": true,
    "force_update": false,
    "minimum_build": 220,
    "recommended_build": 221,
    "latest_version": "2.4.0",
    "latest_build": 221,
    "release_notes": "Bug fixes and improvements."
}
```

### Unsupported — Force Update

```json
{
    "supported": false,
    "requires_update": true,
    "force_update": true,
    "minimum_build": 220,
    "recommended_build": 221,
    "latest_version": "2.4.0",
    "latest_build": 221,
    "release_notes": "Bug fixes and improvements."
}
```

## Mobile Client Implementation

### Flutter

```dart
class ReleaseCheckService {
  final String apiUrl = 'https://your-api.com/api/release-check';

  Future<ReleaseStatus> checkRelease({
    required String app,
    required String platform,
    required int buildNumber,
  }) async {
    final response = await http.post(
      Uri.parse(apiUrl),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'app': app,
        'platform': platform,
        'build_number': buildNumber,
      }),
    );

    if (response.statusCode == 200) {
      return ReleaseStatus.fromJson(jsonDecode(response.body));
    }

    throw Exception('Failed to check release status');
  }
}

class ReleaseStatus {
  final bool supported;
  final bool requiresUpdate;
  final bool forceUpdate;
  final String? latestVersion;
  final int? latestBuild;
  final String? releaseNotes;

  ReleaseStatus({
    required this.supported,
    required this.requiresUpdate,
    required this.forceUpdate,
    this.latestVersion,
    this.latestBuild,
    this.releaseNotes,
  });

  factory ReleaseStatus.fromJson(Map<String, dynamic> json) {
    return ReleaseStatus(
      supported: json['supported'],
      requiresUpdate: json['requires_update'],
      forceUpdate: json['force_update'],
      latestVersion: json['latest_version'],
      latestBuild: json['latest_build'],
      releaseNotes: json['release_notes'],
    );
  }
}
```

### React Native

```javascript
const checkRelease = async (app, platform, buildNumber) => {
  const response = await fetch('https://your-api.com/api/release-check', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      app,
      platform,
      build_number: buildNumber,
    }),
  });

  if (!response.ok) {
    throw new Error('Failed to check release status');
  }

  return response.json();
};

// Usage
const status = await checkRelease('paline', 'android', 219);

if (status.force_update) {
  // Show mandatory update screen
} else if (status.requires_update) {
  // Show optional update prompt
}
```

### Native Android (Kotlin)

```kotlin
data class ReleaseStatus(
    val supported: Boolean,
    val requiresUpdate: Boolean,
    val forceUpdate: Boolean,
    val latestVersion: String?,
    val latestBuild: Int?,
    val releaseNotes: String?
)

suspend fun checkRelease(
    app: String,
    platform: String,
    buildNumber: Int
): ReleaseStatus {
    val client = OkHttpClient()
    val json = JSONObject().apply {
        put("app", app)
        put("platform", platform)
        put("build_number", buildNumber)
    }

    val body = json.toString().toRequestBody("application/json".toMediaType())
    val request = Request.Builder()
        .url("https://your-api.com/api/release-check")
        .post(body)
        .build()

    val response = client.newCall(request).execute()
    val result = JSONObject(response.body?.string() ?: "")

    return ReleaseStatus(
        supported = result.getBoolean("supported"),
        requiresUpdate = result.getBoolean("requires_update"),
        forceUpdate = result.getBoolean("force_update"),
        latestVersion = result.optString("latest_version"),
        latestBuild = result.optInt("latest_build"),
        releaseNotes = result.optString("release_notes")
    )
}
```

### Native iOS (Swift)

```swift
struct ReleaseStatus: Codable {
    let supported: Bool
    let requiresUpdate: Bool
    let forceUpdate: Bool
    let latestVersion: String?
    let latestBuild: Int?
    let releaseNotes: String?
}

func checkRelease(
    app: String,
    platform: String,
    buildNumber: Int,
    completion: @escaping (Result<ReleaseStatus, Error>) -> Void
) {
    let url = URL(string: "https://your-api.com/api/release-check")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.setValue("application/json", forHTTPHeaderField: "Content-Type")

    let body: [String: Any] = [
        "app": app,
        "platform": platform,
        "build_number": buildNumber
    ]
    request.httpBody = try? JSONSerialization.data(withJSONObject: body)

    URLSession.shared.dataTask(with: request) { data, response, error in
        guard let data = data,
              let status = try? JSONDecoder().decode(ReleaseStatus.self, from: data) else {
            completion(.failure(error ?? NSError()))
            return
        }
        completion(.success(status))
    }.resume()
}
```

## Integration Checklist

1. Create a Laravel API endpoint that calls the facade methods
2. Validate the incoming request (app, platform, build_number)
3. Call `isSupported()` and `requiresUpdate()` to determine status
4. Return a JSON response with the status and metadata
5. Handle the response in your mobile app:
   - `force_update = true` → Show mandatory update screen
   - `requires_update = true` → Show optional update prompt
   - `supported = true` → Continue normally
6. Optionally include a "Check for Updates" button that calls the API

## Best Practices

- **Cache responses** — The release status doesn't change frequently. Consider caching API responses for a short period.
- **Handle errors gracefully** — If the API is unreachable, allow the app to continue with its current version.
- **Store the response locally** — Save the last known status so the app can make decisions even offline.
- **Use HTTPS** — Always use HTTPS for your release check API.
- **Version the API** — Consider versioning your API endpoint (e.g., `/api/v1/release-check`) for future changes.
