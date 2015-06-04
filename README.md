#Single-file bug tracker for team of one

[![Build Status](https://img.shields.io/travis/serkin/bugmanager.svg?style=flat-square)](https://travis-ci.org/serkin/parser)
[![Coverage Status](https://img.shields.io/coveralls/serkin/bugmanager/master.svg?style=flat-square)](https://coveralls.io/r/serkin/bugmanager?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/serkin/bugmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/serkin/bugmanager/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/serkin/bugmanager/v/stable)](https://packagist.org/packages/serkin/bugmanager)
[![Total Downloads](https://poser.pugx.org/serkin/bugmanager/downloads)](https://packagist.org/packages/serkin/bugmanager)
[![Latest Unstable Version](https://poser.pugx.org/serkin/bugmanager/v/unstable)](https://packagist.org/packages/serkin/bugmanager)
[![License](https://poser.pugx.org/serkin/bugmanager/license)](https://packagist.org/packages/serkin/bugmanager)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0d88e0a6-f1ec-4151-9f22-8af9e921b574/small.png)](https://insight.sensiolabs.com/projects/0d88e0a6-f1ec-4151-9f22-8af9e921b574)


To get started you need:
* Created database and user in your MySQL server 
* Import db schema from `dump.sql` to your db
* Copy `bugmanager.php` to your server. You can rename it if you want
* Adjust db settings in `bugmanager.php`

## Adjusting settings
Open `bugmanager.php` and change your db credentials
```php
$app['config'] = array(
    'db' => array(
        'dsn'       => 'mysql:dbname=bugmanager;host=localhost',
        'user'      => 'bugmanager',
        'password'  => '*******'
    ),
    'url'           => $_SERVER['PHP_SELF'],
    'debug'         => false,
    'issue_types'   => array(
        array('type' => 'bug'),
        array('type' => 'feature')
    )
);
```
## Screenshots
![Foler](screenshot.png?raw=true "Bugmanager")


## TODO
* Add Russian localization
* Add German localization
* Function tests
* Unit tests
* CI integration

## Licence
* MIT
