<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
class Trainer extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	protected $tablename = 'trainers';
	function add($data) {
		if (! isset ( $data ['name'] ) || ! isset ( $data ['email'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE name=$1", array (
				$data ['name'] 
		) );
		if ($db->valid ())
			throw new Exception ( "Trainer exists", self::EXISTS );
		return parent::add ( $data );
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Trainer not exists", self::NOT_EXISTS );
		}
	}
}
