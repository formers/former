# Former
## A Laravelish way to create and format forms

[![Build Status](http://img.shields.io/travis/Anahkiasen/former.svg?style=flat)](https://travis-ci.org/Anahkiasen/former)
[![Latest Stable Version](http://img.shields.io/packagist/v/anahkiasen/former.svg?style=flat)](https://packagist.org/packages/anahkiasen/former)
[![Total Downloads](http://img.shields.io/packagist/dt/anahkiasen/former.svg?style=flat)](https://packagist.org/packages/anahkiasen/former)
[![Scrutinizer Quality Score](http://img.shields.io/scrutinizer/g/Anahkiasen/former.svg?style=flat)](https://scrutinizer-ci.com/g/Anahkiasen/former/)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/Anahkiasen/former.svg?style=flat)](https://scrutinizer-ci.com/g/Anahkiasen/former/)
[![Dependency Status](https://www.versioneye.com/user/projects/54363f5eb2a9c51d400000d0/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54363f5eb2a9c51d400000d0)
[![Support via Gittip](http://img.shields.io/gittip/Anahkiasen.svg?style=flat)](https://www.gittip.com/Anahkiasen/)

Former is the name of a little project I'd like to present you — it's a PHP package that allows you to do all kinds of powerful stuff with forms while remaining pretty simple to use. It's also a really nice guy too once you get to know him.

Former chews a lot of the work for you, it handles repopulation, validation, grouped fields, automatic markup for your favorite CSS framework (Bootstrap, Foundation). I invite you to take a look at the features page to get more informations.

### Introduction

Former aims to rethink elegantly form creation by transforming each field into its own model, with its own methods and attributes. This means that you can do this sort of stuff :

```php
Former::horizontal_open()
  ->id('MyForm')
  ->secure()
  ->rules(['name' => 'required'])
  ->method('GET');

  Former::xlarge_text('name')
    ->class('myclass')
    ->value('Joseph')
    ->required();

  Former::textarea('comments')
    ->rows(10)->columns(20)
    ->autofocus();

  Former::actions()
    ->large_primary_submit('Submit')
    ->large_inverse_reset('Reset');

Former::close();
```

Every time you call a method that doesn't actually exist, Former assumes you're trying to set an attribute and creates it magically. That's why you can do in the above example `->rows(10)` ; in case you want to set attributes that contain dashes, just replace them by underscores : `->data_foo('bar')` equals `data-foo="bar"`.
Now of course in case you want to set an attribute that actually contains an underscore (jeez aren't you the little smartass) you can always use the fallback method `setAttribute('data_foo', 'bar')`. You're welcome.

This is the core of it, but Former offers a lot more. I invite you to consult the wiki to see the extend of what Former does.

Former is developed by [Maxime Fabre][] and [Peter Coles][].

-----

### Table of contents

- [Getting started][]
- [Features][]
- [Anatomy][]

  [Peter Coles]: http://petercoles.com
  [Maxime Fabre]: http://autopergamene.eu
  [Anatomy]: https://github.com/Anahkiasen/former/wiki/Anatomy
  [Features]: https://github.com/Anahkiasen/former/wiki/Features
  [Getting started]: https://github.com/Anahkiasen/former/wiki/Getting-started
