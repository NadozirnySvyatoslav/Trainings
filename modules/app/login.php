<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/loginuser.php';
include_once __DIR__ . '/../user.php';
include_once __DIR__ . '/../sendmail.php';
class LoginPage extends NonAuthorizedPage {
	const OK = 0;
	const ERROR = 1;
	function __construct() {
		if (! isset ( $_POST )) {
			header ( 'Location: /' );
		} else {
			
			$this->checkUser ();
		}
	}
	function checkUser() {
		$user = new User ();
		try {
			$user->login ( $_POST );
			
			$this->result = self::OK;
		} catch ( Exception $e ) {
			$this->result = $e->getCode ();
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_login}</h1>
      </div>

EOF;
		
		switch ($this->result) {
			case self::OK :
				echo "   <p class=\"lead\">{$translator->User_login_ok_Desc}</p>
<script type=\"text/javascript\">
window.location.href='/';
</script>
";
				break;
			case User::NOT_EXISTS :
				echo "   <p class=\"lead\">{$translator->User_notexists_Error_Desc}</p>";
				break;
			default :
				echo "   <p class=\"lead\">{$translator->User_login_Error_Desc}</p>";
		}
		echo <<< EOF
      <p >{$translator->Error_Back}</p>
</div>

EOF;
	}
}
