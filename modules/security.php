<?php
include_once __DIR__ . '/user.php';
include_once __DIR__ . '/course.php';
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
		throw new NoAccessException ( 'No access' ); // return authorization error
	}
	function checkEditor($id) {
		if (((User::EDITOR ) & intval ( $_SESSION ['role_id'] )) > 0) // check permision
			return;
		try{
    		$course=new Course();
		$result=@$course->get(array('id' => $id, 'author_id' => $_SESSION['user_id']));
		if (isset($result) && ((User::EDITOR_SIMPLE & intval ( $_SESSION ['role_id'] )) > 0)) return;
		}catch(Exception $e){	
		    throw new NoAccessException ( 'No access' ); // return authorization error
		}
	}

}
interface Securitable {
	public function getRole();
}

class NoAccessException extends Exception{
}