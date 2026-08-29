<?php

// config for Shaka/AppReleaseManager
return [

    /*
     * Reference data seeded by the ReferenceDataSeeder.
     *
     * Override these values in your app's config to customise the
     * platforms, channels, release types and distribution statuses
     * that ship with the package.
     */
    'reference_data' => [

        'platforms' => [
            'Android',
            'iOS',
            'macOS',
            'watchOS',
            'Windows',
            'Linux',
            'Web',
        ],

        'distribution_channel_types' => [
            'Store',
            'Direct',
            'Enterprise',
            'Internal',
        ],

        'distribution_channels' => [
            ['name' => 'Google Play', 'slug' => 'google-play', 'type' => 'Store'],
            ['name' => 'Apple App Store', 'slug' => 'apple-app-store', 'type' => 'Store'],
            ['name' => 'Huawei AppGallery', 'slug' => 'huawei-appgallery', 'type' => 'Store'],
            ['name' => 'Microsoft Store', 'slug' => 'microsoft-store', 'type' => 'Store'],
            ['name' => 'Direct Download', 'slug' => 'direct-download', 'type' => 'Direct'],
            ['name' => 'Firebase App Distribution', 'slug' => 'firebase-app-distribution', 'type' => 'Internal'],
        ],

        'release_types' => [
            'Major',
            'Minor',
            'Feature',
            'Patch',
            'Hotfix',
        ],

        'release_distribution_statuses' => [
            'Draft',
            'Submitted',
            'In Review',
            'Published',
            'Rejected',
            'Withdrawn',
        ],

    ],

];
