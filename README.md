# Doctrine 2 for Laravel

[![Latest Stable Version](https://poser.pugx.org/mitch/laravel-doctrine/version.png)](https://packagist.org/packages/mitch/laravel-doctrine)
[![License](https://poser.pugx.org/mitch/laravel-doctrine/license.png)](https://packagist.org/packages/mitch/laravel-doctrine)
[![Total Downloads](https://poser.pugx.org/mitch/laravel-doctrine/downloads.png)](https://packagist.org/packages/mitch/laravel-doctrine)

A Doctrine 2 implementation that melts with Laravel 4.

## Documentation

Begin reading [the full documentation](https://github.com/mitchellvanw/laravel-doctrine/wiki) here or go to a specific chapter right away.

1. [Installation](https://github.com/mitchellvanw/laravel-doctrine/wiki/Installation)
2. [How It Works](https://github.com/mitchellvanw/laravel-doctrine/wiki/How-It-Works)
  1. [Basics](https://github.com/mitchellvanw/laravel-doctrine/wiki/Basics)
  2. [Entity Manager](https://github.com/mitchellvanw/laravel-doctrine/wiki/Entity-Manager)
  3. [Timestamps](https://github.com/mitchellvanw/laravel-doctrine/wiki/Timestamps)
  4. [Soft Deleting](https://github.com/mitchellvanw/laravel-doctrine/wiki/Soft-Deleting)
  5. [Authentication](https://github.com/mitchellvanw/laravel-doctrine/wiki/Authentication)
3. [Schemas](https://github.com/mitchellvanw/laravel-doctrine/wiki/Schemas)
4. [Doctrine Configuration](https://github.com/mitchellvanw/laravel-doctrine/wiki/Doctrine-Configuration)
  1. [Metadata Configuration](https://github.com/mitchellvanw/laravel-doctrine/wiki/Metadata-Configuration)
  2. [Annotation Reader](https://github.com/mitchellvanw/laravel-doctrine/wiki/Annotation-Reader)
  3. [Metadata](https://github.com/mitchellvanw/laravel-doctrine/wiki/Metadata)
5. [MIT License](https://github.com/mitchellvanw/laravel-doctrine/blob/master/LICENSE)

## Installation

Begin by installing the package through Composer. Edit your project's `composer.json` to require `mitch/laravel-doctrine`.

> This package is still in it's early stages, but fully functional. Is it possible that the API might change slightly, no drastic changes.

```php
"require": {
    "mitch/laravel-doctrine": "0.*"
}
```

Next use Composer to update your project from the the Terminal:

```php
php composer.phar update
```

Once the package has been installed you'll need to add the service provider. Open your `app/config/app.php` configuration file, and add a new item to the `providers` array.

```php
'Mitch\LaravelDoctrine\LaravelDoctrineServiceProvider'
```

After This you'll need to add the facade. Open your `app/config/app.php` configuration file, and add a new item to the `aliases` array.

```php
'EntityManager' => 'Mitch\LaravelDoctrine\EntityManagerFacade'
```

It's recommended to publish the package configuration.

```php
php artisan config:publish mitch/laravel-doctrine --path=vendor/mitch/laravel-doctrine/config
```

## 2 Minutes

This package uses the Laravel database configuration and thus it works right out of the box. With the [Entity Manager](https://github.com/mitchellvanw/laravel-doctrine/wiki/Entity-Manager) facade (or service locator) you can interact with repositories.
It might be wise to [check out the Doctrine 2 docs](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html) to know how it works.
The little example below shows how to use the EntityManager in it simplest form.

> This package currently only supports MySQL. Other drivers will be added soon.

```php
<?php

$user = new User;
$user->setName('Mitchell');

EntityManager::persist($user);
EntityManager::flush();
```

The `User` used in the example above looks like this.

```php
<?php

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
```

If you've only used Eloquent and its models this might look bloated or frightening, but it's actually very simple. Let me break the class down.

```php
<?php

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
}
```

The only thing that's actually important in this `entity` are the properties. This shows you which data the `entity` holds.

With Doctrine 2 you can't interact with database by using the entity `User`. You'll have to use [Entity Manager](https://github.com/mitchellvanw/laravel-doctrine/wiki/Entity-Manager) and `repositories`.
This does create less overhead since your entities aren't extending the whole Eloquent `model` class. Which can dramatically slow down your application a lot if you're working with thousands or millions of records.

## Caveats

At the moment Doctrine\ORM version 2.5 is still in beta. As a result the composer install may require you to change
the `minimum-stability` in your `composer.json` to `dev`.

## License

This package is licensed under the [MIT license](https://github.com/mitchellvanw/laravel-doctrine/blob/master/LICENSE).
