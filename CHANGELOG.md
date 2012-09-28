## Changelog

### 2.*.*

- [add] Added `->check()` method on radios and checkboxes

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