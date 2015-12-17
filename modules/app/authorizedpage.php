<?php
require __DIR__ . '/page.php';
include LC_PATH . '/common.php';
include_once __DIR__ . '/../translator.php';
class AuthorizedPage extends Page {
	function __construct($param = null) {
		$this->defaultRole ();
		parent::__construct ( $param );
	}
	function defaultRole() {
		$this->role = User::USER;
	}
	function displayFooter() {
		echo <<< EOF
	</div>
    </div>
</div>
<footer class="footer">
      <div class="container">
        <p class="text-muted">© 2007-2014 Група компаній «РЕНОМЕ»</p>
      </div>
</footer>
<div class="scroll-top-wrapper ">
    <span class="scroll-top-inner">
<i class="glyphicon glyphicon-upload"></i>
    </span>
</div>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/scrollToTop.js"></script>

EOF;
		
		parent::displayFooter ();
	}
	function buildMenu() {
		include __DIR__ . '/menu.php';
		
		$translator = new Translator ();
		echo <<< EOF
<div class="container-fluid">
      <div class="row">
        <div id="dashboard" class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">

EOF;
		
		include_once __DIR__ . '/../training.php';
		$training = new Training ();
		$t_cnt = $training->getActiveCount ( $_SESSION ['user_id'] );
		
		foreach ( $user_menu as $title => $url ) {
			if ($t_cnt > 0 && $url == '/mycourses') {
				$badge = " <span class=\"badge\">$t_cnt</span>";
			} else {
				$badge = '';
			}
			
			echo "<li" . ($url == $_SERVER ['REDIRECT_URL'] ? ' class="active"' : '') . "><a href=\"$url\">$title" . $badge . "</a></li>\n";
		}
		echo <<< EOF
          </ul>

EOF;
		if (($_SESSION ['role_id'] & (User::SUPERUSER | User::ADMIN | User::ADMIN_RO)) > 0) {
			include_once __DIR__ . '/../request.php';
			$request = new Request ();
			$r_cnt = $request->getCount ( array (
					'active' => 'true' 
			) );
			
			echo <<< EOF
          <ul class="nav nav-sidebar">
	    <li ><div>{$translator->Administration}</div></li>

EOF;
			foreach ( $admin_menu as $title => $url ) {
				if ($r_cnt > 0 && $url == '/admin_requests') {
					$badge = " <span class=\"badge\">$r_cnt</span>";
				} else {
					$badge = '';
				}
				
				echo "<li" . ($url == $_SERVER ['REDIRECT_URL'] ? ' class="active"' : '') . "><a href=\"$url\">$title" . $badge . "</a></li>\n";
			}
			
			echo <<< EOF
          </ul>

EOF;
		}
		if (($_SESSION ['role_id'] & (User::SUPERUSER | User::EDITOR_RO | User::EDITOR | User::EDITOR_SIMPLE)) > 0) {
				
			echo <<< EOF
          <ul class="nav nav-sidebar">
	    <li ><div>{$translator->Editor}</div></li>
		
EOF;
			foreach ( $editor_menu as $title => $url ) {
				$badge='';
				echo "<li" . ($url == $_SERVER ['REDIRECT_URL'] ? ' class="active"' : '') . "><a href=\"$url\">$title" . $badge . "</a></li>\n";
			}
				
			echo <<< EOF
          </ul>
		
EOF;
		}	
		
		echo <<< EOF
        </div>
EOF;
	}
	function addPaginator($cnt, $activepage = 0) {
		$pagination = '<ul class="pagination">' . NL;
		for($i = 0; $i < $cnt / ITEMS_IN_PAGE; $i ++) {
			$pagination .= "<li" . ($activepage == $i ? ' class="active"' : '') . "><a href=\"?page=$i\">" . ($i + 1) . "</a></li>" . NL;
		}
		$pagination .= '</ul>' . NL;
		return $pagination;
	}
	
	/**
	 * Display default decoration of authorized pages
	 */
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		echo <<< EOF
<nav class="navbar navbar-fixed  navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
         <button type="button" class="navbar-toggle collapsed btn btn-default" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="glyphicon glyphicon-menu-hamburger"></span>
          </button>
          <a class="navbar-brand" href="/"><img id="logo" src="http://renome-smart.com/assets/images/Home/Logo-UA.png"></a>
         <button type="button" class="navbar-toggle collapsed btn btn-default" data-toggle="collapse" data-target="#dashboard" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle Dashboard</span>
            <span class="glyphicon glyphicon-th-list"></span>
          </button>
        </div>
	<div id="navbar" class="navbar-collapse collapse">
	  <form class="navbar-form navbar-left" role="search" action="/search" method="get">
	    <div class="input-group">
        	<input type="text" class="form-control" placeholder="{$translator->Search}" name="search" required>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-default">
		    		<span class="glyphicon glyphicon-search"></span> {$translator->Search_Button}
				</button>
			</span>	
	    </div>
          </form>
	    <ul class="nav navbar-nav navbar-right">
        	<li><a href="/help">{$translator->Help}</a></li>
    		<li><a href="/profile" >{$_SESSION[user]}</a></li>
			<li><a href="/logout" >{$translator->Logout}</a></li>
	    </ul>
        </div>
    </div>
</nav>

EOF;
		$this->buildMenu ();
		
		echo <<<EOF
        <div class="col-sm-9 col-sm-offset-0 col-md-10 col-md-offset-0 main">

EOF;
	}
}
