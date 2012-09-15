# Former
## A Laravelish way to create and format forms

Former is the name of a little project I'd like to present you — it's a bundle, a superset of Bootstrapper, and a really nice guy too.
Laravel's original Form class is great — simplistic and full of little helpers, but a tad unelegant compared to the rest of the framework. When you add Bootstrapper classes above it, it just becomes intoxicating ; the mere amount of lines you need to type to create a five fields form with control groups is simply discouraging.

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

So ok, that's already a lot of clutter removed by not having to call the lenghty `Form::control_group()` function. Now I hear you coming : "but you know I still have to manually validate my form and su"— Well no you don't. Enters Former's magic helper `setErrors`; what it does is pretty simple. Since it's already wrapping your nice fields into control groups, it goes the distance, and gently check the `Message` object for any errors that field might have, and set that error as an `.help-inline`. Now we're talking !

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

There are a lot of tweaks and changes, things to make your life easier.

----------

# ULTIMATE SHOWDOWN

```php
// Bootstrapper
echo Form::prepend_append(
  Form::control_group(
    Form::label('input01', 'Text input'),
    Form::xlarge_text('input01'),
    (Input::get('input01', Input::old('input01', 'myname'))),
    Form::block_help('This is an help text')
  ),
  '@', '$'
);

// Former
Former::xlarge_text('input01', 'Text input')
  ->blockHelp('This is an help text')
  ->prepend('@')->append('$')
  ->value('myname')
```