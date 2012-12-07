## Changelog

### 2.7.0 (unreleased)

- **[add] Former now recognizes grouped fields (name="field[]") and bind them together. You can also use the new `->grouped()` method**

### 2.6.0

- **[add] Former::open()->rules([]) as alias to Former::withRules**
- **[add] 'required_text' to append a text to required fields's labels**
- [fix] Fix a bug where labels for radios would fail because of identical ids
- [fix] Fixed a bug where ->populateField would fail if the form was populated with an object

### 2.5.0

- **[add] ControlGroup->prependIcon and appendIcon methods**
- **[add] Add basic button class that allow Bootstrappy submit/buttons**
- [add] Ability to pass an array of classes to add to Field->addClass
- [fix] Fix instantiated classes bug in PHP 5.3.2
- [fix] Fix multiple buttons instances overwriting themselves

### 2.4.0

- **[add] Form openers are now objects too and accept chained methods**
- [add] Add `unchecked_value` option to decide what value unchecked checkboxes have in the POST array
- [add] Allow booleans to be passed to Checkable->check() on single items
- [mod] Disable `push_checkbox` option by default
- [fix] Automatically fetch Lang objects passed to `->options`

### 2.3.0

- **[add] Add `push_checkboxes` option which forces the submiting of unchecked fields to the POST array**

### 2.2.0

- **[add] Add `Former::file()` and `Former::files()` with methods `->max` and `->accept`**
- [add] Add ability to set attributes for a label
- [add] Add ability to set a placeholder option for select fields

### 2.1.0

- [add] Added `->check()` method on radios and checkboxes
- **[add] Add ability to populate field with a model's relationships**

### 2.0.0

- **[add] Former now uses Laravel's Config class to manage settings, letting the user create a `former.php` file in `application/config` to override default options**
- [add] Add option to disable automatic labeling of fields
- [fix] Fix translation of empty strings

### 1.2.1

- **[add] Fetch automatically key and value from models through `get_key` and `__toString`**
- [add] Add `Former::populateField` to populate a specific field
- [fix] Fixed a bug preventing from using one Former call to output several times

### 1.2.0

- **[add] Add suport for Zurb's Foundation framework**
- **[add] Allow the passing of Query/Eloquent objects to text fields through `->useDatalist`**
- [add] Add option to desactivate live validation
- [mod] Let user specify a custom id for generated datalists
- [mod] Allow public use of `Former::getErrors()`
- [fix] Fix custom arguments of `open()` not working as desired
- [fix] Don't create a label tag around checkboxes if the label is empty

### 1.1.0

- **[add] Allow the passing of Query/Eloquent objets to select fields through `->fromQuery`**
- [fix] Disable form population on password fields
- [fix] Fix uneditable inputs outputing as text fields

### 1.0.0

- Initial release of Former on [Laravel Bundles](http://bundles.laravel.com/bundle/former/)
