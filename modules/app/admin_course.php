<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../question.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../sendmail.php';
include_once __DIR__ . '/../zip.php';
include LC_PATH . '/courses.php';
class AdminCoursePage extends AuthorizedPage {
	function download() {
		$id = $this->param [1];
		$course = new Course ();
		if ($course->download ( $id )) {
			exit ();
		} else {
			header ( 'Location: /admin_courses' );
		}
	}
	function uploadFiles($id) {
		$course = new Course ();
		$course->upload ( $id );
	}
	function save() {
		$id = array_shift ( $this->param );
		Security::checkEditor($id);
		$translator = new Translator ();
		$obj = new Course ();
		try {
			
			$data = array (
					'name' => '',
					'category_id' => 0,
					'format_id' => 0,
					'prerequisites' => '',
					'language' => 0,
					'describe' => '',
					'skills' => '',
					'duration' => 0,
					'equipment' => '',
					'min' => 0,
					'max' => 0,
					'author' => '',
					'questions' => 0,
					'active' => 'false',
					'exam' => 'false',
					'need_approve' => 'false',
					'exam_duration' => 0 
			);
			
			foreach ( $data as $key => $val ) {
				if (isset ( $_POST [$key] ))
					$data [$key] = $_POST [$key];
			}
			$data ['updated'] = date ( 'Y-m-d H:i:s', time () );
			$obj->update ( $id, $data );
			if (isset ( $_FILES ['file'] ))
				$this->uploadFiles ( $id );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Course_updated}</h1>
      </div>
   <p class="lead">{$translator->Course_updated_Desc}</p>
      <p >{$translator->Error_Backadmincourses}</p>
</div>
<script type="text/javascript">
//window.location.href='/admin_courses';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Course_error}</h1>
      </div>
   <p class="lead">{$translator->Course_error_Desc}</p> $e
      <p >{$translator->Error_Backadmincourses}</p>
</div>

EOF;
		}
	}
	function add() {
		$translator = new Translator ();
		$course = new Course ();
		try {
			
			$data = array (
					'name' => '',
					'category_id' => 0,
					'format_id' => 0,
					'prerequisites' => '',
					'language' => 0,
					'describe' => '',
					'skills' => '',
					'duration' => 0,
					'equipment' => '',
					'min' => 0,
					'max' => 0,
					'author' => '',
					'questions' => 0,
					'active' => 'false',
					'exam' => 'false',
					'need_approve' => 'false',
					'exam_duration' => 0 
			);
			
			foreach ( $data as $key => $val ) {
				if (isset ( $_POST [$key] ))
					$data [$key] = $_POST [$key];
			}
			$course->add ( $data );
			if (isset ( $_FILES ['file'] ))
				$this->uploadFiles ( $id );
			
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Course_updated}</h1>
      </div>
   <p class="lead">{$translator->Course_updated_Desc}</p>
      <p >{$translator->Error_Backadmincourses}</p>
</div>
<script type="text/javascript">
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Course_error}</h1>
      </div>
   <p class="lead">{$translator->Course_error_Desc}</p>
      <p >{$translator->Error_Backadmincourses}</p>
</div>

EOF;
		}
	}
	function edit($create = null) {
		$translator = new Translator ();
		$course = new Course ();
		$category = new Category ();
		
		if ($create) {
			$func = 'add';
			$category_id = array_shift ( $this->param );
		} else {
			$func = 'save';
			$data = $course->get ( array (
					'id' => array_shift ( $this->param ) 
			) );
			foreach ( $data as $key => $val ) {
				$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
			$category_id = $data ['category_id'];
			;
		}
		$enum = $category->enumerate ();
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['parent_id']] [] = $val;
			}
			foreach ( $items as $key => $val ) {
				asort ( $items [$key] );
			}
			asort ( $items );
			$categories = $this->makeCategoryList ( $items, 0, $category_id );
		}
		foreach ( $course->formats as $key => $val ) {
			$formats .= "<option value=\"$key\"" . ($data ['format_id'] == $key ? ' selected' : '') . ">" . htmlspecialchars ( $val, ENT_QUOTES ) . "</option>" . NL;
		}
		
		foreach ( $course->languages as $key => $val ) {
			$languages .= "<option value=\"$key\"" . ($data ['language'] == $key ? ' selected' : '') . ">" . htmlspecialchars ( $val, ENT_QUOTES ) . "</option>" . NL;
		}
		
		if ($data ['active'] == 't')
			$active = 'checked';
		if ($data ['exam'] == 't')
			$exam = 'checked';
		
		if ($data ['need_approve'] == 't')
			$need_approve = 'checked';
		
		$maxsize = ini_get ( 'post_max_size' );
		if (!isset($create) && file_exists ( __DIR__ . '/../../../files/courses/' . $id )){
			$download = "<a href=\"/admin_course/download/{$data[id]}\" target=\"_blank\">{$translator->Download_Archive}</a>
		<a href=\"/admin_manager/list/{$data[id]}\">{$translator->File_Manager}</a>";
		
		$question = new Question ();
		$q_cnt = $question->getCount ( array (
				'course_id' => $data ['id'] 
		) );
		
		if ($q_cnt) {
			$q_cnt = "<span class=\"badge\">$q_cnt</span>";
		} else {
			$q_cnt = '';
		}
		$questions="<div><a href=\"/admin_questions/{$data[id]}\">{$translator->Questions_Link} $q_cnt</a></div>";
		}
		echo <<< EOF
