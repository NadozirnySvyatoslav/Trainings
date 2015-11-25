<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../question.php';
include_once __DIR__ . '/../exam.php';

include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/exam.php';

class TestPage extends AuthorizedPage {
	// statuses
	const ERROR = 0;
	const NOTFOUND = 1;
	const TIMEOUT = 2;
	function init() {
		$this->exam_hash = array_shift ( $this->param );

	}
	function displayBody() {
		parent::displayBody ();

		$translator = new Translator ();
		$course = new Course ();
		$category = new Category ();
		$training = new Training ();
		$t_data = $training->get ( array (
				'exam_hash' => $this->exam_hash,
				'user_id' => $_SESSION ['user_id']
		) );
		$c_data = $course->get ( array (
				'id' => $t_data ['course_id']
		) );

		foreach ( $c_data as $key => $val ) {
			$c_data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		$category_id = $c_data ['category_id'];
		;

		$enum = $category->enumerate ();
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
			$categories = $this->makeCategoryList ( $items, $category_id );
		}
		echo <<< EOF
<div class="container-fluid">
<div class="col-lg-10 col-lg-offset-1" id="window">
	<div class="row" >
	<div class="col-xs-10">
	    <h4>$categories</h4>
	    <h2>{$c_data[name]}</h2>
	</div>
	<div class="col-xs-2 text-right" id="top_buttons">
	    <a href="#" id="fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></a>
	    <a href="#" id="inscreen" class="collapse"><span class="glyphicon glyphicon-remove"></span></a>
	</div>
	</div>

EOF;

		try {
			$d2 = new DateTime ( 'now' );
			$diff = $d2->diff ( new DateTime ( $t_data ['finished'] ) );
			$diff = $diff->format ( '%r%i' );
			if ($diff < 0)
				throw new Exception ( "Time out", self::TIMEOUT );
					
				$exam = new Exam ();
				$enum = $exam->enumerate ( array (
						'training_id' => $t_data ['id']
				), intval ( $this->page ), 1, 'id' );
				$cnt = $exam->getCount ( array (
						'training_id' => $t_data ['id']
				) );
				$current = $this->page + 1;
				$back = ($this->page > 0 ? $this->page - 1 : 0);
				$next = $this->page + 1;
					
				if (! $enum)
					throw new Exception ( "Not found", self::NOTFOUND );
					$e_data = $enum->current ();
					$question = new Question ();
					$q_data = $question->get ( array (
							'id' => $e_data ['question_id']
					) );
						
					echo <<< EOF
	<div class="embed-responsive embed-responsive-16by9" id="fullscreen_media">
	<p>{$translator->Question}: {$current} / {$cnt}</p>
	<p> {$translator->Time_remain}: {$diff}</p>
	{$q_data[data]}
	<hr>
	{$translator->Answer}:
	    <form action="?" method="post">
	    <input type="hidden" name="id" value="{$e_data[id]}">
EOF;
	switch ($q_data ['type_id']) {
		case Question::SINGLE :
			echo "<ol>";
			for($i = 1; $i <= $q_data ['count']; $i ++) {
				echo "<li><input type=\"radio\" name=\"answer\" value=\"$i\" " . ($i == $e_data ['answer'] ? 'checked' : '') . "></li>" . NL;
			}
			echo "</ol>";
			break;
		case Question::MULTI :
			echo "<ol>";
			$e_data ['answer'] = explode ( ',', $e_data ['answer'] );
			for($i = 1; $i <= $q_data ['count']; $i ++) {
				echo "<li><input type=\"checkbox\" name=\"answer[]\" value=\"$i\" " . ((in_array ( $i, $e_data ['answer'] )) ? 'checked' : '') . "></li>" . NL;
			}
			echo "</ol>";
			break;
		case Question::TEXT :
			echo "<input type=\"text\" name=\"answer\" value=\"$e_data[answer]\">" . NL;
			break;
		default :
	}
	$back = ($this->page > 0 ? $this->page - 1 : 0);
	$next = $this->page + 1;
		
	echo <<<EOF
	    <input type="submit" class="btn btn-primary">
	    </form>
	</div>

EOF;
		} catch ( Exception $e ) {
			switch ($e->getCode ()) {
				case self::TIMEOUT :
					echo <<< EOF
    <div class="bs-callout bs-callout-danger" id="callout-type-b-i-elems">
    <h4>{$translator->Timeout}</h4>
    <p>{$translator->Timeout_Desc}</p>
    <p class="text-muted">{$translator->BackCourses}</p>
    </div>

EOF;
					$training->update ( $t_data ['id'], array (
							'status_id' => Training::FAILED
					) );
						
					break;
				case self::NOTFOUND :
					echo <<< EOF
    <div class="bs-callout bs-callout-info" id="callout-type-b-i-elems">
    <h4>{$translator->Not_found}</h4>
    <p>{$translator->Not_found_Desc}</p>
    <p class="text-muted">{$translator->BackExam}</p>
    </div>

EOF;
					break;

				default :
					echo $e;
					echo <<< EOF
    <div class="bs-callout bs-callout-danger" id="callout-type-b-i-elems">
    <h4>{$translator->Error}</h4>
    <p>{$translator->Error_Desc}</p>
    <p class="text-muted">{$translator->BackExam}</p>
    </div>

EOF;
			}
			// echo "<script>window.history.back();</script>";
	}
	echo <<< EOF
	<nav id="bottom_buttons">
	  <ul class="pager">
	    <li class="previous"><a href="$back" id="back"><span aria-hidden="true">&larr;</span> {$translator->Back}</a></li>
	    <li class="top"><a href="0" id="top">{$translator->Top}</a>|
		<a href="/test/{$t_data[id]}" onclick='return confirm("{$translator->AreYouSure}");'>{$translator->Finish}</a></li>
	    <li class="next"><a href="$next" id="next">{$translator->Next} <span aria-hidden="true">&rarr;</span></a></li>
	  </ul>
	</nav>
</div>
</div> <!-- /container -->

EOF;
	}
	function makeCategoryList(&$items, $category_id) {
		$category = $items [$category_id] ['name'];
		if ($items [$category_id] ['parent_id'] != 0)
			$category = $this->makeCategoryList ( &$items, $items [$category_id] ['parent_id'] ) . "<span class=\"glyphicon glyphicon-menu-right\"></span>" . $category;
			return $category;
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
