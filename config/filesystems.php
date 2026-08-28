<?php

/*
    ----------------------------------------------------------
    Perilaku penyimpanan
    ----------------------------------------------------------
    Secara lokal (dev/test) aplikasi memakai filesystem lokal.
    Saat kredensial S3 (AWS_*) terisi, disk `local`, `private`,
    dan `public` otomatis beralih ke object storage S3-compatible
    (AWS S3 / Cloudflare R2 / MinIO) — diperlukan karena filesystem
    lokal bersifat read-only pada deployment serverless (Vercel).
*/

$useS3 = (bool) (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY') && env('AWS_BUCKET'));

// Konfigurasi disk S3-compatible (dipakai untuk disk private & public saat $useS3).
$s3Disk = [
    'driver'                  => 's3',
    'key'                     => env('AWS_ACCESS_KEY_ID'),
    'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
    'region'                  => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket'                  => env('AWS_BUCKET'),
    'url'                     => env('AWS_URL'),
    'endpoint'                => env('AWS_ENDPOINT'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'bucket_endpoint'         => env('AWS_BUCKET_ENDPOINT', false),
    'throw'                   => false,
    'report'                  => false,
];

// Konfigurasi disk local (dev/test), disesuaikan agar sendirinya memakai S3 bila aktif.
if ($useS3) {
    $localDisk            = $s3Disk;
    $privateDisk          = $s3Disk + ['visibility' => 'private'];
    $publicDisk           = $s3Disk + ['visibility' => 'public'];
} else {
    $localDisk = [
        'driver' => 'local',
        'root'   => storage_path('app/private'),
        'serve'  => true,
        'throw'  => false,
        'report' => false,
    ];
    $privateDisk = [
        'driver'     => 'local',
        'root'       => storage_path('app/private'),
        'visibility' => 'private',
        'throw'      => false,
        'report'     => false,
    ];
    $publicDisk = [
        'driver'     => 'local',
        'root'       => storage_path('app/public'),
        'url'        => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
        'visibility' => 'public',
        'throw'      => false,
        'report'     => false,
    ];
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', $useS3 ? 's3' : 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => $localDisk,

        'private' => $privateDisk,

        'public' => $publicDisk,

        's3' => $s3Disk + ['visibility' => 'private'],

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
