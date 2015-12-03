<?php
error_reporting ( E_ERROR | E_PARSE | E_WARNING );

require __DIR__ . '/config.php';
require __DIR__ . '/modules/app/application.php';

$app = new Application ();

