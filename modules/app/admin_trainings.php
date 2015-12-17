<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../plan.php';
include_once __DIR__ . '/../course.php';
include LC_PATH . '/common.php';
include LC_PATH . '/plans.php';
class AdminPlansPage extends AuthorizedPage {
	function delete() {
		$translator = new Translator ();
		$plan = new Plan ();
		try {
			
			$id = array_shift ( $this->param );
			$plan->delete ( $id );
			$training = new Training ();
			$training->deleteMore ( array (
					'plan_id' => $id 
			) );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_deleted}</h1>
      </div>
   <p class="lead">{$translator->Plan_deleted_Desc}</p>
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
	function approve() {
		$translator = new Translator ();
		$plan = new Plan ();
		try {
			
			$id = array_shift ( $this->param );
			$plan->invert ( $id );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Plan_approved}</h1>
      </div>
   <p class="lead">{$translator->Plan_approved_Desc}</p>
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
	function displayBody() {
		parent::displayBody ();
		if (! is_array ( $this->param ))
			return;
		$func = array_shift ( $this->param );
		switch ($func) {
			case 'approve' :
				$this->approve ();
				break;
			case 'delete' :
				$this->delete ();
				break;
			default :
				$this->view ();
		}
	}
	function view() {
		$translator = new Translator ();
		$plan = new Plan ();
		$page = $_GET ['page'];
		$offset = intval ( $page ) * ITEMS_IN_PAGE;
		if (isset ( $_GET ['search'] )) {
			unset ( $_GET ['search'] );
			unset ( $_GET ['page'] );
			$search = $_GET;
		} else {
			$search = array (
					'active' => 'true' 
			);
		}
		$cnt = $plan->getCount ( $search );
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		$categories=Course::getCategoriesForSelect($category_id);
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<h2  class="page-header">{$translator->Plans_header} <span class="badge">$cnt</span></h2>
    <div class="row">
	<div class="btn-group">
	<a href="/admin_training/new" class="btn btn-sm btn-primary">
	    <span class="glyphicon glyphicon-plus"></span>
	    <span class="glyphicon glyphicon-th-list"></span> 
	     {$translator->Add}
	</a>
	<button class="btn btn-sm btn-default"  data-toggle="collapse" data-target="#filter">
	    <span class="glyphicon glyphicon-filter"></span> {$translator->Filter}
	</button>
	</div>
    </div>
    <div class="row">
        <div class="col-md-5 collapse" id="filter">
	    <form role="form" class="form-horizontal" action="?" method="get">
		<div class="form-group">
		    <div class="form-group">
		    <label for="coursename" class="control-label col-sm-3">{$translator->Course}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="coursename" name="coursename" value="{$values[coursename]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="category" class="control-label col-sm-3">{$translator->Category}:</label>
		    <div class="col-sm-8">
		    <select name="category_id" class="form-control" >
			$categories
		    </select>
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="start" class="control-label col-sm-3">{$translator->Start}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control datetimepicker" id="start" name="start" value="{$values[start]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="finish" class="control-label col-sm-3">{$translator->Finish}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control datetimepicker" id="finish" name="finish" value="{$values[finish]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="active" class="control-label col-sm-offset-3">{$translator->Active}: 
		        <input type="checkbox" id="active" name="active" value="true">
		        </label>
		    </div>
		    <div class="form-group">
		    <div class="col-sm-offset-3 col-md-10">
		    <input class="sr-only" name="search" value="1">
		    <button type="submit" class="btn btn-sm btn-default">{$translator->Apply}</button>
		    </div>
		    </div>
		</div>
	    </form>
	</div>
    </div>
    
{$pagination}
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{$translator->Course}</th>
                  <th>{$translator->Start}</th>
                  <th>{$translator->Finish}</th>
                  <th>{$translator->Users}</th>
                </tr>
              </thead>
              <tbody>

EOF;
		
		$course = new Course ();
		$training = new Training ();
		$enum = $plan->enumerate ( $search, $offset, ITEMS_IN_PAGE );
		if (is_object ( $enum )) {
			$i = 1 + $offset;
			foreach ( $enum as $key => $data ) {
				$c_data = $course->get ( array (
						'id' => $data ['course_id'] 
				) );
				$participants = $training->getCount ( array (
						'plan_id' => $data ['id'] 
				) );
				
				echo "                <tr>
                  <td>{$i}</td>
                  <td>" . ($data ['active'] == 'f' ? '<del>' : '') . "<p class=\"text-muted\">{$data[category_name]}</p>
			<a href=\"/course/{$c_data[id]}\" target=\"_blank\">{$c_data[name]}</a></td>
                  <td>{$data[start]}</td>
                  <td>{$data[finish]}</td>
                  <td>{$participants}</td>
                  <td><a href=\"/admin_trainings/approve/{$data[id]}\" title=\"{$translator->activate}\"><span class=\"glyphicon glyphicon-ok\"></span></a></td>
                  <td><a href=\"/admin_training/edit/{$data[id]}\" title=\"{$translator->edit}\"><span class=\"glyphicon glyphicon-pencil\"></span></a></td>
                  <td><a href=\"/admin_trainings/delete/{$data[id]}\" title=\"{$translator->del}\" onClick=\"return confirm('{$translator->AreYouSure}');\"><span class=\"glyphicon glyphicon-trash\"></span></a></td>
                </tr>" . NL;
				$i ++;
			}
		}
		
		echo <<< EOF
	    </table>
	</div>

EOF;
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
            $( ".datetimepicker" ).each(function(){
             $(this).datetimepicker({
                locale: '{$locale}',
                format: 'YYYY-MM-DD HH:mm'
                });
            });
    });
</script>

EOF;
	}

	function defaultRole() {
		$this->role = User::ADMIN;
	}
}
