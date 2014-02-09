# CHANGELOG

## 3.4.2

- [add] Added the ability to specify multiple namespaces to look for fields in the MethodDispatcher
- [mod] Appended buttons are now properly wrapped in input-group-btn in Bootstrap 3
- [fix] Fixed a bug where wrong items would get selected in optgroups
- [fix] Fixed some bug when fetching data from the request
- [fix] Fixed spaces in validation rules causing errors

## 3.4.1

- [add] Added support for passing MessageBag instances to `withErrors`
- [mod] MethodDispatcher can now look for field classes in multiple namespaces
- [mod] Use objects' `toArray` instead of array casting when possible
- [fix] Fix framework classes overwriting classes predefined on Field classes
- [fix] Fix stability problems that prevented Former form being installed

## 3.4.0

- [add] Added `Former::rawOpen` to open a temporary no-framework/no-label form
- [add] Added support for camelCase form openers (ie. `verticalOpen` and `vertical_open`)
- [add] Added possibility to disable automatic capitalization of translations
- [fix] Fixed a bug where two fields with the same name would get the same ID
- [fix] Various bugfixes related to repopulation
- [fix] Fix various memory and 4.1 compatibility issues

## 3.3.0

- [add] Add ability to pass attributes to a select's options
- [add] Add support for PATCH method
- [add] Add ability to create range number fields (`Former::number('foo')->range(1, 5)` sets the `min` to 1 and `max` to 5)
- [add] Added Form->route and Form->controller to set a form's action to a route/controller path, and the corresponding method
- [add] Allow switching to alternate icon fonts
- [mod] Form classes are now framework-dependant
- [mod] More work on the Bootstrap 3 integration
- [fix] Prevent custom groups from responding to errors from non-grouped fields
- [fix] Fix bug in selection false values in Selects
- [fix] Fix bug where selects with optgroups weren't populated correctly

## 3.2.0

- **[mod] Updated TwitterBootstrap3 to the latest release**
- **[mod] Former now handles camelCase attributes (ie. `dataPlaceholder` for `data-placeholder`)**
- [mod] `$group->getErrors()` is now public

## 3.1.0

- [add] **You can now configure which attributes are translated by default**
- [add] **Added the `TwitterBootstrap3` framework**
- [add] Add a second argument to `Former::group` that allows specifying which errors should be displayed
- [add] Add ability to interact with the Group's method by using `onGroup{method}` (ex: `onGroupAddClass`)
- [mod] All fields are now displayed as "raw" by default in custom groups
- [fix] Fix some checkable bugs

## 3.0.0

- **Refactor of Former – the project is now framework agnostic, see installation details**
- [add] You can now chain methods to actions blocks `Former::actions('Hello', 'Mr Bond')->id('foo')->addClass('bar')`
- [add] You can now chain buttons to actions blocks `Former::actions()->large_primary_submit('Submit')`
- [add] You can now chain live validation rules to fields (example: `Former::text('foo')->alpha_dash()`)
- [add] You can now display a single field without control group in any kind of form (`Former::text('foo')->raw()`)
- [mod] Frameworks names renamed from `bootstrap` to `TwitterBootstrap`, `zurb` to `ZurbFoundation` and `null` to `Nude`
- [add] You can now manually open groups via `Former::group('label')`
- [add] You can also create a group that contains raw content (not a field) with `Former::group('label')->contents('YourContent')`. This will wrap the content in a control class so that your content is aligned with the fields.
- [add] Checkables now handle being populated from relations
- [add] You can now add classes to the group via the `->addGroupClass` method
- [add] Former::withRules() now also take a Laravel-formatted string of rules (ie. "rule|rule:parameter|rule")
- [add] You can now populate on a form-basis with the chained method `->populate` on a form opener
- [add] Add support for macros with Former::macro($name, $macro())
- [add] Add Select->range() method
- [add] Former now automatically inserts a token in the form
- [add] Support for select groups

## 2.6.0

- **[add] 'required_text' to append a text to required fields's labels**
- **[add] Former::open()->rules([]) as alias to Former::withRules**
- [fix] Fix a bug where labels for radios would fail because of identical ids
- [fix] Fixed a bug where ->populateField would fail if the form was populated with an object

## 2.5.0

- **[add] Add basic button class that allow Bootstrappy submit/buttons**
- **[add] ControlGroup->prependIcon and appendIcon methods**
- [add] Ability to pass an array of classes to add to Field->addClass
- [fix] Fix instantiated classes bug in PHP 5.3.2
- [fix] Fix multiple buttons instances overwriting themselves

## 2.4.0

- **[add] Form openers are now objects too and accept chained methods**
- [add] Add `unchecked_value` option to decide what value unchecked checkboxes have in the POST array
- [add] Allow booleans to be passed to Checkable->check() on single items
- [mod] Disable `push_checkbox` option by default
- [fix] Automatically fetch Lang objects passed to `->options`

## 2.3.0

- **[add] Add `push_checkboxes` option which forces the submiting of unchecked fields to the POST array**

## 2.2.0

- **[add] Add `Former::file()` and `Former::files()` with methods `->max` and `->accept`**
- [add] Add ability to set a placeholder option for select fields
- [add] Add ability to set attributes for a label

## 2.1.0

- **[add] Add ability to populate field with a model's relationships**
- [add] Added `->check()` method on radios and checkboxes

## 2.0.0

- **[add] Former now uses Laravel's Config class to manage settings, letting the user create a `former.php` file in `application/config` to override default options**
- [add] Add option to disable automatic labeling of fields
- [fix] Fix translation of empty strings

## 1.2.1

- **[add] Fetch automatically key and value from models through `get_key` and `__toString`**
- [add] Add `Former::populateField` to populate a specific field
- [fix] Fixed a bug preventing from using one Former call to output several times

## 1.2.0

- **[add] Add suport for Zurb's Foundation framework**
- **[add] Allow the passing of Query/Eloquent objects to text fields through `->useDatalist`**
- [add] Add option to desactivate live validation
- [mod] Allow public use of `Former::getErrors()`
- [mod] Let user specify a custom id for generated datalists
- [fix] Don't create a label tag around checkboxes if the label is empty
- [fix] Fix custom arguments of `open()` not working as desired

## 1.1.0

- **[add] Allow the passing of Query/Eloquent objets to select fields through `->fromQuery`**
- [fix] Disable form population on password fields
- [fix] Fix uneditable inputs outputing as text fields

## 1.0.0

- Initial release of Former on [Laravel Bundles](http://bundles.laravel.com/bundle/former/)
