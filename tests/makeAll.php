<?php
error_reporting ( E_ERROR | E_PARSE | E_WARNING );
require __DIR__ . '/../config.php';
function __autoload($classname) {
	include __DIR__ . '/' . $classname . '.php';
}

$test1 = new Session_test ();
$test1->run ();
/*
$test=new DB_test();
$test->run();
*/