<?php
require __DIR__ . '/page.php';
include LC_PATH . '/common.php';
include_once __DIR__ . '/../translator.php';
class NonAuthorizedPage extends Page {
	function displayFooter() {
		echo <<< EOF
<footer class="footer">
      <div class="container">
        <p class="text-muted">© 2007-2014 Група компаній «РЕНОМЕ»</p>
      </div>
</footer>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>

EOF;
		parent::displayFooter ();
	}
	/**
	 * Display default decoration of non-authorized pages
	 */
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		echo <<< EOF
<nav class="navbar navbar-fixed-top navbar-inverse ">
    <div class="container-fluid">
        <div class="navbar-header">
         <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><img id="logo" src="http://renome-smart.com/assets/images/Home/Logo-UA.png"></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a id="flag_ua" href="/setlanguage/uk" title="Українська">&nbsp;&nbsp;&nbsp;</a></li>
            <li><a id="flag_ru" href="/setlanguage/ru" title="Русский">&nbsp;&nbsp;&nbsp;</a></li>
            <li><a id="flag_en" href="/setlanguage/en" title="English">&nbsp;&nbsp;&nbsp;</a></li>
          </ul>
    </div>
    </div>
</nav>

EOF;
	}
}
