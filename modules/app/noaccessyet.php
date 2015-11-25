<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once LC_PATH . '/common.php';
include_once __DIR__ . '/../sendmail.php';
class NoAccessYet extends NonAuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		
		echo <<< EOF
<div class="container">
      <div class="page-header">
        <h1>{$translator->Error_No_access_yet_Header}</h1>
      </div>
      <p class="lead">{$translator->Error_No_access_yet_Desc}</p>
      <p >{$translator->Error_Back}</p>
</div>

EOF;
		if (isset ( $_COOKIE )) {
			Session::delete ();
		}
	}
}
