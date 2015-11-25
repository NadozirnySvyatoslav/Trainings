<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
include_once __DIR__ . '/training.php';
include_once __DIR__ . '/course.php';
class Request extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	protected $tablename = 'requests';
	protected $checker = array (
			'active' => 'activeCheck',
			'category_id' => 'categoryCheck',
			'username' => 'usernameCheck',
			'coursename' => 'coursenameCheck' 
	);
	function add($data) {
		if (! isset ( $data ['course_id'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$data ['created'] = date ( 'Y-m-d H:i:s', time () );
		return parent::add ( $data );
	}
	function approve($id, $data = null) {
		$training = new Training ();
		$course_obj = new Course ();
		
		$request = $this->get ( array (
				'id' => $id 
		) );
		$course = $course_obj->get ( array (
				'id' => $request ['course_id'] 
		) );
		
		$data ['course_id'] = $request ['course_id'];
		$data ['start'] = $request ['planned_date'];
		
		$date = new DateTime ( $data ['start'] );
		if ($course ['duration'] > 8) {
			$date->add ( new DateInterval ( 'P' . ceil ( $course ['duration'] / 8 ) . 'DT' . ($course ['duration'] % 8) . 'H' ) );
		} else {
			$date->add ( new DateInterval ( 'PT' . $course ['duration'] . 'H' ) );
		}
		$data ['finish'] = $date->format ( 'Y-m-d H:i' );
		$data ['user_id'] = $request ['user_id'];
		
		if ($course ['format_id'] == Course::ONLINE) {
			$data ['course_hash'] = md5 ( $data ['user_id'] . $data ['course_id'] . $data ['start'] );
		}
		$data ['tries'] = 0;
		if ($course ['exam'] == 't') {
			$data ['exam_hash'] = md5 ( $data ['user_id'] . $data ['course_id'] . $data ['start'] . $data ['tries'] );
		}
		$data ['request_id'] = $id;
		$data ['status_id'] = Training::CREATED;
		$data ['active'] = 'true';
		$training->add ( $data );
		$this->disable ( $id );
	}
	function getCount($search = null) {
		$db = new DB ();
		$query = "SELECT count(*)as cnt  FROM {$this->tablename}";
		
		if (isset ( $search ))
			$query .= $this->addSearch ( $search );
		$db->select ( $query );
		if ($db->valid ()) {
			$res = $db->current ();
			return $res ['cnt'];
		}
		return 0;
	}
	function enumerate($search = null, $start = null, $limit = null, $order = null) {
		$db = new DB ();
		$query = "SELECT requests.id as id,
    requests.course_id as course_id,
    courses.name as course_name,
    courses.category_id as category_id,
    requests.user_id as user_id,
    requests.created as created,
    users.name as user_name,
    users.company as user_company,
    users.position as user_position,
    requests.planned_date as planned_date,
    requests.active as active
 FROM {$this->tablename}
INNER JOIN courses ON courses.id=requests.course_id
INNER JOIN users ON users.id=requests.user_id";
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
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Request is not exists", self::NOT_EXISTS );
		}
	}
	function categoryCheck($val) {
		return 'courses.category_id = ' . intval ( $val );
	}
	function usernameCheck($val) {
		return "users.name like '%" . htmlspecialchars ( $val, ENT_QUOTES ) . "%'";
	}
	function coursenameCheck($val) {
		return "courses.name like '%" . htmlspecialchars ( $val, ENT_QUOTES ) . "%'";
	}
	function activeCheck($val) {
		return 'requests.active = ' . ($val ? 'true' : 'false');
	}
}
