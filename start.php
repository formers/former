<?php

// Loading Former -------------------------------------------------- /

Autoloader::namespaces(array(
  'Former'     => Bundle::path('former') . 'src'    .DS. 'Former',
  'Illuminate' => Bundle::path('former') . 'vendor' .DS. 'illuminate/container/Illuminate',
  'Underscore' => Bundle::path('former') . 'vendor' .DS. 'anahkiasen/underscore-php/src/Underscore',
));

include 'vendor/meido/form/src/Meido/Form/Form.php';
include 'vendor/meido/html/src/Meido/HTML/HTML.php';