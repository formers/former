# Former
## A Laravelish way to create and format forms

Travis status : [![Build Status](https://secure.travis-ci.org/Anahkiasen/former.png)](http://travis-ci.org/Anahkiasen/former)

Former is the name of a little project I'd like to present you — it's a bundle, a superset of Bootstrapper, and a really nice guy too.
Laravel's original Form class is great — simplistic and full of little helpers, but a tad unelegant compared to the rest of the framework. When you add Bootstrapper classes above it, it just becomes intoxicating ; the mere amount of lines you need to type to create a five fields form with control groups is simply discouraging.

Former is still in beta, it's not yet published on Laravel's bundle repo. It's working, but I encourage you to post any question, idea or bug as an issue on this repo and i'll be there for you (cause you're there for me too).

-----

## Introduction

Former aims to re-laravelize form creation by transforming each field into its own model, with its own methods and attributes. This means that you can do this sort of stuff :

```php
Former::horizontal_open()

  Former::xlarge_text('name')
    ->class('myclass')
    ->value('Joseph')
    ->require();

  Former::textarea('comments')
    ->rows(10)->columns(20)
    ->autofocus();

Former::close()
```

While also being able to do – just like in the days of old :

```php
Former::xlarge_text('name', null, 'Joseph', array('require' => true, 'class' => 'myclass'))

Former::textarea('comments', null, null, array('rows' => 10, 'columns' => 20, 'autofocus' => true))
```

The advantages of the first option being that you can skip arguments. If you want to set one single class on a text field, you don't have to go and set the label and the value and the yada yada to `null`, you just do `Former::text('name')->class('class')`.

Everytime you call a method that doesn't actually exist, Former assumes you're trying to set an attribute and creates it magically. That's why you can do in the above example `->rows(10)` ; in case you want to set attributes that contain dashes, just replace them by underscores : `->data_foo('bar')` equals `data-foo="bar"`.
Now of course in case you want to set an attribute that actually contains an underscore (jeez aren't you the little smartass) you can always use the fallback method `setAttribute('data_foo', 'bar')`. You're welcome.

## Installation

Installing Former is easy as hell. You just type the following in your Terminal :

```bash
php artisan bundle:install former
```

Add the following to your `bundles.php` file :

```php
return array(
  'former' => array('auto' => true),
)
```

And finally for easier use I recommand adding this alias to your alias array in `application.php` :

```php
'Former'   => 'Former\Former',
```

----

# Features

## Out-of-the-box integration to Bootstrap

So that's pretty nice, but so far that just looks like a modification of Laravel's Form class I mean, what's so Bootstrappy about all that ? That's where the magic underneath lies : Former recognizes when you create an horizontal or veritcal form, and goes the extra mile of wrapping each field in a control group, all behind the scenes.
That means that when you type this :

```php
Former::select('clients')->options($clients, 2)
  ->inlineHelp('Pick some dude')
  ->state('warning')
```

What you actually get is the following output :

```html
<div class='control-group warning'>
  <label for='clients'>Clients</label>
  <select name='clients'>
    <option value='0'>Michael</option>
    <option value='1'>Joseph</option>
    <option value='2' selected>Patrick</option>
  </select>
  <span class='help-inline'>Pick some dude</span>
</div>
```

All the Bootstrap syntax although useful to a lot of people, can be desactivated with the following option :

```php
// Turn off Bootstrap syntax
Former::useBootstrap(false);

// Turn it on again (MAKE UP YOUR MIND JEEZ)
Former::useBootstrap();
```

## Ties-in with Laravel's Validator

So ok, that's already a lot of clutter removed by not having to call the lenghty `Form::control_group()` function. Now I hear you coming : "but you know I still have to manually validate my form and su"— Well no you don't. Enters Former's magic helper `withErrors`; what it does is pretty simple. Since it's already wrapping your nice fields into control groups, it goes the distance, and gently check the `Message` object for any errors that field might have, and set that error as an `.help-inline`. Now we're talking !

To use it, simply do the following, be it in your controller or in your view (better if you redirect on validation fail) :

```php
// Use directly in the controller if rendering the view directly
if($validation->fails()) {
  Former::withErrors($validation);
  return View::make('myview');
}

// OR if redirection after a fail
if($validation->fails()) {
  return Redirect::to('login')
    ->with_errors($validator);
}

// And then in your view
{{ Former::withErrors() }}

```

Former will automatically get the `$errors` variable from the session so you don't actually need to pass anything to the `withErrors()` method, **nor do you actually need to check for it** like you usually do (`if(isset($errors)) { }`).

## Form populating

You can populate a form with value quite easily with the `Former::populate` function. There is two ways to do that. The first way is the usual passing of an array of values, like this :

```php
// Will populate the field 'name' with the value 'value'
Former::populate( array('name' => 'value') )
```

You can also populate a form by passing an Eloquent model to it, say you have a Client model, you can do that :

```php
Former::populate( Client::find(2) )
```

Former will recognize the model and populate the field with the model's attribute. If here per example our client has a `name` set to 'Foo' and a `firstname` set to 'Bar', Former will look for fields named 'name' and 'firstname' and fill them respectively with 'Foo' and 'Bar'.

## Datalists

But what else does it do ? Datalists, it can do datalists. You don't know what they are ? Ok; you know how sometimes you would like to make people chose between something in a select but also being able to type what they want if it's not in it ? That's a datalist. In Former you can simply create one like that :

```php
Former::text('clients')->useDatalist($clients)
```

It will automatically generate the corresponding `<datalist>` and link it by `id` to that field. Which means your text input will get populated by the values in your array, while still letting people type whatever they want if they don't find happiness and/or are little pains in the ass.

## Live validation

MORE. Ok, instant validation, we all like that don't we ? Now as some of you may know, all modern browsers support instant validation via HTML attributes — no Javascript needed nor script nor polyfill. There are a few attributes that can do that kind of job for you, `pattern`, `required`, `max/min` to name a few.
Now you know when you validate your POST data with that little `$rules` array stuff ? Wouldn't it be awesome to just be able to pass that array to your form and let it transcribe your rules into real-live validation ? Yes ? Because you totally can with Former, just sayin'.
Take the following (simple) rules array :

```php
$rules = array(
  'name' => 'required|alpha',
  'age'  => 'min:18'
);
```

What Former will do is look for fields that match the keys and apply the best it can those rules. There's not a lot of supported rules for now but I plan on adding more.

```html
<input type="text" name="name" required pattern="[a-zA-Z]+" />
<input type="number" min="18" />
```

Note that you can always add custom rules the way you'd add any attributes, since the pattern attribute uses a Regex.

```php
Former::number('age')->minlength(18)

Former::text('client_code')->pattern('[a-z]{4}[0-9]{2}')
```

And that's it ! And the best news : since Bootstrap recognizes live validation, if say you try to type something that doesn't match the `alpha` pattern in your name field, it will automatically turn red just like when your control group is set to `error`. Just like that, fingers snappin' and all, nothing to do but sit back, relax, and watch Chrome/Firefox/whatever pop up a sweet little box saying "You have to fill that field dude".

You can also, mid-course, manually set the state of a control group — that's a feature of course available only if you're using Bootstrap's syntax. You can use any of the control group states which include `success`, `warning`, `error` and `info`.

```php
Former::text('name')->state('error')
```

## Checkboxes and Radios

Checkboxes and radios, man, aren't those annoying ? Even more when you have to create several of them, and you think in your head "WHY CAN'T I VALIDATE ALL THESE LIMES ?". With Former it's all a little easier :

```php
// Create a one-off checkbox
Former::checkbox('checkme')

// Create a one-off checkbox with a text
Former::checkbox('checkme')->text('YO CHECK THIS OUT')

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

// Fine tune checkable elements
Former::radios('radio')
  ->radios(array(
    'label' => array('name' => 'foo', 'value' => 'bar', 'data-foo' => 'bar'),
    'label' => array('name' => 'foo', 'value' => 'bar', 'data-foo' => 'bar'),
  ))
```

When creating checkables via the checkboxes/radios() method, by default for each checkable name attribute it will use the original name you specified and append it a number (here in our exemple it would be `<input type="checkbox" name="checkme_2">`).
It also repopulates it, meaning a checked input will stay checked on submit.

## Localization helpers

For those of you that work on multingual projects, Former is also here to help. By default, when creating a field, if no label is specified Former will use the field name by default. But more importantly it will try and translate it automatically. Same goes for checkboxes labels and form legends. Which means the following :

```php
// This
Former::text('name', __('validation.attributes.name'))
Former::checkbox('rules')->text(__('my.translation'))
<legend>{{ __('validation.attributes.mylegend') }}</legend>

// Is the same as this
Former::text('name')
Former::checkbox('rules')->text('my.translation')
Former::legend('mylegend')
```

Which you know, is kind of cool. Former will first try to translate the string in itself, ie `my.text` will return `__('my.text')` and if that fails, it will look for it in a fallback placE. You can set where Former look for translations by changing the following variable : `Former::$translateFrom' (defaults to `validation.attributes`). Note that **it must be an array**.

## Notes on setting field values

All form classes encounter a problem at one point : what kind of data takes precedence over what kind ? To populate your field, Former set the following priorities to found values :

* POST data takes precedence over everything – if a user just typed something in a field, chances are this *is* what they want to see in it next time
* Then comes data set via the `->forceValue()` method – it's a special branch of `->value()` created to force a value in a field no matter what happens
* Then any values set via `Former::populate()` – that means that if you're editing something or are repopulating with some value, it can be overwritten with `forceValue`
* Finally the classic `->value()` gets the least priority – it is created for minimum and default field values and thus gets overwritten by population and POST data

----------

# ULTIMATE SHOWDOWN

```php
// Laravel
<div class="control-group">
  {{ Form::label('input01', __('validation.attributes.input01'), array('class' => 'control-label') )}}
  <div class="controls">
    <div class="input-prepend input-append">
      <span class="add-on">@</span>
      {{ Form::text('input01', 'myname', (Input::get('input01', Input::old('input01')), array('class' => 'input-xlarge')) }}
      <span class="add-on">$</span>
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
  '@', '$'
);

// Former
Former::withErrors($validation);
Former::xlarge_text('input01', 'Text input')
  ->blockHelp('This is an help text')
  ->prepend('@')->append('$')
  ->value('myname')
```

```php
// Laravel
// Man don't even get me started

// Boostrapper
echo Form::control_group(
  Form::label('checkboxes', 'Check those boxes'),
  Form::inline_labelled_checkbox('check1', 'Check me', 1, Input::get('check1', Input::old('check1'))).
  Form::inline_labelled_checkbox('check2', 'Ccheck me too', 1, Input::get('check1', Input::old('check1'))),
  $validation->errors->get('check1'),
  Form::block_help('I SAID CHECK THOSE DOUBLES')
);

// Former
Former::withErrors($validation);
Former::checkboxes('check')->checkboxes('Check me', 'Check me too')
  ->blockHelp('I SAID CHECK THOSE DOUBLES')
```

----

# Sidebar

It may seems like I'm spitting on both Laravel and Bootstrapper here but bare with me — I'm totally not. I love Laravel, it's an amazing and elegant framework, and I couldn't stress out enough how every one of you should have Bootstrapper installed somewhere in your bundles — hell I'm even collaborating actively on the project. I even inteded Former to replace Bootstrapper's Form class but to me it was just a little too much out of its scope.

Anyway, that's all for now, hope you enjoy it and don't forget to report any question/bug/idea/issue in the, well, Issues.