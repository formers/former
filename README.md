# Former
## A Laravelish way to create and format forms

[![Build Status](http://img.shields.io/travis/formers/former.svg?style=flat)](https://travis-ci.org/formers/former)
[![Latest Stable Version](http://img.shields.io/packagist/v/anahkiasen/former.svg?style=flat)](https://packagist.org/packages/anahkiasen/former)
[![Total Downloads](http://img.shields.io/packagist/dt/anahkiasen/former.svg?style=flat)](https://packagist.org/packages/anahkiasen/former)
)

Former outputs form elements in HTML compatible with your favorite CSS framework (Bootstrap and Foundation are currently supported). Former also handles repopulation after validation errors, including automatically rendering error text with affected fields.

### Introduction

Former provides a fluent method of form creation, allowing you to do:

```php
Former::framework('TwitterBootstrap3');

Former::horizontal_open()
  ->id('MyForm')
  ->rules(['name' => 'required'])
  ->method('GET');

  Former::xlarge_text('name') # Bootstrap sizing
    ->class('myclass') # arbitrary attribute support
    ->label('Full name')
    ->value('Joseph')
    ->required() # HTML5 validation
    ->help('Please enter your full name');

  Former::textarea('comments')
    ->rows(10)
    ->columns(20)
    ->autofocus();

  Former::actions()
    ->large_primary_submit('Submit') # Combine Bootstrap directives like "lg and btn-primary"
    ->large_inverse_reset('Reset');

Former::close();
```

Every time you call a method that doesn't actually exist, Former assumes you're trying to set an attribute and creates it magically. That's why you can do in the above example `->rows(10)` ; in case you want to set attributes that contain dashes, just replace them by underscores : `->data_foo('bar')` equals `data-foo="bar"`.
Now of course in case you want to set an attribute that actually contains an underscore you can always use the fallback method `setAttribute('data_foo', 'bar')`. You're welcome.

This is the core of it, but Former offers a lot more. I invite you to consult the wiki to see the extent of what Former does.

-----

### Installation
Require Former package using Composer:

    composer require anahkiasen/former

Publish config files with artisan:
    
    php artisan vendor:publish --provider="Former\FormerServiceProvider"

#### App.php config for Laravel 5.4 and below

For Laravel 5.4 and below, you must modify your `config/app.php`.

In the `providers` array add :

    Former\FormerServiceProvider::class

Add then alias Former's main class by adding its facade to the `aliases` array in the same file :

    'Former' => 'Former\Facades\Former',

### Table of contents

- [Getting started][]
- [Features][]
- [Usage and Examples][]

  [Getting started]: https://github.com/formers/former/wiki/Getting-started
  [Features]: https://github.com/formers/former/wiki/Features
  [Usage and Examples]: https://github.com/formers/former/wiki/Usage-and-Examples
