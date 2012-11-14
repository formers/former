# Former
## A Laravelish way to create and format forms

Travis status : [![Build Status](https://secure.travis-ci.org/Anahkiasen/former.png)](http://travis-ci.org/Anahkiasen/former)

Former is the name of a little project I'd like to present you — it's a bundle for the Laravel framework, and a really nice guy too once you get to know him.
Laravel's original Form class is great — simplistic and full of little helpers, but a tad unelegant compared to the rest of the framework. When you add Bootstrapper classes above it, it just becomes intoxicating ; the mere amount of lines you need to type to create a five fields form with control groups is simply discouraging.

Enter Former, a powerful form builder with helpers for localization, validation, repopulation, and ties-in directly to both Bootstrap and Foundation.

### Table of contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Features](#features)
    * [Out-of-the-box integration to Bootstrap and Foundation](#out-of-the-box-integration-to-bootstrap-and-foundation)
    * [Ties-in with Laravel's Validator](#ties-in-with-laravels-validator)
    * [Form population](#form-populating)
    * [Datalists](#datalists)
    * [Live validation](#live-validation)
    * [Files handling](#files-handling)
    * [Checkboxes and radios](#checkboxes-and-radios)
    * [Localization helpers](#localization-helpers)
    * [Notes on setting field values](#notes-on-setting-field-values)
- [Ultimate showdown](#ultimate-showdown)
- [Sidebar](#sidebar)

-----

<a name='introduction'></a>
## Introduction

Former aims to re-laravelize form creation by transforming each field into its own model, with its own methods and attributes. This means that you can do this sort of stuff :

```php
Former::horizontal_open()
  ->id('MyForm')
  ->secure()
  ->rules(array( 'name' => 'required' ))
  ->method('GET')

  Former::xlarge_text('name')
    ->class('myclass')
    ->value('Joseph')
    ->required();

  Former::textarea('comments')
    ->rows(10)->columns(20)
    ->autofocus();

  Former::actions (
    Former::large_primary_submit('Submit'),
    Former::large_inverse_reset('Reset')
  )

Former::close()
```

While also being able to do – just like in the days of old :

```php
Former::xlarge_text('name', null, 'Joseph', array('required' => true, 'class' => 'myclass'))

Former::textarea('comments', null, null, array('rows' => 10, 'columns' => 20, 'autofocus' => true))
```

The advantages of the first option being that you can skip arguments. If you want to set one single class on a text field, you don't have to go and set the label and the value and the yada yada to `null`, you just do `Former::text('name')->class('class')`.

Every time you call a method that doesn't actually exist, Former assumes you're trying to set an attribute and creates it magically. That's why you can do in the above example `->rows(10)` ; in case you want to set attributes that contain dashes, just replace them by underscores : `->data_foo('bar')` equals `data-foo="bar"`.
Now of course in case you want to set an attribute that actually contains an underscore (jeez aren't you the little smartass) you can always use the fallback method `setAttribute('data_foo', 'bar')`. You're welcome.

<a name='installation'></a>
## Installation

Installing Former is easy as hell. You just type the following in your Terminal :

```bash
php artisan bundle:install former
```

Add the following to your `bundles.php` file :

```php
'former' => array('auto' => true),
```

And finally for easier use I recommand adding this alias to your alias array in `application.php` :

```php
'Former' => 'Former\Former',
```

----

<a name='features'></a>
# Features

<a name='out-of-the-box-integration-to-bootstrap-and-foundation'></a>
## Out-of-the-box integration to Bootstrap and Foundation

So that's pretty nice, but so far that just looks like a modification of Laravel's Form class I mean, what's so fancy about all that ? That's where the magic underneath lies : Former recognizes when you create an horizontal or vertical form, and goes the extra mile of wrapping each field in a control group, all behind the scenes.
That means that when you type this :

```php
Former::select('clients')->options($clients, 2)
  ->help('Pick some dude')
  ->state('warning')
```

What you actually get is the following output (with Bootstrap) :

```html
<div class="control-group warning">
  <label for="clients" class="control-label">Clients</label>
  <div class="controls">
    <select id="clients" name="clients">
      <option value="0">Mickael</option>
      <option value="1">Joseph</option>
      <option value="2" selected="selected">Patrick</option>
    </select>
    <span class="help-inline">Pick some dude</span>
  </div>
</div>
```

By default Former will use Twitter Bootstrap for its syntax but you can select which framework to use with the method `Former::framework()`. For the moment Former supports `'bootstrap'` for Twitter Bootstrap, `'zurb'` for Zurb Foundation, and `null` for no framework.

```php
// Turn off Bootstrap syntax
Former::framework(null);

// Turn it on again (MAKE UP YOUR MIND JEEZ)
Former::framework('bootstrap');
```

Here is an example of code for Foundation :

```php
Former::framework('zurb');

Former::four_text('foo')->state('error')->help('bar')
```

Outputs :

```html
<div class="error">
  <label for="foo">Foo</label>
  <input class="four" type="text" name="foo" id="foo">
  <small>Bar</small>
</div>
```

<a name='ties-in-with-laravels-validator'></a>
## Ties-in with Laravel's Validator

So ok, that's already a lot of clutter removed by not having to call the lenghty `Form::control_group()` function. Now I hear you coming : "but you know I still have to manually validate my form and su"— Well no you don't. Enters Former's magic helper `withErrors`; what it does is pretty simple. Since it's already wrapping your nice fields into control groups, it goes the distance, and gently check the `Message` object for any errors that field might have, and set that error as an `.help-inline`. Now we're talking !

Now you use it differently according to how your code reacts after a failed validation :

### If your render a view on failed validation (no redirection)
```php
if($validation->fails()) {
  Former::withErrors($validation);
  return View::make('myview');
}
```

### If your redirect on failed validation
```php
if($validation->fails()) {
  return Redirect::to('login')
    ->with_errors($validation);
}
```

Now on the last example you never actually call Former, be it in your controller or in your view. Why is that ? That's because when Former opens a form on a page, it will automatically check in Session if there's not an object called `errors` and if there is, it will try to use it without requiring you to call anything.
You can disable Former's automatic errors fetching with the following option : `Former::config('fetch_errors', false)`.

<a name='form-populating'></a>
## Form populating

You can populate a form with value quite easily with the `Former::populate` function. There is two ways to do that. The first way is the usual passing of an array of values, like this :

```php
// Will populate the field 'name' with the value 'value'
Former::populate( array('name' => 'value') )
```

You can also populate a form by passing an Eloquent model to it, say you have a Client model, you can do that. This allows for a lot of goodies, I'll get back to it.

```php
Former::populate( Client::find(2) )
```

Former will recognize the model and populate the field with the model's attribute. If here per example our client has a `name` set to 'Foo' and a `firstname` set to 'Bar', Former will look for fields named 'name' and 'firstname' and fill them respectively with 'Foo' and 'Bar'.

Alternatively you can also populate a specific field after you've populated the whole form (for a relationship per example) by doing this :

```php
Former::populate($project)

Former::populateField('client', $project->client->name)
```

For the rest of the form, filling fields is basically as easy as doing `->value('something')`.
To generate a list of options for a `<select>` you call `Former::select('foo')->options([array], [facultative: selected value])`.
You can also use the results from an Eloquent/Fluent query as options for a select, like this :

```php
Former::select('foo')->fromQuery(Client::all(), 'name', 'id')
```

Where the second argument is which attribute will be used for the option's text, and the third facultative argument is which attribute will be used for the option's value (defaults to the `id` attribute).
Former also does some magic if none of those two arguments are specified. Say you pass Eloquent models to Former and don't specify what is to be used as key or value. Former will obtain the key by using Eloquent's `get_key()` method, and use any `__toString()` method binded to the model as raw value. Take this example :

```php
class Client extends Eloquent
{
  public static $key = 'code';

  public function __toString()
  {
    return $this->name;
  }
}

Former::select('clients')->fromQuery(Client::all());
```

Is the same as doing this but you know, in less painful and DRYer. This will use each Client's default key, and output the Client's name as the option's label.

```html
<div class="control-group">
  <label for="foo" class="control-label">Foo</label>
  <div class="controls">
    <select id="foo" name="foo">
      @foreach(Client::all() as $client)
        @if(Input::get('foo', Input::old('foo')) == $client->code)
          <option selected="selected" value="{{ $client->code }}">{{ $client->name }}</option>
        @else
          <option value="{{ $client->code }}">{{ $client->name }}</option>
        @endif
      @endforeach
    </select>
  </div>
</div>
```

Former is also able to populate fields with relationships. Now an example is worth a thousand words (excepted if, you know, your example is a thousand words long) :

```php
Former::populate(Client::find(2))

// Will populate with $client->name
Former::text('name')

// Will populate with $client->store->name
Former::text('store.name')

// You can go as deep as you need to
Former::text('customer.name.adress')

// Will populate with the date from all of the client's reservations
Former::select('reservations.date')

// Which is the same as this ^
Former::select('reservations')->fromQuery($client->reservations, 'date')

// If you're using a text and not a select, instead of listing the
// relationship's models as options, it wil concatenate them
Former::text('customers.name') // Will display "name, name, name"

// You can rename a field afterwards for easier Input handling
Former::text('comment.title')->name('title')
```

Kudos to [cviebrock](https://github.com/cviebrock) for the original idea.

<a name='datalists'></a>
## Datalists

But what else does it do ? Datalists, it can do datalists. You don't know what they are ? Ok; you know how sometimes you would like to make people chose between something in a select but also being able to type what they want if it's not in it ? That's a datalist. In Former you can simply create one like that :

```php
Former::text('clients')->useDatalist($clients)

// Or use a Query object, same syntax than fromQuery()
Former::text('projects')->useDatalist(Project::all(), 'name')
```

You can also (if you need to) set a custom id on the created datalist by doing `Former::text('foo')->list('myId')->useDatalist()`.
From there it will automatically generate the corresponding `<datalist>` and link it by `id` to that field. Which means your text input will get populated by the values in your array, while still letting people type whatever they want if they don't find happiness and/or are little pains in the ass.

<a name='live-validation'></a>
## Live validation

MORE. Ok, instant validation, we all like that don't we ? Now as some of you may know, all modern browsers support instant validation via HTML attributes — no Javascript needed nor script nor polyfill. There are a few attributes that can do that kind of job for you, `pattern`, `required`, `max/min` to name a few.
Now you know when you validate your POST data with that little `$rules` array stuff ? Wouldn't it be awesome to just be able to pass that array to your form and let it transcribe your rules into real-live validation ? Yes ? Because you totally can with Former, just sayin'.
Take the following (far-fetched) rules array :

```php
Former::open()->rules(array(
  'name'     => 'required|max:20|alpha',
  'age'      => 'between:18,24',
  'email'    => 'email',
  'show'     => 'in:batman,spiderman',
  'random'   => 'match:/[a-zA-Z]+/',
  'birthday' => 'before:1968-12-03',
  'avatar'   => 'image',
));
```

What Former will do is look for fields that match the keys and apply the best it can those rules. There's not a lot of supported rules for now but I plan on adding more.

```html
<input name="name"      type="text"   required maxlength="20" pattern="[a-zA-Z]+" />
<input name="age"       type="number" min="18" max="24" />
<input name="email"     type="email" />
<input name="show"      type="text"   pattern="^(batman|spiderman)$" />
<input name="random"    type="text"   pattern="[a-zA-Z]+" />
<input name="birthday"  type="date"   max="1968-12-03" />
<input name="avatar"    type="file"   accept="image/jpeg,image/png,image/gif,image/bmp" />
```

Note that you can always add custom rules the way you'd add any attributes, since the pattern attribute uses a Regex (and if you don't speak Regex you totally should because it will guide you through life or something).

```php
Former::number('age')->min(18)

Former::text('client_code')->pattern('[a-z]{4}[0-9]{2}')
```

And that's it ! And the best news : since Bootstrap recognizes live validation, if say you try to type something that doesn't match the `alpha` pattern in your name field, it will automatically turn red just like when your control group is set to `error`. Just like that, fingers snappin' and all, nothing to do but sit back, relax, and watch Chrome/Firefox/whatever pop up a sweet little box saying "You have to fill that field dude" or "That is not email, what are you trying to do, fool me or something ?".

You can also, mid-course, manually set the state of a control group — that's a feature of course available only if you're using either Bootstrap or Foundation. You can use any of the control group states which include `success`, `warning`, `error` and `info`.

```php
Former::text('name')->state('error')
```
<a name='files-handling'></a>
## Files handling

In Former like in Laravel you can create a simple file field with `Former::file`. What's new, is you can also create a multiple files field by calling `Former::files` which which will generate `<input type="file" name="foo[]" multiple />`.

One of the special method is the `->accept()` with which you can do the following :

```php
// Use a shortcut (image, video or audio)
Former::files('avatar')->accept('image')

// Use an extension which will be converted to MIME by Laravel
Former::files('avatar')->accept('gif', 'jpg')

// Or directly use a MIME
Former::files('avatar')->accept('image/jpeg', 'image/png')
```

You can also set a maximum size easily by using either bits or bytes

```php
Former::file('foo')->max(2, 'MB')
Former::file('foo')->max(400, 'Ko')
Former::file('foo')->max(1, 'TB')
```

This will create an hidden `MAX_FILE_SIZE` field with the correct value in bytes.

<a name='checkboxes-and-radios'></a>
## Checkboxes and Radios

Checkboxes and radios, man, aren't those annoying ? Even more when you have to create several of them, and you think in your head "WHY CAN'T I VALIDATE ALL THESE LIMES ?". With Former it's all a little easier :

```php
// Create a one-off checkbox
Former::checkbox('checkme')

// Create a one-off checkbox with a text, and check it
Former::checkbox('checkme')
  ->text('YO CHECK THIS OUT')
  ->check()

// Create four related checkboxes
Former::checkboxes('checkme')
  ->checkboxes('first', 'second', 'third', 'fourth')

// Create related checkboxes, and inline them
Former::checkboxes('checkme')
  ->checkboxes($checkboxes)->inline()

// Everything that works on a checkbox also works on a radio element
Former::radios('radio')
  ->radios(array('label' => 'name', 'label' => 'name'))
  ->stacked()

// Stacked and inline can also be called as magic methods
Former::inline_checkboxes('foo')->checkboxes('foo', 'bar')
Former::stacked_radios('foo')->radios('foo', 'bar')

// Set which checkables are checked or not in one move
Former::checkboxes('level')
  ->checkboxes(0, 1, 2)
  ->check(array('level_0' => true, 'level_1' => false, 'level_2' => true))

// Fine tune checkable elements
Former::radios('radio')
  ->radios(array(
    'label' => array('name' => 'foo', 'value' => 'bar', 'data-foo' => 'bar'),
    'label' => array('name' => 'foo', 'value' => 'bar', 'data-foo' => 'bar'),
  ))
```

**Important point :** Former gives you an option to force the pushing of checkboxes. What is that you mean ? That's when your checkboxes still pop up in your POST data even when they're unchecked. That sounds pretty normal but is actually the opposite of the weird-ass default behavior of forms. "IT'S UNCHECKED ? I HAVE NO RECOLLECTION WHATSOEVER OF THAT FIELD HAVING EVER EXISTED".
You can change what value an unchecked checkbox possesses in the POST array via the `unchecked_value` option.

When creating checkables via the checkboxes/radios() method, by default for each checkable name attribute it will use the original name you specified and append it a number (here in our exemple it would be `<input type="checkbox" name="checkme_2">`).
It also repopulates it, meaning a checked input will stay checked on submit.

<a name='localization-helpers'></a>
## Localization helpers

For those of you that work on multilingual projects, Former is also here to help. By default, when creating a field, if no label is specified Former will use the field name by default. But more importantly it will try and translate it automatically. Same goes for checkboxes labels, help texts and form legends. Which means the following :

```php
// This
Former::label(__('validation.attributes.name'))
Former::text('name', __('validation.attributes.name'))
Former::text('name')->inlineHelp(__('help.name'))
Former::checkbox('rules')->text(__('my.translation'))
<legend>{{ __('validation.attributes.mylegend') }}</legend>

// Is the same as this
Former::label('name')
Former::text('name')
Former::text('name')->inlineHelp('help.name')
Former::checkbox('rules')->text('my.translation')
Former::legend('mylegend')
```

Which you know, is kind of cool. Former will first try to translate the string in itself, ie `my.text` will return `__('my.text')` and if that fails, it will look for it in a fallback place. You can set where Former look for translations by changing the following variable : `Former::config('translate_from', [boolean])` (defaults to `validation.attributes`). Note that **it must be an array**.

<a name='notes-on-setting-field-values'></a>
## Notes on setting field values

All form classes encounter a problem at one point : what kind of data takes precedence over what kind ? To populate your field, Former set the following priorities to found values :

* POST data takes precedence over everything – if a user just typed something in a field, chances are this *is* what they want to see in it next time
* Then comes data set via the `->forceValue()` method – it's a special branch of `->value()` created to force a value in a field no matter what happens
* Then any values set via `Former::populate()` – that means that if you're editing something or are repopulating with some value, it can be overwritten with `forceValue`
* Finally the classic `->value()` gets the least priority – it is created for minimum and default field values and thus gets overwritten by population and POST data

----------

<a name='ultimate-showdown'></a>
# ULTIMATE SHOWDOWN

```php
// Laravel
<div class="control-group">
  {{ Form::label('input01', __('validation.attributes.input01'), array('class' => 'control-label') )}}
  <div class="controls">
    <div class="input-prepend input-append">
      <span class="add-on">@</span>
      {{ Form::text('input01', 'myname', (Input::get('input01', Input::old('input01')), array('class' => 'input-xlarge')) }}
      <span class="add-on"><i class="icon-white icon-enveloppe"></i></span>
    </div>
    <p class="help-block">This is an help text</p>
  </div>
</div>

// Bootstrapper
echo Form::prepend_append(
  Form::control_group(
    Form::label('input01', __('validation.attributes.input01')),
    Form::xlarge_text('input01'),
    (Input::get('input01', Input::old('input01', 'myname'))),
    $validation->errors->get('input01'),
    Form::block_help('This is an help text')
  ),
  '@', Icon::white_enveloppe()
);

// Former
Former::xlarge_text('input01', 'Text input')
  ->blockHelp('This is an help text')
  ->prepend('@')->appendIcon('white-enveloppe')
  ->value('myname')
```

```php
// Laravel
// Man don't even get me started

// Boostrapper
echo Form::control_group(
  Form::label('checkboxes', 'Check those boxes'),
  Form::inline_labelled_checkbox('check1', 'Check me', 1, Input::get('check1', Input::old('check1'))).
  Form::inline_labelled_checkbox('check2', 'Check me too', 1, Input::get('check2', Input::old('check2'))),
  $validation->errors->get('check1'),
  Form::inline_help('I SAID CHECK THOSE DOUBLES')
);

// Former
Former::checkboxes('check')
  ->checkboxes('Check me', 'Check me too')
  ->help('I SAID CHECK THOSE DOUBLES')
```

----

<a name='sidebar'></a>
# Sidebar

It may seems like I'm spitting on both Laravel and Bootstrapper here but bare with me — I'm totally not. I love Laravel, it's an amazing and elegant framework, and I couldn't stress out enough how every one of you should have Bootstrapper installed somewhere in your bundles — hell I'm even collaborating actively on the project. I even intended Former to replace Bootstrapper's Form class but to me it was just a little too much out of its scope.

Anyway, that's all for now, hope you enjoy it and don't forget to report any question/bug/idea/issue in the, well, Issues.