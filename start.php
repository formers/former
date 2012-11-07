<?php
use \Former\Config;
use \Former\Framework;

// Loading Former -------------------------------------------------- /

Autoloader::namespaces(array(
  'Former' => Bundle::path('former') . 'libraries'
));

// Loading Former configuration ------------------------------------ /

// Fetch configuration files
new Config;

// Set default framework
Framework::useFramework(Config::get('framework'));
