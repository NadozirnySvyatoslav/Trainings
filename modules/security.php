<?php
include_once __DIR__ . '/user.php';
class Security {
	function check($obj) {
		if (($obj->getRole () == User::GUEST) || // there is no authorization needed
(($obj->getRole () & intval ( $_SESSION ['role_id'] )) > 0) || // check permision
((($obj->getRole () & User::ADMIN) > 0) && ((User::ADMIN_RO & intval ( $_SESSION ['role_id'] )) > 0) 
		&& ($_SERVER ['REQUEST_METHOD'] == 'GET')) || // check ReadOnly permision for admins
((($obj->getRole () & User::EDITOR) > 0) && ((User::EDITOR_RO & intval ( $_SESSION ['role_id'] )) > 0) 
		&& ($_SERVER ['REQUEST_METHOD'] == 'GET')) || // check ReadOnly permision for editors
((User::SUPERUSER & intval ( $_SESSION ['role_id'] )) == User::SUPERUSER)) // superuser access
			return;
		throw new Exception ( 'No access' ); // return authorization error
	}
}
interface Securitable {
	public function getRole();
}
