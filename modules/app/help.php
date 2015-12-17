<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';

class HelpPage extends AuthorizedPage {
	function init(){
		if (isset($_GET['file'])){
			$filename=$_SERVER['DOCUMENT_ROOT'].'/../files/docs/'.preg_replace('/\//','',$_GET['file']);
			if (file_exists($filename)){
				header("Content-type: application/pdf");
				header("Content-Disposition: inline; filename=\"$filename\"");
				readfile($filename);
				exit;
			}
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		
		echo <<< EOF
<div class="container-fluid">
<div class="col-md-10 col-lg-8 col-lg-offset-2 ">
	<h1 class="page-header">{$translator->Help} <small>{$translator->Help_desc}</small></h1>
		<ul>
			<li><a href="?file=LMS - Editor.pdf">Створення і керування переліком курсів в системі (Укр)</a></li>
		</ul>
	</div>
</div> <!-- /container -->

EOF;
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
