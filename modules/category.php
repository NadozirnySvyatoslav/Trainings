<?php
include_once __DIR__ . '/generic_object.php';
include_once __DIR__ . '/db/DB.php';
/**
 *
 * @author Nadozirny_SV
 *        
 */
class Category extends GenericObject {
	// ERROR CODES
	const EXISTS = 1000;
	const NOT_EXISTS = 1001;
	const NO_DATA = 1002;
	protected $tablename = 'categories';
	protected $checker = array (
			'active' => 'activeCheck' 
	);
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see GenericObject::add()
	 */
	function add($data) {
		if (! isset ( $data ['name'] )) {
			throw new Exception ( "No data", self::NO_DATA );
		}
		
		$db = new DB ();
		$db->select ( "SELECT * FROM {$this->tablename} WHERE name=$1", array (
				$data ['name'] 
		) );
		if ($db->valid ())
			throw new Exception ( "Category exists", self::EXISTS );
		return parent::add ( $data );
	}
	
	/**
	 *
	 * @param unknown $id        	
	 */
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
		}
	}
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see GenericObject::get()
	 */
	function get($cond) {
		try {
			return parent::get ( $cond );
		} catch ( Exception $e ) {
			throw new Exception ( "Category is not exists", self::NOT_EXISTS );
		}
	}
	/**
	 *
	 * @param unknown $val        	
	 * @return string
	 */
	function activeCheck($val) {
		return 'active = ' . ($val ? 'true' : 'false');
	}
}
