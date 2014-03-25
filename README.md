# Doctrine for Laravel

Doctrine implementation for Laravel 4

## Installation

Begin by installing the package through Composer. Edit your project's `composer.json` file to require `mitch/laravel-doctrine`.

  ```php
  "require": {
    "mitch/laravel-doctrine": "0.1.x"
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
  php artisan config:publish mitch/laravel-doctrine
  ```

## How It Works

This package gives you possibility to access the entity manager through the `EntityManager` facade.

  ```php
  $product = new Product;
  $product->setName('thinkpad');
  $product->setPrice(1200);

  EntityManager::persist($product);
  EntityManager::flush();
  ```

For the full documentation on Doctrine [check out their docs](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html)

## Commands

This package provides three artisan commands for your schema:

    * doctrine:schema:create - Create database schema from models
    * doctrine:schema:update - Update database schema to match models
    * doctrine:schema:drop   - Drop database schema
