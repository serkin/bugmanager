<?php

$app = [];

$app['config'] = [
    'db' => [
        'dsn' => 'mysql:dbname=bugmanager;host=localhost',
        'user' => 'bugmanager',
        'password' => '',
    ],
    'url' => $_SERVER['PHP_SELF'],
    'debug' => false,
    'issue_types' => [
        ['type' => 'bug'],
        ['type' => 'feature']
    ]
];

if ($app['config']['debug']):
    error_reporting(E_ALL);
endif;

$app['locale'] = 'en';
