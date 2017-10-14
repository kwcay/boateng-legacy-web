<?php

return [
    'keen' => [
        'id'     => env('KEEN_PROJECT_ID'),
        'master' => env('KEEN_MASTER_KEY'),
        'write'  => env('KEEN_WRITE_KEY'),
        'addons' => [
            'geo-data'  => 1,   // IP to Geo parser
            'user-data' => 1,   // User Agent parser
        ],
    ],
];
