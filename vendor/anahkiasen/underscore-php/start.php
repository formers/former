<?php

//////////////////////////////////////////////////////////////////////
///////////////////////// START FILE FOR LARAVEL /////////////////////
//////////////////////////////////////////////////////////////////////

// Autoload Underscore's namespace
Autoloader::namespaces(array(
  'Underscore' => Bundle::path('underscore') . 'src' .DS. 'Underscore',
));

// Alias the main class according to user config
$alias = Config::get('underscore::underscore.alias');
Autoloader::alias('Underscore\Underscore', $alias);

/**
 * Shortcut alias to creating an Underscore object
 *
 * @param array $array An array to wrap
 * @return Underscore
 */
function underscore($array)
{
  return new Underscore\Underscore($array);
}
