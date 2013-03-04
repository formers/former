<?php

// Loading Former -------------------------------------------------- /

Autoloader::namespaces(array(
  'Former'     => Bundle::path('former') . 'src'    .DS. 'Former',
  'HtmlObject' => Bundle::path('former') . 'vendor' .DS. 'anahkiasen/html-object/src/HtmlObject',
  'Illuminate' => Bundle::path('former') . 'vendor' .DS. 'illuminate/container/Illuminate',
  'Underscore' => Bundle::path('former') . 'vendor' .DS. 'anahkiasen/underscore-php/src/Underscore',
));
