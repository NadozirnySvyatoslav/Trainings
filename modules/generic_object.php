<?php
include_once __DIR__ . '/db/DB.php';
class GenericObject {
	protected $tablename;
	protected $customSearch;
	protected $checker = null;
	function add($data) {
		$db = new DB ();
		$meta = $db->meta ( $this->tablename );
		foreach ( $meta as $key => $val ) {
			$meta [$key] = $data [$key];
		}
		
		$db->select ( "SELECT id FROM {$this->tablename} ORDER BY id DESC LIMIT 1" );
		if ($db->valid ()) {
			$res = $db->current ();
			$id = $res ['id'] + 1;
		} else {
			$id = 0;
		}
		$meta ['id'] = $id;
		$db->insert ( $this->tablename, $meta );
		return $id;
	}
	function getById($id) {
		$db = new DB ();
		$res = select ( "SELECT * FROM {$this->tablename} WHERE id=$1", array (
				$id 
		) );
		return $db->current ();
	}
	function update($id, $data) {
		$db = new DB ();
		$db->update ( $this->tablename, $data, array (
				'id' => $id 
		) );
	}
	function updateMore($cond, $data) {
		$db = new DB ();
		$db->update ( $this->tablename, $data, $cond );
	}
	function disable($id) {
		$db = new DB ();
		$db->update ( $this->tablename, array (
				'active' => 'false' 
		), array (
				'id' => $id 
		) );
	}
	function enable($id) {
		$db = new DB ();
		$db->update ( $this->tablename, array (
				'active' => 'true' 
		), array (
				'id' => $id 
		) );
	}
	function delete($id) {
		$db = new DB ();
		$db->delete ( $this->tablename, array (
				'id' => $id 
		) );
	}
	function addSearch($search) {
		foreach ( $search as $key => $val ) {
			if ($val != '') {
				if (isset ( $this->checker ) && isset ( $this->checker [$key] ))
					$cond .= ($cond == '' ? '' : ' AND ') . call_user_func ( array (
							$this,
							$this->checker [$key] 
					), $val );
				else
					$cond .= ($cond == '' ? '' : ' AND ') . $key . " like '%" . pg_escape_string ( $val ) . "%'";
			}
		}
		if ($cond != '')
			$query = ' WHERE ' . $cond;
		return $query;
	}
	function getCount($search = null) {
		$db = new DB ();
		$query = "SELECT count(*) as cnt FROM {$this->tablename}";
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
		$query = "SELECT * FROM {$this->tablename}";
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
		$db = new DB ();
		
		$query = "SELECT * FROM {$this->tablename} WHERE";
		$i = 1;
		unset ( $param );
		foreach ( $cond as $key => $val ) {
			$query .= ' ' . ($i > 1 ? 'AND ' : '') . $key . '=' . "'" . pg_escape_string ( $val ) . "'";
			$i ++;
			$param [] = $val;
		}
		$db->select ( $query );
		if (! $db->valid ())
			throw new Exception ( "Object not exists" );
		return $db->current ();
	}
	function deleteMore($cond) {
		$db = new DB ();
		
		$query = "DELETE FROM {$this->tablename} WHERE";
		$i = 1;
		unset ( $param );
		foreach ( $cond as $key => $val ) {
			$query .= ' ' . ($i > 1 ? 'AND ' : '') . $key . '=' . "'" . pg_escape_string ( $val ) . "'";
			$i ++;
			$param [] = $val;
		}
		echo $query;
		$db->query ( $query );
	}
}
