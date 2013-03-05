<?php

$vendor = Bundle::path('former') . 'vendor' .DS;

// Loading Former -------------------------------------------------- /

Autoloader::namespaces(array(
  'Former'     => Bundle::path('former') . 'src' .DS. 'Former',
  'HtmlObject' => $vendor . 'anahkiasen/html-object/src/HtmlObject',
  'Illuminate' => $vendor . 'illuminate/container/Illuminate',
  'Underscore' => $vendor . 'anahkiasen/underscore-php/src/Underscore',
));

// Load Str class -------------------------------------------------- /

include $vendor . 'illuminate/support/Illuminate/Support/Str.php';
include $vendor . 'illuminate/support/Illuminate/Support/Pluralizer.php';
