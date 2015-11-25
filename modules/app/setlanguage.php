<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../session.php';
class SetLanguagePage extends NonAuthorizedPage {
	function __construct($param) {
		$l = array_shift ( $param );
		$_SESSION ['locale'] = $l;
		setcookie ( 'locale', $l, time () + 14400, '/' );
		header ( 'Location: /' );
	}
}
