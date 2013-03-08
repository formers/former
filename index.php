<?php
require 'vendor/autoload.php';
use Former\Facades\Agnostic as Former;

$test = Former::checkbox('foo')->check()->__toString();
dd($test);