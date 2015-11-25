<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
class Plan extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	protected $tablename = 'plans';
	protected $checker = array (
			'active' => 'activeCheck',
			'not_finish' => 'notfinishedCheck' 
	);
	function add($data) {
		if (! isset ( $data ['course_id'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE start=$1 AND course_id=$2", array (
				$data ['start'],
				$data ['course_id'] 
		) );
		if ($db->valid ())
			throw new Exception ( "Plan exists", self::EXISTS );
		$data ['created'] = date ( 'Y-m-d H:i:s', time () );
		return parent::add ( $data );
	}
	function adduser($id, $user_id) {
		$training = new Training ();
		$course_obj = new Course ();
		$plan = $this->get ( array (
				'id' => $id 
		) );
		$course = $course_obj->get ( array (
				'id' => $plan ['course_id'] 
		) );
		
		$data ['course_id'] = $plan ['course_id'];
		$data ['start'] = $plan ['start'];
		$data ['finish'] = $plan ['finish'];
		$data ['user_id'] = $user_id;
		$data ['status_id'] = Training::CREATED;
		if ($course ['format_id'] == Course::ONLINE) {
			$data ['course_hash'] = md5 ( $data ['user_id'] . $data ['course_id'] . $data ['start'] );
		}
		$data ['tries'] = 0;
		if ($course ['exam'] == 't') {
			$data ['exam_hash'] = md5 ( $data ['user_id'] . $data ['course_id'] . $data ['start'] . $data ['tries'] );
		}
		$data ['plan_id'] = $id;
		$training->add ( $data );
	}
	function invert($id) {
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE id=$1", array (
				$id 
		) );
		if ($db->valid ()) {
			$data = $db->current ();
			$data ['active'] = $data ['active'] == 't' ? 'f' : 't';
			$this->update ( $id, array (
					'active' => $data ['active'] 
			) );
			$training = new Training ();
			$training->updateMore ( array (
					'plan_id' => $id 
			), array (
					'active' => $data ['active'] 
			) );
		}
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Plan is not exists", self::NOT_EXISTS );
		}
	}
	function activeCheck($val) {
		return 'active = ' . ($val ? 'true' : 'false');
	}
	function notfinishedCheck($val) {
		return "(finish > 'now' AND start > 'now')";
	}
}
