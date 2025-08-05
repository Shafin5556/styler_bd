<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        'asset' => [
            'driver' => 'local',
            'root' => base_path('asset'),
            'url' => env('APP_URL').'/asset',
            'visibility' => 'public',
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('asset') => base_path('asset'),
    ],
];