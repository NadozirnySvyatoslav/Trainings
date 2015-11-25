<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/courses.php';
class CoursePage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		
		$translator = new Translator ();
		$course = new Course ();
		$category = new Category ();
		$training = new Training ();
		$id = array_shift ( $this->param );
		try {
			$t_exists = $training->checkFinish ( $_SESSION ['user_id'], $id );
		} catch ( Exception $e ) {
		}
		$data = $course->get ( array (
				'id' => $id 
		) );
		foreach ( $data as $key => $val ) {
			$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		$category_id = $data ['category_id'];
		;
		
		$enum = $category->enumerate ( null );
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
			$categories = $this->makeCategoryList ( $items, $category_id );
		}
		foreach ( $course->formats as $key => $val ) {
			if ($key == $data ['format_id'])
				$format = htmlspecialchars ( $val, ENT_QUOTES );
		}
		
		foreach ( $course->languages as $key => $val ) {
			if ($key == $data ['language'])
				$language = htmlspecialchars ( $val, ENT_QUOTES );
		}
		
		if ($data ['active'] == 't')
			$active = 'checked';
		if ($data ['exam'] == 't')
			$exam = 'checked';
		
		if ($data [duration] > 8)
			$duration = (floor ( $data ['duration'] / 8 )) . " {$translator->days}" . (($data ['duration'] % 8) > 0 ? " + " . ($data ['duration'] % 8) . " {$translator->hours}" : '');
		else
			$duration = $data ['duration'] . " {$translator->hours}";
		if ($t_exists)
			$disabled = 'disabled';
		
		echo <<< EOF
<div class="container-fluid">
<div class="col-md-10 col-lg-8 col-lg-offset-2 ">
	<h4>$categories</h4>
	<h2>{$data[name]}</h2>
	<div class="row">
	    <div class="col-sm-6"><small>{$translator->Duration}: {$duration}</small></div>
	    <div class="col-sm-6 text-right"><small>{$translator->Format}: {$format}</small></div>
	</div>
	<hr/>
	<label for="describe">{$translator->Describe}:</label> 
	<p class="describe">{$data[describe]}</p>
	<label for="data">{$translator->Prerequisites}:</label> 
	<p class="describe">{$data[prerequisites]}</p>
	<label for="skills">{$translator->Skills}:</label>
	<p class="describe">{$data[skills]}</p>
	<label for="equipment">{$translator->Equipment}:</label>
	<p class="describe">{$data[equipment]}</p>

<!-- TODO>
 Add needed courses select-box
	    <div class="form-group">Courses</div>
<!-->
	<div>
	    <label for="language">{$translator->Language}:</label> $language
	</div>
	<div>
	    <label for="language">{$translator->Author}:</label> {$data[author]}
	</div>
	<hr/>
	<div>
		<a href="/enroll/$data[id]" class="btn btn-primary btn-lg {$disabled}" >{$translator->Register}</a>
	</div>
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
