<?php

return [
    'keen' => [
        'id'     => env('KEEN_PROJECT_ID'),
        'master' => env('KEEN_MASTER_KEY'),
        'write'  => env('KEEN_WRITE_KEY'),
        'addons' => [
            'ip_to_geo'       => true,
            'ua_parser'       => true,
            'referrer_parser' => true,
        ],
    ],
];
