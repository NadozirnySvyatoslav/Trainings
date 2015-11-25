<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../session.php';
class LogoutPage extends NonAuthorizedPage {
	function __construct() {
		if (isset ( $_COOKIE )) {
			Session::delete ();
		}
		header ( 'Location: /' );
	}
}
