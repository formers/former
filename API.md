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