<div class="container">
<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" action="/admin_course/{$func}/{$data[id]}" method="post" enctype="multipart/form-data">
	<h2>{$translator->edit}</h2>
	    <div class="form-group">
		<input type="text" name="name" id="name" class="form-control input-lg" 
		placeholder="{$translator->Name}" tabindex="1" required value="{$data[name]}">
	    </div>
	    <div class="form-group">
		<label for="category_id">{$translator->Category}:</label>
		    <select class="form-control" id="category_id" name="category_id">
		    $categories
		</select>
	    </div>
	    <div class="form-group">
		<label for="format_id">{$translator->Format}:</label>
		    <select class="form-control" id="format_id" name="format_id">
		    $formats
		</select>
	    </div>
	    <div class="form-group">
		<label for="data">{$translator->Prerequisites}:</label> {$translator->Prerequisites_Desc}
		<textarea  name="prerequisites" id="prerequisites" class="form-control" rows="4">{$data[prerequisites]}</textarea>
	    </div>
<!-- TODO>
 Add needed courses select-box
	    <div class="form-group">Courses</div>
<!-->
	    <div class="form-group">
		<label for="language">{$translator->Language}:</label>
		    <select class="form-control" id="language" name="language">
		    $languages
		</select>
	    </div>
	    <div class="form-group">
		<label for="describe">{$translator->Describe}:</label> {$translator->Describe_Desc}
		<textarea  name="describe" id="describe" class="form-control" rows="5">{$data[describe]}</textarea>
	    </div>
	    <div class="form-group">
		<label for="skills">{$translator->Skills}:</label> {$translator->Skills_Desc}
		<textarea  name="skills" id="skills" class="form-control" rows="5">{$data[skills]}</textarea>
	    </div>
	    <div class="form-group">
		<input type="number" name="duration" id="duration" class="form-control" min="1"
		placeholder="{$translator->Duration}" tabindex="1" required value="{$data[duration]}">
		<span class="help-block">{$translator->Duration_Desc}</span>
	    </div>
	    <div class="form-group">
		<label for="equipment">{$translator->Equipment}:</label> {$translator->Equipment_Desc}
		<textarea  name="equipment" id="equipment" class="form-control" rows="5">{$data[equipment]}</textarea>
	    </div>
	    <div class="form-group">
		<input type="number" name="min" id="min" class="form-control" min="1"
		placeholder="{$translator->Min}" tabindex="1" value="{$data[min]}">
		<span class="help-block">{$translator->Min_Desc}</span>
	    </div>
	    <div class="form-group">
		<input type="number" name="max" id="max" class="form-control" min="1"
		placeholder="{$translator->Max}" tabindex="1" value="{$data[max]}">
		<span class="help-block">{$translator->Max_Desc}</span>
	    </div>
	    <div class="form-group">
		<input type="text" name="author" id="author" class="form-control" 
		placeholder="{$translator->Author}" tabindex="1" value="{$data[author]}">
	    </div>
	    <div class="form-group">
		<input type="number" name="questions" id="questions" class="form-control" min="0"
		placeholder="{$translator->Questions}" tabindex="1" value="{$data[questions]}">
		<span class="help-block">{$translator->Questions_Desc}</span>
	    </div>
	    <div class="checkbox">
		  <label><input type="checkbox" value="true" name="active" id="active" $active> {$translator->Active}</label>
	    </div>
	    <div class="checkbox">
		  <label><input type="checkbox" value="true" name="need_approve" id="need_approve" $need_approve> {$translator->Need_approve}</label>
	    </div>
	    <div class="checkbox">
		<label><input type="checkbox" value="true" name="exam" id="exam" $exam> {$translator->Exam}</label>
		 $questions
	    </div>
	    <div class="form-group">
		<input type="number" name="exam_duration" id="exam_duration" class="form-control" min="1"
		placeholder="{$translator->Exam_Duration}" tabindex="1" required value="{$data[exam_duration]}">
		<span class="help-block">{$translator->Exam_Duration_Desc}</span>
	    </div>

	    <div class="form-group">
		<span class="btn btn-default btn-file"><input type="file" name="file" value="file"> </span>
		$download
	    </div>

	    <div class="row">
		<div class="col-xs-12 col-md-6"><input type="submit" value="{$translator->Save}" class="btn btn-primary" tabindex="13"></div>
	    </div>
    </form>
</div>
</div> <!-- /container -->

EOF;
	}
	function init() {
		if (is_array ( $this->param ))
			if ($this->param [0] == 'download')
				$this->download ();
	}
	function displayBody() {
		parent::displayBody ();
		
		if (! is_array ( $this->param ))
			return;
		$func = array_shift ( $this->param );
		switch ($func) {
			case 'new' :
				$this->edit ( true );
				break;
			case 'add' :
				
				$this->add ();
				break;
			case 'edit' :
				$this->edit ();
				break;
			case 'save' :
				$this->save ();
				break;
		}
	}
	function makeCategoryList(&$items, $id, $category_id, $space = '') {
		foreach ( $items [$id] as $key => $val ) {
			$categories .= "<option value=\"$val[id]\"" . ($val ['id'] == $category_id ? ' selected' : '') . ">" . $space . htmlspecialchars ( $val ['name'], ENT_QUOTES ) . "</option>" . NL;
			if (isset ( $items [$val ['id']] ))
				$categories .= $this->makeCategoryList ( $items, $val ['id'], $category_id, $space . '&nbsp;&nbsp;&nbsp;' );
		}
		return $categories;
	}
	function defaultRole() {
		$this->role = User::EDITOR | User::EDITOR_SIMPLE;
	}
}
