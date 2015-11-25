<?php
require __DIR__ . '/../modules/db/DB.php';
require __DIR__ . '/class_Test.php';
class DB_test extends Test {
	private $db;
	const RECORDS = 100;
	function testConnect() {
		$this->db = new DB ();
	}
	function testCreateTable($name) {
		$this->db->query ( "create table $name(id int,data varchar)" );
	}
	function testInsert($arr) {
		foreach ( $arr [data] as $key => $val )
			$this->db->insert ( $arr [table], $val );
	}
	function testUpdate($arr) {
		foreach ( $arr [data] as $key => $val )
			$this->db->update ( $arr [table], $val, $val );
	}
	function testSelect($name) {
		$this->db->select ( "SELECT * FROM $name" );
		$i = 0;
		foreach ( $this->db as $key => $val ) {
			$i ++;
		}
		echo "rows:" . $i . NL;
	}
	function testDelete($arr) {
		foreach ( $arr [data] as $key => $val )
			$this->db->delete ( $arr [table], $val );
	}
	function testDropTable($name) {
		$this->db->query ( "drop table $name" );
	}
	function run() {
		$this->doTest ( array (
				$this,
				"testConnect" 
		) );
		$this->doTest ( array (
				$this,
				"testCreateTable" 
		) );
		$this->doTest ( array (
				$this,
				"testInsert" 
		) );
		$this->doTest ( array (
				$this,
				"testUpdate" 
		) );
		$this->doTest ( array (
				$this,
				"testSelect" 
		) );
		$this->doTest ( array (
				$this,
				"testDelete" 
		) );
		$this->doTest ( array (
				$this,
				"testDropTable" 
		) );
	}
	function getName() {
		return "Database test - 3 tables with " . self::RECORDS . " records";
	}
	function provider() {
		for($i = 0; $i < self::RECORDS; $i ++) {
			$insertdata [] = array (
					'id' => $i,
					'data' => "data $i" 
			);
		}
		$this->add ( "testCreateTable", array (
				'table1',
				'table2',
				'table3' 
		) );
		$this->add ( "testDropTable", array (
				'table1',
				'table2',
				'table3' 
		) );
		$this->add ( "testInsert", array (
				array (
						'table' => 'table1',
						'data' => $insertdata 
				),
				array (
						'table' => 'table2',
						'data' => $insertdata 
				),
				array (
						'table' => 'table3',
						'data' => $insertdata 
				) 
		) );
		$this->add ( "testUpdate", array (
				array (
						'table' => 'table1',
						'data' => $insertdata 
				),
				array (
						'table' => 'table2',
						'data' => $insertdata 
				),
				array (
						'table' => 'table3',
						'data' => $insertdata 
				) 
		) );
		$this->add ( "testSelect", array (
				'table1',
				'table2',
				'table3' 
		) );
		$this->add ( "testDelete", array (
				array (
						'table' => 'table1',
						'data' => $insertdata 
				),
				array (
						'table' => 'table2',
						'data' => $insertdata 
				),
				array (
						'table' => 'table3',
						'data' => $insertdata 
				) 
		) );
	}
}

