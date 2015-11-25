<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once LC_PATH . '/common.php';
class error403 extends NonAuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		
		echo <<< EOF
<div class="container">
      <div class="page-header">
        <h1>{$translator->Error_403_Header}</h1>
      </div>
      <p class="lead">{$translator->Error_403_Desc}</p>
      <p >{$translator->Error_Back}</p>
</div>

EOF;
	}
}
