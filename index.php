<?php
require 'vendor/autoload.php';
use Former\Facades\Agnostic as Former;

$test = Former::checkbox('foo')->text('foo')->__toString();
echo $test;