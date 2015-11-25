<?php
require __DIR__ . '/class_Test.php';
require __DIR__ . '/../modules/session.php';
class Session_test extends Test {
	function testCreate() {
		Session::create ();
	}
	function testSave() {
		Session::save ();
	}
	function testLoad() {
		Session::start ();
	}
	function testDelete() {
		Session::delete ();
	}
	function testCookie() {
		if (! isset ( $_COOKIE ['session_id'] ))
			throw new Exception ( 'Cookies not set' );
	}
	function run() {
		$this->doTest ( array (
				$this,
				'testDelete' 
		) );
		$this->doTest ( array (
				$this,
				'testCookie' 
		) );
		$this->doTest ( array (
				$this,
				'testCreate' 
		) );
		$this->doTest ( array (
				$this,
				'testSave' 
		) );
		$this->doTest ( array (
				$this,
				'testLoad' 
		) );
	}
	function getName() {
		return "Test sessions";
	}
	function provider() {
	}
}

