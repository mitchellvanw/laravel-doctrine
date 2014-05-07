# Doctrine for Laravel

A Doctrine implementation that melts with Laravel 4.

## Installation

Begin by installing the package through Composer. Edit your project's `composer.json` file to require `mitch/laravel-doctrine`.

> This package is still in it's early stages, but fully functional. Is it possible that the API might change slightly, no drastic changes.

  ```php
  "require": {
    "mitch/laravel-doctrine": "0.x"
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

## How It Works

### Basics

This package uses the Laravel database configuration and thus it works right out of the box. With the `EntityManager` facade (or service locator) you can interact with repositories.
It might be wise to [check out the Doctrine docs](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html) to know how it works.
The little example below shows how to use the EntityManager in it simplest form.

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

  /**
   * @Entity
   * @Table(name="users")
   */
  class User
  {
      /**
       * @Id
       * @GeneratedValue
       * @Column(type="integer")
       */
      private $id;

      /**
       * @Column(type="string")
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

  /**
   * @Entity
   * @Table(name="users")
   */
  class User
  {
      /**
       * @Id
       * @GeneratedValue
       * @Column(type="integer")
       */
      private $id;

      /**
       * @Column(type="string")
       */
      private $name;
  }
  ```

The only thing that's actually important in this `entity` are the properties. This shows you which data the `entity` holds.

With Doctrine you can't interact with database by using the entity `User`. You'll have to use `EntityManager` and `repositories`.
This does create less overhead since your entities aren't extending the whole Eloquent `model` class. Which can dramatically slow down your application a lot if you're working with thousands or millions of records.

### Timestamps

Doctrine doesn't support timestamps `(created_at & updated_at)` out of the box. That's why I've added a trait `Timestamps` which add this functionality without a sweat.
When you add this trait and the `@HasLifecycleCallbacks()` annotation to your `entity` managing the timestamps will be done automagically.

> Do remember that the database table of this entity needs to have the `created_at` and `updated_at` columns. If the trait has been added you can simply call the artisan command `doctrine:schema:update` to update your database schema.

  ```php
  <?php

  use Mitch\LaravelDoctrine\Traits\Timestamps;

  /**
   * @Entity
   * @Table(name="users")
   * @HasLifecycleCallbacks()
   */
  class User
  {
      use Timestamps;

      /**
       * @Id
       * @GeneratedValue
       * @Column(type="integer")
       */
      private $id;

      /**
       * @Column(type="string")
       */
      private $name;
  }
  ```

### Soft Deleting

Soft Deletes is also one of those things I've added to this package to work right out of the box. It's the same thing like with `Timestamps`.
Simply add the trait `SoftDeletes`, make sure the database table has the `deleted_at` column and you're good to go!

  ```php
  <?php

  use Mitch\LaravelDoctrine\Traits\SoftDeletes;

  /**
   * @Entity
   * @Table(name="users")
   */
  class User
  {
      use SoftDeletes;

      /**
       * @Id
       * @GeneratedValue
       * @Column(type="integer")
       */
      private $id;

      /**
       * @Column(type="string")
       */
      private $name;
  }
  ```

## Schemas

Doctrine using entities for its migrations. This means that an entity represents the current state of that database table.
This package provides three artisan commands to create, update and drop your database schema.

    * doctrine:schema:create - Create database schema from models
    * doctrine:schema:update - Update database schema to match models
    * doctrine:schema:drop   - Drop database schema

It's possible to get the SQL that Doctrine was going to execute by using `--sql` when calling the command.

## License

The MIT License (MIT)

Copyright (c) 2014 Mitchell van Wijngaarden (mitchell@kooding.nl)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
