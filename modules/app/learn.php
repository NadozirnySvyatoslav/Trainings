<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/learn.php';
class LearnPage extends AuthorizedPage {
	function init() {
		$this->course_hash = array_shift ( $this->param );
		$page = array_shift ( $this->param );
		$course = new Course ();
		$training = new Training ();
		$t_data = $training->get ( array (
				'course_hash' => $this->course_hash,
				'user_id' => $_SESSION ['user_id'] 
		) );
		
		switch ($t_data ['status_id']) {
			
			case Training::CREATED : // CREATED - ok, go on
			
			case Training::STARTED : // STARTED - ok, go on
				$c_data = $course->get ( array (
						'id' => $t_data ['course_id'] 
				) );
				if ($c_data ['format_id'] == Course::OFFLINE) {
					// only Online format allowed
					if ($c_data ['exam'] == 't') {
						$training->update ( $t_data ['id'], array (
								'status_id' => Training::EXAM 
						) );
					} else {
						$training->update ( $t_data ['id'], array (
								'status_id' => Training::FINISHED 
						) );
					}
					header ( 'Location: /mycourses' );
					exit ();
				} else {
					$training->update ( $t_data ['id'], array (
							'status_id' => Training::STARTED 
					) );
				}
				break;
				
				break;
			case Training::DELETED : // DELETED - not ok
			case Training::EXAM : // only exam, no
			case Training::FAILED : // try exam
			case Training::FINISHED : // register once more time
			default :
				header ( 'Location: /403' );
				exit ();
		}
		
		if (isset ( $page )) {
			// load iframe
			
			$file = preg_replace ( '/(\/[a-zA-Z\-_0-9\.]*\/[a-z0-9]*\/)/', $_SERVER ['DOCUMENT_ROOT'] . '/../files/courses/' . $t_data ['course_id'] . '/', $_SERVER ['REQUEST_URI'] );
			// echo $file;
			if (file_exists ( $file ) && is_file ( $file )) {
				header ( 'Content-type:' );
				readfile ( $file );
			} else {
				echo "Page not found";
				if ($page != 'index.html') {
					echo "<script>window.history.back();</script>";
				}
			}
			exit ();
		}
	}
	function displayFooter() {
		parent::displayFooter ();
		
		echo <<< EOF
<script src="/js/learn.js"></script>

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		
		$translator = new Translator ();
		$course = new Course ();
		$training = new Training ();
		$t_data = $training->get ( array (
				'course_hash' => $this->course_hash,
				'user_id' => $_SESSION ['user_id'] 
		) );
		$c_data = $course->get ( array (
				'id' => $t_data ['course_id'] 
		) );
		
		foreach ( $c_data as $key => $val ) {
			$c_data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		
		echo <<< EOF
<div class="container-fluid">
<div class="col-lg-10 col-lg-offset-1" id="window">
	<div class="row" >
	<div class="col-xs-10">
	    <h4>{$c_data[category_name]}</h4>
	    <h2>{$c_data[name]}</h2>
	</div>
	<div class="col-xs-2 text-right" id="top_buttons">
	    <a href="#" id="fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></a>
	    <a href="#" id="inscreen" class="collapse"><span class="glyphicon glyphicon-remove"></span></a>
	</div>
	</div>
	<div class="embed-responsive embed-responsive-16by9" id="fullscreen_media">
	  <iframe class="embed-responsive-item" src="/learn/{$this->course_hash}/index.html" id="iframe"></iframe>
	</div>
	<nav id="bottom_buttons">
	  <ul class="pager">
	    <li class="previous"><a href="#" id="back"><span aria-hidden="true">&larr;</span> {$translator->Back}</a></li>
	    <li class="top"><a href="#" id="top">{$translator->Top}</a>|
		<a href="/mycourses/finish/{$t_data[id]}" onclick='return confirm("{$translator->AreYouSure}");'>{$translator->Finish}</a></li>
	    <li class="next"><a href="#" id="next">{$translator->Next} <span aria-hidden="true">&rarr;</span></a></li>
	  </ul>
	</nav>
</div>
</div> <!-- /container -->

EOF;
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
