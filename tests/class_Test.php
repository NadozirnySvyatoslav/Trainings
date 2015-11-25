<?php
define ( NL, "<br/>\n" );

/**
 * Test class for providing tests with modules
 *
 * \brief
 *
 * 1. Create tests by add method testName($param), where "param" kind of provider data (see step 4)
 * 2. Realize run() with:
 * \code
 * $this->doTest(array($this,"testName"));
 * \endcode
 *
 * for each test
 * 3. Realize getName() for set Name of test globally
 * 4. Realize provider() for assign data for tests:
 * \code
 * $this->add('testName',$param);
 * \endcode
 *
 * \authors
 * \version
 * \todo
 */
abstract class Test {
	private $name;
	private $start;
	private $end;
	private $pass;
	
	/**
	 * Constructor
	 * Start time counter and fill provided data for tests
	 */
	function __construct() {
		echo "Begin tests: " . $this->getName () . NL;
		$this->provider ();
		$this->start = microtime ( true );
	}
	
	/**
	 * Destructor
	 * Count time and print Summary spent time
	 */
	function __destruct() {
		$end = microtime ( true );
		echo "Test end in " . ($end - $this->start) . ' sec' . NL;
	}
	
	/**
	 * Add values to tests data pool
	 *
	 * \param[in] name test's name
	 * \param[in] data test's data, type array with enumerated params array('pass1','pass2',...)
	 */
	function add($name, $data) {
		$this->params [$name] = $data;
	}
	
	/**
	 * Test executor
	 *
	 * check test execution to wait Exception
	 * if no Exception - test passed otherwise test not passed
	 *
	 * \param[in] func array(testObject, testName)
	 */
	function doTest($func) {
		$this->pass ++;
		
		if (isset ( $this->params [$func [1]] )) {
			// multiple tests
			for($i = 0; $i < sizeof ( $this->params [$func [1]] ); $i ++) {
				$start = microtime ( true );
				try {
					call_user_func ( $func, $this->params [$func [1]] [$i] );
					$res = "Passed";
				} catch ( Exception $e ) {
					$error = $e->getMessage ();
					$res = "<b>Not passed</b>";
				}
				$end = microtime ( true );
				echo 'Test[' . $this->pass . '/' . $i . ']: ' . $func [1] . '... ' . $res . " in " . ($end - $start) . NL;
				if ($error)
					echo "Error: " . $error . NL;
			}
		} else {
			// single test
			$start = microtime ( true );
			try {
				$ret = call_user_func ( $func );
				$res = "Passed";
			} catch ( Exception $e ) {
				$error = $e->getMessage ();
				$res = "<b>Not passed</b>";
			}
			$end = microtime ( true );
			echo ($res == 'Passed' ? '' : '<font color=red>') . 'Test[' . $this->pass . ']: ' . $func [1] . '... ' . $res . " in " . ($end - $start) . ($res == 'Passed' ? '' : '</font>') . NL;
			if ($error)
				echo "Error: " . $error . NL;
		}
		return $ret;
	}
	
	/**
	 * Provide data for tests
	 */
	abstract function provider();
	/**
	 * Run tests
	 */
	abstract function run();
	/**
	 * Assign test's name
	 * \return Test's name
	 */
	abstract function getName();
}