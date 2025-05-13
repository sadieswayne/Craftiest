<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'extension' => [
            'driver' => 'local',
            'root'   => base_path('app/Extensions'),
            'throw'  => false,
        ],

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
        ],

        'public' => [
            'driver'     => 'local',
            'root'       => public_path() . '/uploads',
            'url'        => env('APP_URL') . '/public',
            'visibility' => 'public',
        ],

        'uploads' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads',
            'url'    => env('APP_URL') . '/uploads',
            'throw'  => false,
        ],

        'views' => [
            'driver' => 'local',
            'root'   => resource_path('views'),
            'throw'  => false,
        ],

        'thumbs' => [
            'driver' => 'local',
            'root'   => public_path() . '/uploads/thumbnail/default',
            'url'    => env('APP_URL') . '/uploads/thumbnail/default',
            'throw'  => false,
        ],

        'themes' => [
            'driver' => 'local',
            'root'   => public_path('themes'),
            'throw'  => false,
        ],

        'build' => [
            'driver' => 'local',
            'root'   => public_path('build'),
            'throw'  => false,
        ],

        'data' => [
            'driver' => 'local',
            'root'   => public_path('data'),
            'throw'  => false,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => true,
        ],

        'r2' => [
            'driver'                  => 's3',
            'key'                     => env('CLOUDFLARE_R2_ACCESS_KEY_ID'),
            'secret'                  => env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
            'region'                  => env('CLOUDFLARE_R2_DEFAULT_REGION', 'us-east-1'),
            'bucket'                  => env('CLOUDFLARE_R2_BUCKET'),
            'url'                     => env('CLOUDFLARE_R2_URL'),
            'visibility'              => 'private',
            'endpoint'                => env('CLOUDFLARE_R2_ENDPOINT'),
            'use_path_style_endpoint' => env('CLOUDFLARE_R2_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
