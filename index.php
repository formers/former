<!DOCTYPE html>
<html>
<head>
  <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
</head>
<body style='padding: 2rem'>
<?php
require 'vendor/autoload.php';

use Former\Facades\FormerAgnostic as Former;
?>


<?= Former::horizontal_open() ?>
  <?= Former::legend('legend') ?>
    <? Former::populate((object) array('foo_0' => true)); ?>
    <?= Former::checkboxes('foo')->checkboxes('foo', 'bar')->__toString(); ?>>
    <?= Former::text('foo')->required() ?>
  <?= Former::actions()->large_primary_submit() ?>
<?= Former::close() ?>
</body>
</html>