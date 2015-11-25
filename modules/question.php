<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
class Question extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	
	// Type
	const SINGLE = 1;
	const MULTI = 2;
	const TEXT = 3;
	public $types = array (
			1 => 'Single',
			2 => 'Multi',
			3 => 'Text' 
	);
	protected $tablename = 'questions';
	protected $checker = array (
			'course_id' => 'courseCheck' 
	);
	function add($data) {
		if (! isset ( $data ['course_id'] ) || ! isset ( $data ['type_id'] ) || ! isset ( $data ['data'] ) || ! isset ( $data ['answer'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE data=$1 AND type_id=$2", array (
				$data ['data'],
				$data ['type_id'] 
		) );
		if ($db->valid () && 0)
			throw new Exception ( "Question exists", self::EXISTS );
		$data ['created'] = date ( 'Y-m-d H:i:s', time () );
		return parent::add ( $data );
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Question is not exists", self::NOT_EXISTS );
		}
	}
	function enumerateIDs($search = null, $start = null, $limit = null, $order = null) {
		$db = new DB ();
		$query = "SELECT id FROM {$this->tablename}";
		if (isset ( $search ))
			$query .= $this->addSearch ( $search );
		if (isset ( $order ))
			$query .= " ORDER BY " . $order;
		if (isset ( $start ))
			$query .= " OFFSET $start";
		if (isset ( $limit ))
			$query .= " LIMIT $limit";
		$db->select ( $query );
		if ($db->valid ())
			return $db;
	}
	function courseCheck($val) {
		return 'questions.course_id = ' . intval ( $val );
	}
}
