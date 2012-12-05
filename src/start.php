/*<?php
use \Former\Config;
use \Former\Framework;

// Loading Former -------------------------------------------------- /

Autoloader::namespaces(array(
  'Former' => Bundle::path('former') . 'src' .DS. 'Former',
));

// Loading Former configuration ------------------------------------ /

// Fetch configuration files
new Config;

// Set default framework
$this->app['former.framework']->useFramework(Config::get('framework'));
*/