<?php
require 'vendor/autoload.php';
use Former\Facades\Agnostic as Former;

$test = Former::select('foo')->options(array('foo' => 'bar', 'kal' => 'ter'));
dd($test);