<?php

return [
    'method' => env('AES_METHOD'),
    'key' => env('AES_KEY'),
    'options' => [
        'encrypt' => env('AES_ENCRYPT_OPTION'),
        'decrypt' => env('AES_DECRYPT_OPTION'),
    ],
    'iv' => env('AES_IV'),
];
