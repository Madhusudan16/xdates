<?php

return [
    'driver'          => 'bitly',
    'google'          => [
        'apikey' => env('URL_SHORTENER_GOOGLE_API_KEY', ''),
    ],
    'bitly'           => [
        'username' => 'XdateDev',
        'password' => 'Test@Dev990',
    ],
    'connect_timeout' => 2,
    'timeout'         => 2,
];
