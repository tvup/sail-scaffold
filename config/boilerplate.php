<?php

return [
    'docker_image' => env('BOILERPLATE_DOCKER_IMAGE', 'laravelsail/php85-composer:latest'),

    'placeholders' => [
        ':APP_NAME:' => [
            'description' => 'The project name from the URL',
            'resolve' => 'appName',
        ],
        ':SERVICES:' => [
            'description' => 'Comma-separated list of Sail services',
            'resolve' => 'servicesString',
        ],
    ],
];
