## Changelog

3.0.0
-----

- Refactor of Former for Laravel 4
- [add] You can now chain methods to actiosn blocks `Former::actions('Hello', 'Mr Bond')->id('foo')->addClass('bar')`
- [add] You can now chain buttons to actions blocks `Former::actions()->large_primary_submit('Submit')`
- [add] You can now chain live validation rules to fields (example: `Former::text('foo')->alpha_dash()`)
- [add] You can now display a single field without control group in any kind of form (`Former::text('foo')->raw()`)
- [mod] Frameworks names renamed from `bootstrap` to `TwitterBootstrap`, `zurb` to `ZurbFoundation` and `null` to `Nude`

2.6.0
-----

- **[add] 'required_text' to append a text to required fields's labels**
- **[add] Former::open()->rules([]) as alias to Former::withRules**
- [fix] Fix a bug where labels for radios would fail because of identical ids
- [fix] Fixed a bug where ->populateField would fail if the form was populated with an object

2.5.0
-----

- **[add] Add basic button class that allow Bootstrappy submit/buttons**
- **[add] ControlGroup->prependIcon and appendIcon methods**
- [add] Ability to pass an array of classes to add to Field->addClass
- [fix] Fix instantiated classes bug in PHP 5.3.2
- [fix] Fix multiple buttons instances overwriting themselves

2.4.0
-----

- **[add] Form openers are now objects too and accept chained methods**
- [add] Add `unchecked_value` option to decide what value unchecked checkboxes have in the POST array
- [add] Allow booleans to be passed to Checkable->check() on single items
- [mod] Disable `push_checkbox` option by default
- [fix] Automatically fetch Lang objects passed to `->options`

2.3.0
-----

- **[add] Add `push_checkboxes` option which forces the submiting of unchecked fields to the POST array**

2.2.0
-----

- **[add] Add `Former::file()` and `Former::files()` with methods `->max` and `->accept`**
- [add] Add ability to set a placeholder option for select fields
- [add] Add ability to set attributes for a label

2.1.0
-----

- **[add] Add ability to populate field with a model's relationships**
- [add] Added `->check()` method on radios and checkboxes

2.0.0
-----

- **[add] Former now uses Laravel's Config class to manage settings, letting the user create a `former.php` file in `application/config` to override default options**
- [add] Add option to disable automatic labeling of fields
- [fix] Fix translation of empty strings

1.2.1
-----

- **[add] Fetch automatically key and value from models through `get_key` and `__toString`**
- [add] Add `Former::populateField` to populate a specific field
- [fix] Fixed a bug preventing from using one Former call to output several times

1.2.0
-----

- **[add] Add suport for Zurb's Foundation framework**
- **[add] Allow the passing of Query/Eloquent objects to text fields through `->useDatalist`**
- [add] Add option to desactivate live validation
- [mod] Allow public use of `Former::getErrors()`
- [mod] Let user specify a custom id for generated datalists
- [fix] Don't create a label tag around checkboxes if the label is empty
- [fix] Fix custom arguments of `open()` not working as desired

1.1.0
-----

- **[add] Allow the passing of Query/Eloquent objets to select fields through `->fromQuery`**
- [fix] Disable form population on password fields
- [fix] Fix uneditable inputs outputing as text fields

1.0.0
-----

- Initial release of Former on [Laravel Bundles](http://bundles.laravel.com/bundle/former/)
