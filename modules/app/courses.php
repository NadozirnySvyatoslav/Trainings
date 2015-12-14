<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/courses.php';
class CoursesPage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$category = new Category ();
		$course = new Course ();
		$cnt = $course->getCount ();
		
		$enum = $category->enumerate ( array (
				'active' => true 
		) );
		foreach ( $enum as $key => $val ) {
			if ($val ['id'] != 0)
				$this->items [$val ['parent_id']] [] = $val;
		}
		foreach ( $this->items as $key => $val ) {
			asort ( $this->items [$key] );
		}
		
		$enum = $course->enumerate ( array (
				'active' => true 
		) );
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
			if (sizeof ( $this->courses [$data ['id']] ) > 0)
				$count = " <span class=\"badge\">" . sizeof ( $this->courses [$data ['id']] ) . "</span>";
			else
				$count = '';
			
			echo "                <li class=\"list-group-item" . ($data ['active'] == 'f' ? ' not-active' : '') . "\" >
		  <a href=\"#\"  data-toggle=\"collapse\" data-target=\"#list{$data[id]}\">
                  <span class=\"item-name\" id=\"$data[id]\">{$data[name]} $count</span></a>
" . NL;
			
			if (isset ( $this->courses [$data ['id']] ) && $data ['id'] != 0) {
				echo "<ul class=\"list-group category-list collapse\" id=\"list{$data[id]}\">" . NL;
				$this->put_courses_list ( $data ['id'] );
				echo "</ul>" . NL;
			}
			
			if (isset ( $this->items [$data ['id']] ) && $data ['id'] != 0) {
				echo "<ul class=\"list-group category-list " . ($parent == 0 ? '' : 'collapse') . "\" id=\"list{$data[id]}\">" . NL;
				$this->put_list ( $data ['id'] );
				echo "</ul>" . NL;
			}
			echo "</li>" . NL;
		}
	}
	function put_courses_list($category_id = 0) {
		$translator = new Translator ();
		$course = new Course ();
		
		foreach ( $this->courses [$category_id] as $key => $data ) {
			switch ($data ['format_id']) {
				case Course::ONLINE :
					$img = "<span class=\"glyphicon glyphicon-headphones\"></span> ";
					break;
				case Course::WEBCAST :
					$img = "<span class=\"glyphicon glyphicon-facetime-video\"></span> ";
					break;
				case Course::OFFLINE :
					$img = "<span class=\"glyphicon glyphicon-blackboard\"></span> ";
					break;
			}
			echo "                <li class=\"list-group-item list-group-item-info" . ($data ['active'] == 'f' ? ' not-active' : '') . "\" >
		  <a href=\"/course/{$data[id]}\" >$img
                  <span class=\"item-name\" id=\"$data[id]\">{$data[name]} </span></a>
                   <small class=\"text-muted\">(" . $course->formats [$data ['format_id']] . ' / ' . $course->languages [$data ['language']] . ")</small>" . NL;
			echo "</li>" . NL;
		}
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
