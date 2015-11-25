<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/signin.php';
include LC_PATH . '/common.php';
class SigninPage extends NonAuthorizedPage {
	function displayHeader() {
		parent::displayHeader ();
		echo <<<EOF
<link href="/css/signin.css" rel="stylesheet">
<link href="/css/full.css" rel="stylesheet">

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		echo <<< EOF
<div class="container">
    <div class="row">
	<div id="title"><h2>{$translator->Title}</h2></div>
    </div>
    <form class="form-signin" action="/login" method="post">
        <h2 class="form-signin-heading"><small>{$translator->Please_sign_in}</small></h2>
        <label for="inputEmail" class="sr-only">{$translator->Email_address}</label>
        <input name="email" type="email" id="inputEmail" class="form-control" placeholder="{$translator->Email_address}" required autofocus>
        <label for="inputPassword" class="sr-only">{$translator->Password}</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="{$translator->Password}" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">{$translator->Sign_in}</button>
	<ul class="nav nav-pills row">
	 <li role="presentation col-md-1">
         <a href="/register_user"><span class="glyphicon glyphicon-user"></span> {$translator->Register_user}</a></li>
	 <li role="presentation  col-md-1">
         <a href="/recover_password"><span class="glyphicon glyphicon-info-sign" ></span> {$translator->Recover_password}</a></li>
	</ul>
    </form>
</div> <!-- /container -->

EOF;
	}
}
