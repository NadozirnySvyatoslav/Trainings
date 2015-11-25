<?php
include_once __DIR__ . '/../user.php';
include_once __DIR__ . '/../security.php';
abstract class Page implements Securitable {
	protected $role = User::GUEST;
	protected $param;
	function __construct($param = null) {
		try {
			Security::check ( $this );
		} catch ( Exception $e ) {
			header ( 'Location: /403' );
		}
		$this->param = $param;
		$this->init ();
	}
	function init() {
	}
	function getRole() {
		return $this->role;
	}
	function displayHeader() {
		include __DIR__ . '/../../html/header.php';
	}
	function displayFooter() {
		include __DIR__ . '/../../html/footer.php';
	}
	function displayBody() {
		echo <<< EOF
</head>
<body>

EOF;
	}
	function finish() {
		echo <<< EOF
</body>
</html>

EOF;
	}
	function display() {
		$this->displayHeader ();
		$this->displayBody ();
		$this->displayFooter ();
		$this->finish ();
	}
}
