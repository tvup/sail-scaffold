<?php

return [
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
