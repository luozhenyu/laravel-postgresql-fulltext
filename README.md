laravel-postgresql-fulltext
================

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)

Add fulltext and inherited table support to postgresql

## Installation
[PHP](https://php.net) **5.6.4+** and [Laravel](https://laravel.com) **5.4+** are required.

Run the following command to install this package via composer

```shell
composer require "luozhenyu/laravel-postgresql-fulltext"
```

Then you need to register the service provider. Open `config/app.php` and add the following to the `providers` array.

```php
LuoZhenyu\PostgresFullText\PostgresqlSchemaServiceProvider::class
```

## Usage

When using a postgresql database, you can use the method `inherits()`:

```php
Schema::create('users', function(Blueprint $table) {
  $table->increments('id');
  $table->string('name');
  $table->integer('age');
});

Schema::create('admins', function(Blueprint $table) {
    $table->string('permission');
    
    $table->inherits('users');//table admins inherit table users
});
```

Also you can use the method `fulltext()` and `dropFulltext()`.

```php
Schema::create('articles', function(Blueprint $table) {
  $table->increments('id');
  $table->string('title');
  $table->text('content');
  
  $table->fulltext(['title', 'content']);
  
  $table->dropFulltext('articles_title_content_fulltext');
});
```

And a simple query method is also given in Eloquent ORM using fulltext index.

```php

use LuoZhenyu\PostgresFullText\FulltextBuilder;

...

$fulltext = new FulltextBuilder(['title', 'content']);

$articles = Article::where($fulltext->search('any keyword'))->get();
```
