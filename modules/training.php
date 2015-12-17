<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
class Training extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	
	// STATUSES
	const CREATED = 0;
	const DELETED = 1;
	const STARTED = 2; // training was started and need exam
	const EXAM = 3; // exam was not started
	const EXAM_STARTED = 4; // exam was started
	const FAILED = 5; // exam was failed
	const FINISHED = 6; // not need exam or exam success
	const TIMEOUT = 7; //exceed time limit
	protected $tablename = 'trainings';
	protected $checker = array (
			'active' => 'activeCheck',
			'category_id' => 'categoryCheck',
			'user_id' => 'useridCheck',
			'coursename' => 'coursenameCheck',
			'not_finish' => 'notfinishedCheck',
			'plan_id' => 'planCheck' 
	);
	function add($data) {
		if (! isset ( $data ['course_id'] ) || ! isset ( $data ['user_id'] ) || (! isset ( $data ['request_id'] ) && ! isset ( $data ['plan_id'] ))) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$data ['created'] = date ( 'Y-m-d H:i:s', time () );
		return parent::add ( $data );
	}
	function checkFinish($user_id, $course_id) {
		$db = new DB ();
		$query = "SELECT count(*) as cnt FROM {$this->tablename} " . "WHERE finish > 'now'".
				" AND status_id != " . Training::DELETED . 
				" AND status_id != " . Training::FAILED . 
				" AND status_id != " . Training::TIMEOUT . 
				" AND status_id != " . Training::FINISHED .  
				" AND ( status_id = " . Training::EXAM . " OR finished IS NULL)" . 
				" AND user_id=" . intval ( $user_id ) .
				" AND course_id=" . intval ( $course_id );
		$db->select ( $query );
		if ($db->valid ()) {
			$res = $db->current ();
			return $res ['cnt'];
		}
		return 0;
	}
	function getActiveCount($id) {
		$db = new DB ();
		$query = "SELECT count(*) as cnt  FROM {$this->tablename} WHERE finish > 'now' ".
			" AND status_id != " . Training::DELETED . 
			" AND status_id != " . Training::FAILED . 
			" AND status_id != " . Training::TIMEOUT . 
			" AND status_id != " . Training::FINISHED . 
			" AND active=true".
			" AND user_id=" . intval ( $id );
		$db->select ( $query );
		if ($db->valid ()) {
			$res = $db->current ();
			return $res ['cnt'];
		}
		return 0;
	}
	function getCount($search = null) {
		$db = new DB ();
		$query = "SELECT count(*) as cnt  FROM {$this->tablename}
INNER JOIN courses ON courses.id={$this->tablename}.course_id
INNER JOIN users ON users.id={$this->tablename}.user_id";
		
		if (isset ( $search ))
			$query .= $this->addSearch ( $search );
		$db->select ( $query );
		if ($db->valid ()) {
			$res = $db->current ();
			return $res ['cnt'];
		}
		return 0;
	}
	function enumerate($search = null, $start = null, $limit = null, $order = '1 DESC') {
		$db = new DB ();
		$query = "SELECT trainings.id as id,
    trainings.course_id as course_id,
    courses.name as course_name,
    courses.category_id as category_id,
    courses.category_name as category_name,
    courses.format_id as format_id,
    trainings.user_id as user_id,
    trainings.created as created,
    trainings.start as start,
    trainings.finish as finish,
    trainings.finished as finished,
    trainings.course_hash as course_hash,
    trainings.exam_hash as exam_hash,
    trainings.result as result,
    trainings.questions as questions,
    trainings.answers as answers,
    trainings.tries as tries,
    courses.exam as exam,
    trainings.examed as examed,
    trainings.status_id as status_id
 FROM {$this->tablename}
INNER JOIN courses ON courses.id=trainings.course_id";
		if (isset ( $search ))
			$query .= $this->addSearch ( $search );
		if (isset ( $order ))
			$query .= " ORDER BY $order";
		if (isset ( $start ))
			$query .= " OFFSET $start";
		if (isset ( $limit ))
			$query .= " LIMIT $limit";
		
		$db->select ( $query );
		if ($db->valid ())
			return $db;
	}
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Training is not exists", self::NOT_EXISTS );
		}
	}
	function categoryCheck($val) {
		return 'courses.category_id = ' . intval ( $val );
	}
	function useridCheck($val) {
		return "trainings.user_id=" . intval ( $val );
	}
	function planCheck($val) {
		return "trainings.plan_id=" . intval ( $val );
	}
	function coursenameCheck($val) {
		return "courses.name like '%" . htmlspecialchars ( $val, ENT_QUOTES ) . "%'";
	}
	function activeCheck($val) {
		return 'trainings.active = ' . ($val ? 'true' : 'false');
	}
	function notfinishedCheck($val) {
		return "(trainings.finish > 'now' AND finished is NULL)";
	}
}
