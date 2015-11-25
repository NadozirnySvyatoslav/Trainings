<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../plan.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/plans.php';
/**
 *
 * @author Nadozirny_SV
 *        
 */
class TrainingsPage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		
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
		$search ['not_finish'] = 'true';
		$cnt = $plan->getCount ( $search );
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		$category = new Category ();
		$enum = $category->enumerate ( null );
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['parent_id']] [] = $val;
			}
			foreach ( $items as $key => $val ) {
				asort ( $items [$key] );
			}
			asort ( $items );
			$categories = '<option></option>' . $this->makeFilterCategoryList ( $items, 0, $category_id );
		}
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<h2  class="page-header">{$translator->Plans_header} <span class="badge">$cnt</span></h2>
    <div class="row">
	<div class="btn-group">
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
		$enum = $category->enumerate ( null );
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
		}
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
				$categories = $this->makeCategoryList ( $items, $c_data ['category_id'] );
				
				echo "                <tr>
                  <td>{$i}</td>
                  <td>" . ($data ['active'] == 'f' ? '<del>' : '') . "<p class=\"text-muted\">{$categories}</p>
			<a href=\"/course/{$c_data[id]}\">{$c_data[name]}</a></td>
                  <td>{$data[start]}</td>
                  <td>{$data[finish]}</td>
                  <td>{$participants}</td>
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
	function makeCategoryList(&$items, $category_id) {
		$category = $items [$category_id] ['name'];
		if ($items [$category_id] ['parent_id'] != 0)
			$category = $this->makeCategoryList ( &$items, $items [$category_id] ['parent_id'] ) . "<span class=\"glyphicon glyphicon-menu-right\"></span>" . $category;
		return $category;
	}
	function makeFilterCategoryList(&$items, $id, $category_id, $space = '') {
		foreach ( $items [$id] as $key => $val ) {
			$categories .= "<option value=\"$val[id]\"" . ($val ['id'] == $category_id ? ' selected' : '') . ">" . $space . htmlspecialchars ( $val ['name'], ENT_QUOTES ) . "</option>" . NL;
			if (isset ( $items [$val ['id']] ))
				$categories .= $this->makeFilterCategoryList ( $items, $val ['id'], $category_id, $space . '&nbsp;&nbsp;&nbsp;' );
		}
		return $categories;
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
