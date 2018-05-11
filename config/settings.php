<?php

return [
    'jwt' => [
        'key' => 'secret',
        'header' => [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ],
        'exp' => [
            'access' => env('JWT_EXP_ACCESS', 60),
            'refresh' => env('JWT_EXP_REFRESH', 20160),
            'reset' => env('JWT_EXP_RESET', 60),
        ]
    ],
];