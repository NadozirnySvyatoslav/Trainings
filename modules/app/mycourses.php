<?php
require __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../course.php';
include LC_PATH . '/mycourses.php';
include LC_PATH . '/common.php';
class MyCoursesPage extends AuthorizedPage {
	function init() {
		$func = array_shift ( $this->param );
		$id = array_shift ( $this->param );
		switch ($func) {
			case 'dismiss' :
				$this->dismiss ( $id );
				break;
			case 'delete' :
				$this->delete ( $id );
				break;
			case 'finish' :
				$this->end_course ( $id );
				break;
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$training = new Training ();
		echo <<< EOF
<h2  class="page-header">{$translator->MyCourses}</h2>

EOF;
		
		$category = new Category ();
		$enum = $category->enumerate ( null );
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
		}
		$enum = $training->enumerate ( array (
				'user_id' => $_SESSION ['user_id'],
				'active' => 'true' 
		) );
		if ($enum) {
			foreach ( $enum as $key => $data ) {
				$categories = $this->makeCategoryList ( $items, $data ['category_id'] );
				$now = new DateTime ( 'now' );
				$finish = new DateTime ( $data ['finish'] );
				// check for timeout and set new status
				switch ($data ['status_id']) {
					case Training::CREATED :
					case Training::STARTED :
					case Training::EXAM :
					case Training::EXAM_STARTED :
						
						if ($now > $finish) {
							$training->update ( $data ['id'], array (
									'status_id' => Training::TIMEOUT 
							) );
							$data ['status_id'] = Training::TIMEOUT;
						}
					default :
				}
				// prepeare data for display
				switch ($data ['status_id']) {
					case Training::TIMEOUT :
						$data ['result_desc'] = "<span class=\"label label-danger\">{$translator->Overtime}</span>";
						$actions = "<a href=\"/course/{$data[course_id]}\" class=\"btn btn-default btn-md\">
	        <span class=\"glyphicon glyphicon glyphicon-repeat\"></span> {$translator->Repeat}</a>
	        <a href=\"/mycourses/delete/{$data[id]}\" class=\"btn btn-default btn-md\"
	        onclick='return confirm(\"{$translator->AreYouSure}\");'>
	        <span class=\"glyphicon glyphicon glyphicon-trash\"></span> {$translator->Delete}</a>";
						break;
					case Training::CREATED :
						$data ['result_desc'] = "<span class=\"label label-default\">{$translator->Not_started}</span>";
						$actions = "<a href=\"/learn/{$data[course_hash]}\" class=\"btn btn-primary btn-md\">
    	    <span class=\"glyphicon glyphicon-play\"></span> {$translator->Learn}</a>
    	    <a href=\"/mycourses/dismiss/{$data[id]}\" class=\"btn btn-danger btn-md\">
    	    <span class=\"glyphicon glyphicon-trash\"></span> {$translator->Dismiss}</a>";
						break;
					case Training::DELETED :
						$data ['result_desc'] = "<span class=\"label label-default\">{$translator->Deleted}</span>";
						$actions = "<a href=\"/course/{$data[course_id]}\" class=\"btn btn-default btn-md\">
            <span class=\"glyphicon glyphicon glyphicon-repeat\"></span> {$translator->Repeat}</a>
            <a href=\"/mycourses/delete/{$data[id]}\" class=\"btn btn-default btn-md\"
            onclick='return confirm(\"{$translator->AreYouSure}\");'>
	    <span class=\"glyphicon glyphicon glyphicon-trash\"></span> {$translator->Delete}</a>";
						break;
					case Training::STARTED :
						$data ['result_desc'] = "<span class=\"label label-warning\">{$translator->Not_tested}</span>";
						$actions = "<a href=\"/learn/{$data[course_hash]}\" class=\"btn btn-primary btn-md\">
	    <span class=\"glyphicon glyphicon-play\"></span> {$translator->Learn}</a>
	    ";
						break;
					case Training::EXAM :
					case Training::EXAM_STARTED :
						$data ['result_desc'] = "<span class=\"label label-danger\">{$translator->NotCertified}</span>" . "<div>{$translator->Result}: " . intval ( $data ['result'] ) . "%({$data[answers]}/{$data[questions]})</div>";
						$actions = "<a href=\"/exam/{$data[exam_hash]}/\" class=\"btn btn-warning btn-md\">
	    <span class=\"glyphicon glyphicon-check\"></span> {$translator->Test}</a>";
						break;
					case Training::FAILED :
						$data ['result_desc'] = "<span class=\"label label-danger\">{$translator->NotCertified}</span>" . "<div>{$translator->Result}: " . intval ( $data ['result'] ) . "%({$data[answers]}/{$data[questions]})</div>";
						$actions = "<a href=\"/course/{$data[course_id]}\" class=\"btn btn-default btn-md\">
            <span class=\"glyphicon glyphicon glyphicon-repeat\"></span> {$translator->Repeat}</a>
            <a href=\"/mycourses/delete/{$data[id]}\" class=\"btn btn-default btn-md\"
            onclick='return confirm(\"{$translator->AreYouSure}\");'>
	    <span class=\"glyphicon glyphicon glyphicon-trash\"></span> {$translator->Delete}</a>";
						break;
					
					case Training::FINISHED :
						if ($data ['exam'] == 't') {
							$data ['result_desc'] = "<span class=\"label label-success\">{$translator->Certified}</span>" . "<div>{$translator->Result}: " . intval ( $data ['result'] ) . "%({$data[answers]}/{$data[questions]})</div>";
							$actions = "<a href=\"/certificate/{$data[course_hash]}\" class=\"btn btn-success btn-md\">
		<span class=\"glyphicon glyphicon-certificate\"></span> {$translator->Certificate}</a>";
						} else {
							$data ['result_desc'] = "<span class=\"label label-success\">{$translator->Certified}</span>";
							$actions = "<a href=\"/certificate/{$data[course_hash]}\" class=\"btn btn-success btn-md\">
		<span class=\"glyphicon glyphicon-thumbs-star\"></span> {$translator->Certificate}</a>";
						}
						break;
					default :
				}
				
				if ($data ['finished'])
					$finish = $data ['finished'];
				else
					$finish = $data ['finish'];
				echo <<<EOF
<div class="panel panel-default col-md-6 col-lg-4 mycourse">
    <div class="panel-heading overflow-hidden">
	<p class="text-muted">$categories</p>
	<h4><a href="/course/{$data[course_id]}">{$data[course_name]}</a></h4>
    </div>
    <div class="panel-body">
	<div class="row">
	    <div class="col-md-8">
	<p>{$translator->Start}: {$data[start]}</p>
	<p>{$translator->Finish}: {$finish}</p>
	    </div>
	    <div class="col-md-4">
		{$data[result_desc]}
	    </div>
	</div>
    	<div class="row">
    	$actions
	</div>
    </div>
</div>

EOF;
			}
		} else {
			
			echo <<<EOF
<div class="panel panel-default col-lg-8">
    <div class="panel-body">
	{$translator->No_courses}
    </div>
</div>

EOF;
		}
	}
	function end_course($id) {
		$training = new Training ();
		$course = new Course ();
		try {
			$t_data = $training->get ( array (
					'id' => $id,
					'user_id' => $_SESSION ['user_id'] 
			) );
			$c_data = $course->get ( array (
					'id' => $t_data ['course_id'] 
			) );
			if ($c_data ['exam'] == 't') {
				$status = Training::EXAM;
			} else {
				$status = Training::FINISHED;
			}
			$training->update ( $id, array (
					'status_id' => $status,
					'finished' => date ( 'Y-m-d H:i', time () ) 
			) );
			
			header ( 'Location: /mycourses' );
			exit ();
		} catch ( Exception $e ) {
			echo $e;
		}
	}
	function dismiss($id) {
		$training = new Training ();
		try {
			$t_data = $training->get ( array (
					'id' => $id,
					'user_id' => $_SESSION ['user_id'] 
			) );
			$training->update ( $id, array (
					'status_id' => Training::DELETED 
			) );
			header ( 'Location: /mycourses' );
			exit ();
		} catch ( Exception $e ) {
			echo $e;
			exit ();
		}
	}
	function delete($id) {
		$training = new Training ();
		try {
			$t_data = $training->get ( array (
					'id' => $id,
					'user_id' => $_SESSION ['user_id'] 
			) );
			$training->delete ( $id );
			header ( 'Location: /mycourses' );
			exit ();
		} catch ( Exception $e ) {
			echo $e;
			exit ();
		}
	}
	function makeCategoryList(&$items, $category_id) {
		$category = $items [$category_id] ['name'];
		if ($items [$category_id] ['parent_id'] != 0)
			$category = $this->makeCategoryList ( &$items, $items [$category_id] ['parent_id'] ) . "<span class=\"glyphicon glyphicon-menu-right\"></span>" . $category;
		return $category;
	}
}
