<?php
include_once __DIR__ . '/db/DB.php';

/*
 * Session class
 * PS: should be with try-catch block around to prevent database errors
 */
class Session {
	const LIFETIME = 14400; // 4 hours
	
	/*
	 * check existing session in browser
	 */
	function __construct() {
		if (isset ( $_COOKIE ['session_id'] ))
			$this->start ();
		else
			$this->create ();
	}
	/*
	 * save session data on end of script
	 */
	function __destruct() {
		if (isset ( $_SESSION )) {
			$this->save ();
		}
	}
	
	/*
	 * save session data and update timestamp in database
	 */
	function save() {
		$db = new DB ();
		$data = serialize ( $_SESSION );
		$db->update ( 'sessions', array (
				'data' => $data,
				'updated' => date ( 'Y-m-d H:i:s', time () ) 
		), array (
				'id' => $_COOKIE ['session_id'] 
		) );
	}
	
	/*
	 * load session data from database
	 */
	function delete() {
		$db = new DB ();
		$db->delete ( 'sessions', array (
				'id' => $_COOKIE ['session_id'] 
		) );
	}
	function start() {
		$db = new DB ();
		$db->select ( "select data from sessions where id = $1 and remote_addr = $2", array (
				$_COOKIE ['session_id'],
				$_SERVER ['REMOTE_ADDR'] 
		) );
		
		if ($db->valid ()) {
			/* Session is saved before and not deleted after lifetime period exceed */
			$res = $db->current ();
			$_SESSION = unserialize ( $res ['data'] );
			setcookie ( 'session_id', $_COOKIE ['session_id'], time () + self::LIFETIME, '/' );
		} else {
			/* Session is not present in database, should create new */
			$this->create ();
		}
	}
	/*
	 * create session data and store ID in browser
	 */
	function create() {
		$db = new DB ();
		$id = md5 ( $_SERVER ['UNIQUE_ID'] . time () );
		$db->insert ( 'sessions', array (
				'id' => $id,
				'data' => '',
				'remote_addr' => $_SERVER ['REMOTE_ADDR'],
				'created' => date ( 'Y-m-d H:i:s', time () ),
				'updated' => date ( 'Y-m-d H:i:s', time () ) 
		) );
		setcookie ( 'session_id', $id, time () + self::LIFETIME, '/' );
		$_COOKIE ['session_id'] = $id;
	}
}
