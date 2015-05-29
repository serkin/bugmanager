#Single-file bug tracker for team of one

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
