<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
include_once __DIR__ . '/translator.php'; 
/**
 * @author Nadozirny_SV
 *
 */
class User extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	const BAD_PASSWORD = 1003;
	// ROLES
	const GUEST = 0;
	const SUPERUSER = 1;
	const ADMIN = 2;
	const ADMIN_RO = 4;
	const EDITOR = 32;
	const EDITOR_SIMPLE = 64;
	const EDITOR_RO = 128;
	const USER = 256;
	public $roles = array (
			0,
			1,
			2,
			4,
			32,
			64,
			128,
			256
	);
	protected $tablename = 'users';
	protected $checker = array (
			'role_id' => 'roleCheck' 
	);
	function add($data) {
		if (! isset ( $data ['first_name'] ) || ! isset ( $data ['last_name'] ) || ! isset ( $data ['email'] ) || ! isset ( $data ['password'] ) || ! isset ( $data ['company'] ) || ! isset ( $data ['position'] ) || ! isset ( $data ['country'] ) || ! isset ( $data ['city'] ) || ! isset ( $data ['phone'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		if (sizeof ( $data ['password'] ) < 6 && ($data ['password'] != $data ['password_confirmation']))
			throw new Exception ( 'Bad password', self::BAD_PASSWORD );
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE email=$1", array (
				$data ['email'] 
		) );
		if ($db->valid ())
			throw new Exception ( "User exists", self::EXISTS );
		$data ['name'] = $data ['last_name'] . ' ' . $data ['first_name'];
		$data ['created'] = date ( 'Y-m-d H:i:s', time () );
		$data ['active'] = 'true';
		if (auto_user_approve == 'y')
		    $data ['role_id'] = self::USER;

		return parent::add ( $data );
	}
	function roleCheck($val) {
		return 'role_id & ' . $val . ' > 0';
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "User not exists", self::NOT_EXISTS );
		}
	}
	function find($cond, $limit = null) {
		$db = new DB ();
		
		$query = "SELECT * FROM {$this->tablename} WHERE";
		$i = 1;
		foreach ( array (
				'name',
				'email',
				'company',
				'city' 
		) as $key => $val ) {
			$query .= ' ' . ($i > 1 ? 'OR ' : '') . $val . ' like ' . "'%" . pg_escape_string ( $cond ) . "%'";
			$i ++;
		}
		if (isset ( $limit ))
			$query .= " LIMIT $limit";
		$db->select ( $query );
		if (! $db->valid ())
			throw new Exception ( "Object not exists" );
		return $db;
	}
	function login($cond) {
		$cond ['active'] = 'true';
		$data = $this->get ( $cond );
		$_SESSION ['user'] = $data ['name'];
		$_SESSION ['user_id'] = $data ['id'];
		$_SESSION ['role_id'] = $data ['role_id'];
		$db = new DB ();
		$db->update ( $this->tablename, array (
				'lastin' => date ( 'Y-m-d H:i:s', time () ) 
		), array (
				'id' => $data ['id'] 
		) );
	}
	/**
	 * @param unknown $roles
	 * @return string
	 */
	function RolesAsString($roles) {
		$translator = new Translator();
		if (($roles & self::SUPERUSER) > 0)
			return $translator->Superuser;
		if (intval ( $roles ) == 0)
			return $translator->Guest;
		if (($roles & self::ADMIN) > 0)
			$res = $translator->Admin;
		if (($roles & self::ADMIN_RO) > 0)
			$res = $translator->Admin_ro;
		if (($roles & self::EDITOR) > 0)
			$res .= ($res == '' ? '' : ', ') . $translator->Editor;
		if (($roles & self::EDITOR_RO) > 0)
			$res .= ($res == '' ? '' : ', ') . $translator->Editor_ro;
		if (($roles & self::EDITOR_SIMPLE) > 0)
			$res .= ($res == '' ? '' : ', ') . $translator->Editor_limited;
		if (($roles & self::USER) > 0)
			$res .= ($res == '' ? '' : ', ') . $translator->User;
		return $res;
	}
}
