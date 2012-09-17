# Former

## Former\Former

This class is the main class - it is your interface for everything in Former. Now of course, Former makes you interact with its subclasses which means you can not only use its methods but the methods of each class used by Former.

### Options

This is a set of options that can be set to modify Former's behavior.

    Former::$useBootstrap = [true|false]

Allows you to decide whether you want Former to use Bootstrap's syntax in its output or not. If set to false, you will not have access to any method used by the ControlGroup class.

    Former::$translateFrom = [string] // Defaults to 'validation.attributes'

By default Former tries to translate most labels, legends and help texts. For that it first tries to translate the string passed as is, and then it tries to look in a special place that can be defined with this variable - defaulting to 'validation.attributes.mykey'.

    Former::$requiredClass = [string]

A class to add to fields that are set as required.

### Helpers

    Former::populate([array])

Populates the fields with an array of values **or** an Eloquent object.

    Former::withErrors([Validator/Message])

If you have an `$errors` variable from a failed validation, you can feed it to this method and it will automatically set the corresponding fields as incorrect and display the error message right next to them. This is if you're calling `withErrors` in your view. If you're in the controller, you can simply pass the `$validator` object to Former and it will work jsut the same.

    Former::withRules([array])

Let you pass an array of Laravel rules to Former, to let it try and apply those rules live with HTML attributes such as `pattern`, `maxlength`, etc.

    Former::useBootstrap()

Alias for `Former::$useBootstrap = true`.

### Form builders

    Former::legend([string])

Opens a Bootstrap `<legend>` tag in your form – the string passed to Former can be a translation index since Former will attempt to translate it.

    Former::open()

The `open` method mirrors Laravel's, which means you can refer to the doc for it on Laravel's website. But it also support the magic methods introduced by Bootstrapper for backward compatibility :

    Former::horizontal_open()
    Former::secure_open()
    Former::open_for_files()
    Former::secure_vertical_open_for_files()

    Form::close()

Pretty straightforward, simply prints out a `</form>` tag. No argument or nothing.

    Form::actions([string, ...])

Here again, this mirrors Bootstrapper's `actions()` function which creates a `<div class='form-actions'>` tag to wrap your submit/reset/back/etc buttons. The only difference is with Bootstrapper to write multiple buttons you'd create an array, with Former you simply use multiple arguments

    // Bootstrapper
    Form::actions(array( Buttons::submit('Submit'), Buttons::reset('Reset') ))

    // Former
    Form::actions( Buttons::submit('Submit'), Buttons::reset('Reset') )

### Field builders

And the one you'll be using the two thirds of the time :

    Former::[classes]_[field]

This method analyze whatever unknown method you're trying to call and creates a field with it. It decomposes it as [classes]_[field] or [field].
As classes you can call all Bootstrap classes working on fields : **span1** to **span12** and **mini** to **xxlarge**.

## Former\Field

This class is what you actually get when you create a field, which means the method listed underneath are only accessible as chained methods after a field was created. To put it simply :

    // Here you're using Former
    Former::populate($project)

    // Here you're actually using the Field class wrapped in Former
    Former::text('foo')

A Former class stops being a field the second that field is printed out. Which means that you can do this :

    $textField = Former::text('foo');
    $textField->class('myclass')

But can't do this :

    echo Former::text('foo')
    Former::class('myclass')

### Interactions with the attributes

    Former::text('foo')->addClass([string])

Adds a class to the current field **without** overwriting any existing one. This differs front `->class()` will — just like any attribute setter — overwrite any existing value.

    Former::text('foo')->value([string])

Sets the field value to something

    Former::text('foo')->[attribute]([value])

Sets the value of any attribute. Attributes containing dashes have to be replaced with an underscore, so that you'd call `data_foo('bar')` to set `data-foo="bar"`.

    Former::text('text')->setAttribute([string], [string])

Can be used as a fallback to magic methods if you really have to set an attribute that contains an underscore.

    Former::text('foo')->setAttributes([associative array])

Allows you to mass-set a couple of attributes with an array. So the following examples do the exact same thing.

    Former::text('foo')->class('foo')->foo('bar')

    Former::text('foo')->setAttributes(array( 'class' => 'foo', 'foo' => 'bar' ))

The second way is not the cleanest per say but is still useful if you have to set the same attributes for a group of fields, you can then just create an array with the attributes you want and assign it to each field in order to stay DRY.

    Former::text('foo')->label([string])

Sets the label of a field to a string. If you're using Bootstrap this has a specific meaning since the label that will be created will automatically have the class `control-label`.

### Helpers

    $textField = Former::text('foo')->require()
    $textField = $textField->isRequired() // Returns "true"

Checks if a field has been set to required, be it by yourself or when transposing Laravel rules.

## Former\Checkable

Checkable is a subset of Field, which means all methods available with Field are available with Checkable. The latter just offers a few more methods related to radios and checkboxes.

    Former::radios('foo')->inline()
    Former::checkboxes('foo')->stacked()

Set the current radios and checkboxes as inline, or stacked (vertical)

    Former::checkbox('foo')->text('bar')

If you're printing only a single checkbox/radio, it's easier to use this method to set the text that will be appended to it.

## Former\ControlGroup

Helpers and methods related to Bootstrap's control groups. If you set the `$useBootstrap` option to false earlier, this class is not accessible, nor are any of its methods.

    Former::text('foo')->state([error|warning|info|success])

Set the state of a control group to a particular Bootstrap class.

    Former::text('foo')->inlineHelp([string])
    Former::text('foo')->blockHelp([string])

Adds an inline/block help text to the current field. Both can be called (meaning you can have both an inline and a block text) but can only be called once (which means if you call `inlineHelp` twice the latter will overwrite whatever you typed in the first one).

    Former::text('foo')->append([string, ...])
    Former::text('foo')->prepend([string, ...])

Prepends one or more icons/text/buttons to the current field. Those functions use `func_get_args()` so you can per example do that :

    Former::text('foo')->prepend('@', '$')

## Former\Fields\Input

All classes related to Input-type fields.

[WIP]