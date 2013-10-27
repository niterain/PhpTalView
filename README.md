Allows you to use [PHPTAL](http://www.phptal.org) seamlessly in [Laravel4](http://www.laravel.com)

Installation
============

Add `niterain\PhpTalView` as a requirement to composer.json:

```javascript
{
    "require": {
        "niterain/php-tal-view": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register PhpTalView with Laravel. Open up app/config/app.php and find the providers key towards the bottom and add:

```php
'PhpTalView\View\ViewServiceProvider',
```

Configuration
=============

Currently PhpTalView is set by default to HTML5, however, you can change this setting in its config.php file, XHTML, or XML.

Usage
=====

You call the PhpTalView template like you would any other view:

```php
View::make('hello', array(...))
```

Filters
==========

PHPTAL also has a nice feature which you can use called filters, for example I have one that bust the cache for images, js, css, this is configurable via the config file.

```php
'preFilters' => array(
    'bust_cache',
    'minimize_js'
)
```
