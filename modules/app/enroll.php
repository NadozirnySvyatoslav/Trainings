<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../request.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/courses.php';
class EnrollPage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		
		$translator = new Translator ();
		$request = new Request ();
		try {
			
			$id = array_shift ( $this->param );
			$course = new Course ();
			$c_data = $course->get ( array (
					'id' => $id 
			) );
			$data ['course_id'] = $id;
			$data ['user_id'] = $_SESSION ['user_id'];
			$data ['active'] = true;
			$data ['planned_date'] = $_GET ['planned_date'] != '' ? $_GET ['planned_date'] : date ( 'Y-m-d H:i:s', time () );
			
			$r_id = $request->add ( $data );
			if ($c_data ['need_approve'] == 'f') {
				$request->approve ( $r_id );
				echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_updated}</h1>
      </div>
   <p class="lead">{$translator->Request_updated_Desc}</p>
      <p >{$translator->Error_Backcourses}</p>
</div>
<script type="text/javascript">
window.location.href='/mycourses';
</script>

EOF;
			} else {
				echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_updated}</h1>
      </div>
   <p class="lead">{$translator->Request_need_approve_Desc}</p>
      <p >{$translator->Error_Backcourses}</p>
</div>

EOF;
			}
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_error}</h1>
      </div>
   <p class="lead">{$translator->Request_error_Desc}</p>
      <p >{$translator->Error_Backcourses}</p>
</div>

EOF;
		}
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
