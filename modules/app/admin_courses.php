<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/courses.php';
class AdminCoursesPage extends AuthorizedPage {
	function init() {
		$obj = new Course ();
		$func = array_shift ( $this->param );
		$id = array_shift ( $this->param );
		if ($func != '') {
			$_SERVER ['REQUEST_METHOD'] = 'POST';
			switch ($func) {
				case 'delete' :
					try {
						$obj->delete ( $id );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_courses' );
					exit ();
					break;
				case 'activate' :
					try {
						$obj->invert ( $id );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_courses' );
					exit ();
					break;
			}
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$category = new Category ();
		$course = new Course ();
		$cnt = $course->getCount ();
		
		$enum = $category->enumerate ();
		foreach ( $enum as $key => $val ) {
			if ($val ['id'] != 0)
				$this->items [$val ['parent_id']] [] = $val;
		}
		foreach ( $this->items as $key => $val ) {
			asort ( $this->items [$key] );
		}
		
		$enum = $course->enumerate ();
		if (isset ( $enum ))
			foreach ( $enum as $key => $val ) {
				$this->courses [$val ['category_id']] [] = $val;
			}
		foreach ( $this->items as $key => $val ) {
			asort ( $this->items [$key] );
		}
		
		echo <<< EOF
<h2  class="page-header">{$translator->Courses_header} <span class="badge">$cnt</span></h2>
<div class="col-lg-8">
    <ul class="list-group category-list" index="0">

EOF;
		
		$this->put_list ();
		
		echo <<< EOF
    </ul>
</div>

EOF;
	}
	function put_list($parent = 0) {
		$translator = new Translator ();
		foreach ( $this->items [$parent] as $key => $data ) {
			echo "                <li class=\"list-group-item" . ($data ['active'] == 'f' ? ' not-active' : '') . "\" >
                  <span class=\"item-name\" id=\"$data[id]\">{$data[name]}</span>
		  <a href=\"/admin_course/new/$data[id]\" title=\"{$translator->add}\">
		    <span class=\"glyphicon glyphicon-plus\"></span></a>" . NL;
			
			if (isset ( $this->courses [$data ['id']] ) && $data ['id'] != 0) {
				echo "<ul class=\"list-group category-list\" index=\"{$data[id]}\">" . NL;
				$this->put_courses_list ( $data ['id'] );
				echo "</ul>" . NL;
			}
			
			if (isset ( $this->items [$data ['id']] ) && $data ['id'] != 0) {
				echo "<ul class=\"list-group category-list\" index=\"{$data[id]}\">" . NL;
				$this->put_list ( $data ['id'] );
				echo "</ul>" . NL;
			}
			echo "</li>" . NL;
		}
	}
	function put_courses_list($category_id = 0) {
		$translator = new Translator ();
		foreach ( $this->courses [$category_id] as $key => $data ) {
			echo "                <li class=\"list-group-item list-group-item-info" . ($data ['active'] == 'f' ? ' not-active' : '') . "\" >
                  <span class=\"item-name\" id=\"$data[id]\"><small>{$data[name]}</small></span>
		  <a href=\"/admin_courses/activate/{$data[id]}\"  title=\"{$translator->activate}\">
		    <span class=\"glyphicon glyphicon-ok\"></span></a>
                  <a href=\"/admin_course/edit/{$data[id]}\" title=\"{$translator->edit}\">
		    <span class=\"glyphicon glyphicon-pencil\"></span></a>
                  <a href=\"/admin_courses/delete/{$data[id]}\" title=\"{$translator->del}\" 
		    onClick=\"return confirm('{$translator->AreYouSure}');\">
		    <span class=\"glyphicon glyphicon-trash\"></span></a>" . NL;
			echo "</li>" . NL;
		}
	}
	function defaultRole() {
		$this->role = User::EDITOR;
	}
}
