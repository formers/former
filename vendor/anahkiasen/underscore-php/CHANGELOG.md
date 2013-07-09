Underscore.php
=====

1.2.1
-----

- Added `String::isIp`, `String::isEmail` and `String::isUrl` from @robclancy Str class
- Added `String::prepend` and `String::append`
- Added `String::baseClass` to get the class out of a namespace (ie `Class` from `Namespace\My\Class`)

1.2.0
-----

- Underscore.php now uses Illuminate's String class instead of Laravel 3's
- The `Underscore::chain` method was renamed to `Underscore::from` to match Repositories behavior

1.1.1
-----

- Parse::toArray will now use existing `toArray` method on objects if existing
- Add various case switchers (`toPascalCase`, `toSnakeCase`, `toCamelCase`)
- Add `Arrays::replaceKeys` to swap all the keys of an array
- Add possibility to change which character `Arrays::flatten` uses to flatten arrays
- Make Repositories use `Parse::toString` on `__toString`

1.1.0
-----

- Add String::randomStrings
- Repositories can now call the `->isEmpty` method to check if the subject is empty
- Type classes now convert their subjects, meaning an object passed to an `Arrays::from` will convert the object to array
- Parse::toInteger($string) now returns the length of the string
- Fix bug with some native PHP functions when chaining
- Fix bug with type routing

1.0.0
-----

- Intial release of Underscore.php
- Type classes are now extendable
- Macros can't conflict between types
- Added Arrays::replaceValue to do an str_replace