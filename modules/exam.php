<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
class Exam extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	protected $tablename = 'exams';
	protected $checker = array (
			'training_id' => 'trainingCheck' 
	);
	function add($data) {
		if (! isset ( $data ['question_id'] ) || ! isset ( $data ['training_id'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE question_id=$1 AND training_id=$2", array (
				$data ['question_id'],
				$data ['training_id'] 
		) );
		if ($db->valid ())
			throw new Exception ( "Exam exists", self::EXISTS );
		return parent::add ( $data );
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Exam is not exists", self::NOT_EXISTS );
		}
	}
	function trainingCheck($val) {
		return 'exams.training_id = ' . intval ( $val );
	}
}
