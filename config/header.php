<?php

$app = array();

$app['config'] = array(
    'db' => array(
        'dsn'      => 'mysql:dbname=bugmanager;host=localhost',
        'user'      => 'root',
        'password'  => ''
    ),
    'url' => $_SERVER['PHP_SELF'],
    'debug' => false
);



if($app['config']['debug']):
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
endif;


$app['locale'] = 'en';
