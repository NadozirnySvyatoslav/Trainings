<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once LC_PATH . '/common.php';
include_once __DIR__ . '/../sendmail.php';
class error500 extends NonAuthorizedPage {
	function displayBody() {
		Sendmail::toAdmin ();
		parent::displayBody ();
		$translator = new Translator ();
		
		echo <<< EOF
<div class="container">
      <div class="page-header">
        <h1>{$translator->Error_500_Header}</h1>
      </div>
      <p class="lead">{$translator->Error_500_Desc}</p>
      <p >{$translator->Error_Back}</p>
</div>

EOF;
	}
}
