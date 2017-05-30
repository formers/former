# CHANGELOG

## 4.0.0
- Laravel 5+ branch

## 3.5.3

### Added
- Allow repopulating multiselects from Laravel relationships
- Support checkbox repopulation from models

### Fixed
- Handle dot notation in live validation
- Don't repopulate _token fields
- Encode values of hidden fields

## 3.5.2

### Fixed
- Fix the `bind` function for checkables
- Fixed an error when manually creating checkables

## 3.5.1

### Added
- Added support for Foundation 5
- Added PlainText field type
- Added `->bind()` method to fields to change which binding to use for repopulation

### Changed
- Peformance tweaks (framework caching)

### Fixed
- Fixed position of MAX_FILE_SIZE field
- Fixed Laravel 5 compatibility
- Fixed wrong class for inline checkables on Bootstrap 3

## 3.5.0

### Changed
- Bumped minimum requirement to 5.4

## 3.4.4

### Deprecated
- Last release for PHP 5.3

## 3.4.3

### Added
- Added step parameter to `Select::range()` method
- Allow individual checkboxes to override the global push-checkbox setting

### Changed
- Add some additional options for Foundation 4
- Allow translation method to fetch nested keys via dot or brackets

### Fixed
- Fix some repopulation issues

## 3.4.2

### Added
- Added the ability to specify multiple namespaces to look for fields in the MethodDispatcher
- Appended buttons are now properly wrapped in input-group-btn in Bootstrap 3

### Fixed
- Fixed a bug where wrong items would get selected in optgroups
- Fixed some bug when fetching data from the request
- Fixed spaces in validation rules causing errors

## 3.4.1

### Added
- Added support for passing MessageBag instances to `withErrors`

### Changed
- MethodDispatcher can now look for field classes in multiple namespaces
- Use objects' `toArray` instead of array casting when possible

### Fixed
- Fix framework classes overwriting classes predefined on Field classes
- Fix stability problems that prevented Former form being installed

## 3.4.0

### Added
- Added `Former::rawOpen` to open a temporary no-framework/no-label form
- Added support for camelCase form openers (ie. `verticalOpen` and `vertical_open`)
- Added possibility to disable automatic capitalization of translations

### Fixed
- Fixed a bug where two fields with the same name would get the same ID
- Various bugfixes related to repopulation
- Fix various memory and 4.1 compatibility issues

## 3.3.0

### Added
- Add ability to pass attributes to a select's options
- Add support for PATCH method
- Add ability to create range number fields (`Former::number('foo')->range(1, 5)` sets the `min` to 1 and `max` to 5)
- Added Form->route and Form->controller to set a form's action to a route/controller path, and the corresponding method

### Changed
- Allow switching to alternate icon fonts
- Form classes are now framework-dependant
- More work on the Bootstrap 3 integration
- Prevent custom groups from responding to errors from non-grouped fields

### Fixed
- Fix bug in selection false values in Selects
- Fix bug where selects with optgroups weren't populated correctly

## 3.2.0

### Changed
- Updated TwitterBootstrap3 to the latest release
- Former now handles camelCase attributes (ie. `dataPlaceholder` for `data-placeholder`)
- `$group->getErrors()` is now public

## 3.1.0

### Added
- You can now configure which attributes are translated by default
- Added the `TwitterBootstrap3` framework
- Add a second argument to `Former::group` that allows specifying which errors should be displayed
- Add ability to interact with the Group's method by using `onGroup{method}` (ex: `onGroupAddClass`)

### Changed
- All fields are now displayed as "raw" by default in custom groups

### Fixed
- Fix some checkable bugs

## 3.0.0

### Added
- Refactor of Former – the project is now framework agnostic, see installation details
- You can now chain methods to actions blocks `Former::actions('Hello', 'Mr Bond')->id('foo')->addClass('bar')`
- You can now chain buttons to actions blocks `Former::actions()->large_primary_submit('Submit')`
- You can now chain live validation rules to fields (example: `Former::text('foo')->alpha_dash()`)
- You can now display a single field without control group in any kind of form (`Former::text('foo')->raw()`)
- Frameworks names renamed from `bootstrap` to `TwitterBootstrap`, `zurb` to `ZurbFoundation` and `null` to `Nude`
- You can now manually open groups via `Former::group('label')`
- You can also create a group that contains raw content (not a field) with `Former::group('label')->contents('YourContent')`. This will wrap the content in a control class so that your content is aligned with the fields.
- Checkables now handle being populated from relations
- You can now add classes to the group via the `->addGroupClass` method
- Former::withRules() now also take a Laravel-formatted string of rules (ie. "rule|rule:parameter|rule")
- You can now populate on a form-basis with the chained method `->populate` on a form opener
- Add support for macros with Former::macro($name, $macro())
- Add Select->range() method
- Former now automatically inserts a token in the form
- Support for select groups

## 2.6.0

### Added
- 'required_text' to append a text to required fields's labels
- Former::open()->rules([]) as alias to Former::withRules

### Fixed
- Fix a bug where labels for radios would fail because of identical ids
- Fixed a bug where ->populateField would fail if the form was populated with an object

## 2.5.0

### Added
- Add basic button class that allow Bootstrappy submit/buttons
- ControlGroup->prependIcon and appendIcon methods
- Ability to pass an array of classes to add to Field->addClass

### Fixed
- Fix instantiated classes bug in PHP 5.3.2
- Fix multiple buttons instances overwriting themselves

## 2.4.0

### Added
- Form openers are now objects too and accept chained methods
- Add `unchecked_value` option to decide what value unchecked checkboxes have in the POST array
- Allow booleans to be passed to Checkable->check() on single items

### Changed
- Disable `push_checkbox` option by default

### Fixed
- Automatically fetch Lang objects passed to `->options`

## 2.3.0

### Added
- Add `push_checkboxes` option which forces the submiting of unchecked fields to the POST array

## 2.2.0

### Added
- Add `Former::file()` and `Former::files()` with methods `->max` and `->accept`
- Add ability to set a placeholder option for select fields
- Add ability to set attributes for a label

## 2.1.0

### Added
- Add ability to populate field with a model's relationships
- Added `->check()` method on radios and checkboxes

## 2.0.0

### Added
- Former now uses Laravel's Config class to manage settings, letting the user create a `former.php` file in `application/config` to override default options
- Add option to disable automatic labeling of fields

### Fixed
- Fix translation of empty strings

## 1.2.1

### Added
- Fetch automatically key and value from models through `get_key` and `__toString`
- Add `Former::populateField` to populate a specific field

### Fixed
- Fixed a bug preventing from using one Former call to output several times

## 1.2.0

### Added
- Add suport for Zurb's Foundation framework
- Allow the passing of Query/Eloquent objects to text fields through `->useDatalist`
- Add option to desactivate live validation

### Changed
- Allow public use of `Former::getErrors()`
- Let user specify a custom id for generated datalists

### Fixed
- Don't create a label tag around checkboxes if the label is empty
- Fix custom arguments of `open()` not working as desired

## 1.1.0

### Added
- Allow the passing of Query/Eloquent objets to select fields through `->fromQuery`

### Fixed
- Disable form population on password fields
- Fix uneditable inputs outputing as text fields

## 1.0.0

### Added
- Initial release of Former on [Laravel Bundles](http://bundles.laravel.com/bundle/former/)
