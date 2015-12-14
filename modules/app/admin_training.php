<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../plan.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../user.php';
include_once __DIR__ . '/../training.php';
include_once __DIR__ . '/../trainer.php';

include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/plans.php';
class AdminPlanPage extends AuthorizedPage {
	function save() {
		$translator = new Translator ();
		$obj = new Plan ();
		try {
			
			$data = array (
					'course_id' => '',
					'start' => '',
					'finish' => '',
					'contact' => '',
					'place' => '',
					'trainer_id' => '',
					'active' => 'false' 
			);
			foreach ( $data as $key => $val ) {
				$data [$key] = $_POST [$key];
			}
			$id = array_shift ( $this->param );
			$obj->update ( $id, $data );
			$training = new Training ();
			$training->updateMore ( array (
					'plan_id' => $id 
			), array (
					'course_id' => $data ['course_id'],
					'start' => $data ['start'],
					'finish' => $data ['finish'],
					'active' => $data ['active'] 
			) );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_updated}</h1>
      </div>
   <p class="lead">{$translator->Plan_updated_Desc}</p>
      <p >{$translator->Error_Backadmintrainings}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_trainings';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_error}</h1>
      </div>
   <p class="lead">{$translator->Plan_error_Desc}</p>
      <p >{$translator->Error_Backadmintrainings}</p>
</div>

EOF;
		}
	}
	function add() {
		$translator = new Translator ();
		$plan = new Plan ();
		try {
			
			$data = array (
					'course_id' => '',
					'start' => '',
					'finish' => '',
					'contact' => '',
					'place' => '',
					'trainer_id' => '',
					'active' => 'false' 
			);
			foreach ( $data as $key => $val ) {
				if (isset ( $_POST [$key] ))
					$data [$key] = $_POST [$key];
			}
			$id = array_shift ( $this->param );
			$plan->add ( $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_updated}</h1>
      </div>
   <p class="lead">{$translator->Plan_updated_Desc}</p>
      <p >{$translator->Error_Backadmintrainings}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_trainings';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_error}</h1>
      </div>
   <p class="lead">{$translator->Plan_error_Desc}</p>
      <p >{$translator->Error_Backadmintrainings}</p>
</div>

EOF;
		}
	}
	function edit($create = null) {
		$translator = new Translator ();
		$category = new Category ();
		$course = new Course ();
		$trainer = new Trainer ();
		
		if ($create)
			$func = 'add';
		else {
			$func = 'save';
			$plan = new Plan ();
			$this->id = array_shift ( $this->param );
			$data = $plan->get ( array (
					'id' => $this->id 
			) );
			foreach ( $data as $key => $val ) {
				$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		try {
			$c_data = $course->get ( array (
					'id' => $data ['course_id'] 
			) );
			$category_id = $c_data ['category_id'];
			$enum = $course->enumerate ( array (
					'category_id' => intval ( $category_id ) 
			) );
			foreach ( $enum as $val ) {
				$courses .= "<option value=\"{$val[id]}\"" . ($val ['id'] == $data ['course_id'] ? ' selected' : '') . ">" . htmlspecialchars ( $val ['name'], ENT_QUOTES ) . "</option>" . NL;
			}
		} catch ( Exception $e ) {
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
		
		$enum = $trainer->enumerate ();
		foreach ( $enum as $val ) {
			$trainers .= "<option value=\"{$val[id]}\"" . ($val ['id'] == $data ['trainer_id'] ? ' selected' : '') . ">" . htmlspecialchars ( $val ['name'], ENT_QUOTES ) . "</option>" . NL;
		}
		
		if ($data ['active'] == 't')
			$active = 'checked';
		
		echo <<< EOF
<div class="container">
<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" action="/admin_training/{$func}/{$data[id]}" method="post">
	<h2>{$translator->Plan_edit}</h2>
	    <div class="form-group">
                <label for="category_id">{$translator->Category}:</label>
        	<select class="form-control" id="category_id" name="category_id">
                $categories
                </select>
            </div>
	    <div class="form-group">
                <label for="course_id">{$translator->Course}:</label>
        	<select class="form-control" id="course_id" name="course_id">
                $courses
                </select>
            </div>

	    <div class="form-group">
		<label for="start">{$translator->Start}:</label> <span class="text-muted">{$translator->Start_Desc}</span>
		<input name="start" id="start" class="form-control datetimepicker" value="{$data[start]}">
	    </div>
	    <div class="form-group">
		<label for="finish">{$translator->Finish}:</label> <span class="text-muted">{$translator->Finish_Desc}</span>
		<input name="finish" id="finish" class="form-control datetimepicker" value="{$data[finish]}">
	    </div>
	    <div class="form-group">
		<input type="text" name="contact" id="contact" class="form-control input-lg" 
		placeholder="{$translator->Contact}" tabindex="5" required value="{$data[contact]}">
	    </div>
	    <div class="form-group">
		<input type="text" name="place" id="place" class="form-control input-lg" 
		placeholder="{$translator->Place}" tabindex="6" required value="{$data[place]}">
	    </div>
	    <div class="form-group">
                <label for="trainer_id">{$translator->Trainer}:</label>
        	<select class="form-control" id="trainer_id" name="trainer_id">
                $trainers
                </select>
            </div>
	    <div class="checkbox">
		<label><input type="checkbox" value="true" name="active" id="active" $active> {$translator->Active}</label>
	    </div>

EOF;
		if (! $create) {
			// let add people to plan, because plan_ID is known
			$user = new User ();
			$training = new Training ();
			$enum = $training->enumerate ( array (
					'plan_id' => $data ['id'] 
			) );
			if (is_object ( $enum ))
				foreach ( $enum as $val ) {
					$u_data = $user->get ( array (
							'id' => $val ['user_id'] 
					) );
					
					$json = array (
							'id' => $data ['id'],
							'name' => $data ['name'],
							'email' => $data ['email'],
							'company' => $data ['company'],
							'city' => $data ['city'] 
					);
					
					$users .= "<tr id=\"row{$u_data[id]}\"><td>{$u_data[name]}</td><td>" . "{$u_data[email]}</td><td>{$u_data[company]} / {$u_data[city]}</td><td>" . "<a href=\"#\" onclick=\"return removeuser({$u_data[id]});\">" . '<span class="glyphicon glyphicon-trash"></span></a></td></tr>' . NL;
				}
			
			echo <<< EOF
<div class="form-group totop-relative">
<small>
<table id="users"  class="table table-striped">
<tbody>
$users
</tbody>
</table>
</small>
<div class="list-group totop" id="usermenu" >
</div>
<input type="text" name="searchuser" id="searchuser" class="form-control input-md" placeholder="{$translator->Search_user}">
</div>

EOF;
		}
		
		echo <<< EOF
	    <div class="row">
		<div class="col-xs-12 col-md-6"><input type="submit" value="{$translator->Save}" class="btn btn-primary" tabindex="13"></div>
	    </div>
    </form>
</div>
</div> <!-- /container -->

EOF;
	}
	function init() {
		$this->func = array_shift ( $this->param );
		
		switch ($this->func) {
			case 'courses' :
				$category_id = array_shift ( $this->param );
				$course = new Course ();
				$data = $course->enumerate ( array (
						'category_id' => $category_id 
				), null, null, 'name' );
				foreach ( $data as $key => $val ) {
					$items [] = array (
							$val ['id'],
							$val ['name'] 
					);
				}
				echo json_encode ( $items );
				exit ();
				break;
			case 'finduser' :
				$id = array_shift ( $this->param );
				$search = $_GET ['search'];
				$user = new User ();
				try {
					foreach ( $user->find ( $search, 5 ) as $data ) {
						$json [] = array (
								'id' => $data ['id'],
								'name' => $data ['name'],
								'email' => $data ['email'],
								'company' => $data ['company'],
								'city' => $data ['city'] 
						);
					}
					echo json_encode ( $json );
				} catch ( Exception $e ) {
				}
				exit ();
				break;
			case 'adduser' :
				$plan_id = array_shift ( $this->param );
				$user_id = array_shift ( $this->param );
				$user = new User ();
				try {
					$data = $user->get ( array (
							'id' => $user_id 
					) );
					$plan = new Plan ();
					$plan->adduser ( $plan_id, $user_id );
					$json = array (
							'id' => $data ['id'],
							'name' => $data ['name'],
							'email' => $data ['email'],
							'company' => $data ['company'],
							'city' => $data ['city'] 
					);
					echo json_encode ( $json );
				} catch ( Exception $e ) {
					echo $e;
				}
				exit ();
				break;
			case 'removeuser' :
				$plan_id = array_shift ( $this->param );
				$user_id = array_shift ( $this->param );
				$training = new Training ();
				$t_data = $training->get ( array (
						'plan_id' => $plan_id,
						'user_id' => $user_id 
				) );
				$training->delete ( $t_data ['id'] );
				try {
					echo json_encode ( '' );
				} catch ( Exception $e ) {
					echo $e;
				}
				exit ();
				break;
		}
	}
	function displayBody() {
		parent::displayBody ();
		
		if (! is_array ( $this->param ))
			return;
		switch ($this->func) {
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
	function displayHeader() {
		parent::displayHeader ();
		echo <<< EOF
<link rel="stylesheet" href="/css/bootstrap-datetimepicker.css">

EOF;
	}
	function displayFooter() {
		parent::displayFooter ();
		$locale = LC;
		echo <<< EOF
<script src="/js/moment-with-locales.js"></script>
<script src="/js/bootstrap-datetimepicker.js"></script>
<script>
$(function() {
    $.ajaxSetup({
	async: false
    });
    $( ".datetimepicker" ).each(function(){
	$(this).datetimepicker({
	    locale: '{$locale}',
	    format: 'YYYY-MM-DD HH:mm'
	    });
	});
    $("#category_id").change(function(){
	$.getJSON("/admin_training/courses/"+$("#category_id :selected").val(),
		 function(data){
		    $("#course_id").html('');
		    $.each(data,function(i,field){
			$('#course_id').append($('<option>', {
			    value: field[0],
			    text: field[1]
			    }));
		    });
	    });
    });
    $("#searchuser").keyup(function(){
	$('#usermenu').html('');
	if ($("#searchuser").val().length > 0)
	$.getJSON("/admin_training/finduser/{$this->id}?search="+$("#searchuser").val(),
	function(res){
	    $.each(res,function(i,data){
	    $('#usermenu').append('<a class="list-group-item adduser list-group-item-info" '+
				    'href="#" onclick="return adduser('+data['id']+');"><h4>'+
				    data['name']+' <small>'+data['email']+'</small></h4><p>'+data['city']+' / '+data['company']+
		    '</p></a>'+"\\n");
		    });
	});
    });
  });
function removeuser(id){
    $.getJSON("/admin_training/removeuser/{$this->id}/"+id,function(data){
	$('table#users tr#row'+id).remove();
    });
    return false;
}
function adduser(id){
    $.getJSON("/admin_training/adduser/{$this->id}/"+id,function(data){
	$('#users').find('tbody').append('<tr id="row'+data['id']+'"><td>'+data['name']+'</td><td>'+
		data['email']+'</td><td>'+data['company']+' / '+data['city']+'</td><td>'+
		'<a href="#" onclick="return removeuser('+data['id']+');">'+
		'<span class="glyphicon glyphicon-trash"></span></a></td></tr>');
	$('#usermenu').html('');
    });
    
    return false;
}
  
</script>

EOF;
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
		$this->role = User::ADMIN;
	}
}
