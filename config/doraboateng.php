<?php
/**
 *
 */

return [

    // API client credentials
    'id'        => env('DORA_BOATENG_ID'),
    'secret'    => env('DORA_BOATENG_SECRET'),

    // API host
    'host'      => env('DORA_BOATENG_HOST', 'https://api.doraboateng.com'),

    // API timeout
    'timeout'   => env('DORA_BOATENG_TIMEOUT', 4.0),

];
