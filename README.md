# Former
## A Laravelish way to create and format forms

Former is the name of a little project I'd like to present you — it's a bundle, a superset of Bootstrapper, and a really nice guy too.
Laravel's original Form class is great — simplistic and full of little helpers, but a tad unelegant compared to the rest of the framework. When you add Bootstrapper classes above it, it just becomes intoxicating ; the mere amount of lines you need to type to create a five fields form with control groups is simply discouraging.

Former is still in beta, it's not yet published on Laravel's bundle repo. It's working, but I encourage you to post any question, idea or bug as an issue on this repo and i'll be there for you (cause you're there for me too).

----------

Former aims to re-laravelize form creation by transforming each field into its own model, with its own methods and attributes. This means that you can do this sort of stuff :

```php
Former::xlarge_text('name')
  ->class('myclass')
  ->value('Joseph')
  ->require();

Former::textarea('comments')
  ->rows(10)->columns(20)
  ->autofocus();
```

While also being able to do – just like in the days of old :

```php
Former::xlarge_text('name', null, 'Joseph', array('require' => true, 'class' => 'myclass'))

Former::textarea('comments', null, null, array('rows' => 10, 'columns' => 20, 'autofocus' => true))
```

The advantages of the first option being that you can skip arguments. If you want to set one single class on a text field, you don't have to go and set the label and the value and the yada yada to `null`, you just do `Former::text('name')->class('class')`.

----------

So that's pretty nice, but so far that just looks like a modification of Laravel's Form class I mean, what's so Bootstrappy about all that ? That's where the magic underneath lies : Former recognizes when you create an horizontal or veritcal form, and goes the extra mile of wrapping each field in a control group, all behind the scenes.
That means that when you type this :

```php
Former::select('clients')->options($clients, 2)
  ->inlineHelp('Pick some dude')
```

What you actually get is the following output :

```html
<div class='control-group'>
  <label for='clients'>Clients</label>
  <select name='clients'>
    <option value='0'>Michael</option>
    <option value='1'>Joseph</option>
    <option value='2' selected>Patrick</option>
  </select>
  <span class='help-inline'>Pick some dude</span>
</div>
```

----------

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
  return View::make('myview')
    ->with_errors($validator);
}

// And then in your view
{{ Former::withErrors($errors) }}

```

But what else does it do ? Datalists, it can do datalists. You don't know what they are ? Ok; you know how sometimes you would like to make people chose between something in a select but also being able to type what they want if it's not in it ? That's a datalist. In Former you can simply create one like that :

```php
Former::text('clients')->useDatalist($clients)
```

It will automatically generate the corresponding `<datalist>` and link it by `id` to that field. Which means your text input will get populated by the values in your array, while still letting people type whatever they want if they don't find happiness and/or are little pains in the ass.

MORE. Ok, instant validation, we all like that don't we ? Now as some of you may know, all modern browsers support instant validation via HTML attributes — no Javascript needed nor script nor polyfill. There are a few attributes that can do that kind of job for you, `pattern`, `required`, `max/min` to name a few.
Now you know when you validate your POST data with that little `$rules` array stuff ? Wouldn't it be awesome to just be able to pass that array to your form and let it transcribe your rules into real-live validation ? Yes ? Because you totally can with Former, just sayin'.
Take the following (simple) rules array :

```php
$rules = array(
  'name' => 'required|alpha',
  'age'  => 'min:18'
);
```

What Former will do is look for fields that match the keys and apply the best it can those rules :

```html
<input type="text" name="name" required pattern="[a-zA-Z]+" />
<input type="number" min="18" />
```

And that's it ! And the best news : since Bootstrap recognizes live validation, if say you try to type something that doesn't match the `alpha` pattern in your name field, it will automatically turn red just like when your control group is set to `error`. Just like that, fingers snappin' and all, nothing to do but sit back, relax, and watch Chrome/Firefox/whatever pop up a sweet little box saying "You have to fill that field dude".

----------

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
  ->radios(array('value' => 'text', 'value' => 'text'))
  ->stacked()
```

When creating checkables via the checkboxes/radios() method, by default for each checkable name attribute it will use the original name you specified and append it a number (here in our exemple it would be `<input type="checkbox" name="checkme_2">`).
It also repopulates it, meaning a checked input will stay checked on submit.

---------

For those of you that work on multingual projects, Former is also here to help. By default, when creating a field, if no label is specified Former will use the field name by default. But more importantly it will try and translate it automatically, which means the following :

```php
// This
Former::text('name', __('validation.custom.name'))

// Is the same as this
Former::text('name')
```

Which you know, is kind of cool. I plan on letting you set where you want Former to look for the translated field names, and add more localization magic in general, but this is all yet to come.

----------

# ULTIMATE SHOWDOWN

```php
// Laravel
<div class="control-group">
  {{ Form::label('input01', __('validation.custom.input01'), array('class' => 'control-label') )}}
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
    Form::label('input01', __('validation.custom.input01')),
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