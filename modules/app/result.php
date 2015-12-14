<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../question.php';
include_once __DIR__ . '/../exam.php';

include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/result.php';
class ResultPage extends AuthorizedPage {
	// statuses
	const ERROR = 0;
	const NOTFOUND = 1;
	const TIMEOUT = 2;
	// minimal percent for success exam, %
	const MIN_RESULT_FOR_SUCCESS = 75; // 75%
	function init() {
		$this->id = array_shift ( $this->param );
		$course = new Course ();
		$training = new Training ();
		$t_data = $training->get ( array (
				'id' => $this->id,
				'user_id' => $_SESSION ['user_id'] 
		) );
		$c_data = $course->get ( array (
				'id' => $t_data ['course_id'] 
		) );
		switch ($t_data ['status_id']) {
			case Training::EXAM_STARTED :
				// check result for test and set status FAILED or FINISHED
				$questions = 0;
				$result = 0;
				$right_answers = 0;
				if ($t_data ['finished'] < date ( 'Y-m-d H:i', time () )) {
					$result = 0;
					$status = Training::FAILED;
				} else {
					$exam = new Exam ();
					$enum = $exam->enumerateResult ( $t_data ['id'] );
					
					foreach ( $enum as $key => $e_data ) {
						$questions ++;
						if ($e_data ['result'] == 't') {
							$right_answers ++;
						}
					}
					if ($questions > 0) {
						$result = intval ( $right_answers * 100 / $questions );
					}
					if ($result >= self::MIN_RESULT_FOR_SUCCESS) {
						$status = Training::FINISHED;
					} else {
						$status = Training::FAILED;
					}
				}
				
				$t_data ['tries'] = intval ( $t_data ['tries'] ) + 1;
				$training->update ( $t_data ['id'], array (
						'status_id' => $status,
						'result' => $result,
						'answers' => $right_answers,
						'questions' => $questions,
						'finished' => date ( 'Y-m-d H:i', time () ) 
				) );
				break;
			default :
		}
	}
	function displayBody() {
		parent::displayBody ();
		
		$translator = new Translator ();
		$course = new Course ();
		$category = new Category ();
		$training = new Training ();
		$t_data = $training->get ( array (
				'id' => $this->id,
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
		if ($t_data ['status_id'] == Training::FINISHED) {
			$result = $translator->Congratulations;
			$result_class = "alert-success";
		} else {
			$result = $translator->Sorry;
			$result_class = "alert-danger";
		}
		echo <<< EOF
<div class="container-fluid">
    <div class="col-lg-10 col-lg-offset-1" id="window">
	<h4>$categories</h4>
	<h2>{$c_data[name]}</h2>
	<h3 class="alert {$result_class}" role="alert">{$result}</h3>
	<h4>{$translator->Result}: {$t_data[result]}% ($t_data[answers] / {$t_data[questions]})</h4>
	<table class="table">
	    <tr>
		<th>#</th>
		<th>{$translator->Question}</th>
		<th>{$translator->Result}</th>
	    </tr>

EOF;
		$exam = new Exam ();
		$enum = $exam->enumerateResult ( $t_data ['id'] );
		$i = 1;
		foreach ( $enum as $key => $e_data ) {
			$answer = $e_data ['result'] == 't' ? $translator->Right : $translator->Wrong;
			$class = $e_data ['result'] == 't' ? "success" : "danger";
			echo <<<EOF
	    <tr class="alert alert-{$class}" role="alert">
		<td>$i</td>
		<td>{$e_data[question]}</td>
		<td >$answer</td>
	    </tr>
EOF;
			$i ++;
		}
		echo <<< EOF
	</table>
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
