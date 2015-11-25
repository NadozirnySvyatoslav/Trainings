<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../request.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/requests.php';
class AdminRequestsPage extends AuthorizedPage {
	function delete() {
		$translator = new Translator ();
		$request = new Request ();
		try {
			
			$id = array_shift ( $this->param );
			$request->delete ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_deleted}</h1>
      </div>
   <p class="lead">{$translator->Request_deleted_Desc}</p>
      <p >{$translator->Error_Backrequests}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_requests';
</script>

EOF;
		} catch ( Exception $e ) {
			
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_error}</h1>
      </div>
   <p class="lead">{$translator->Request_error_Desc}</p>
      <p >{$translator->Error_Backrequests}</p>
</div>

EOF;
		}
	}
	function approve() {
		$translator = new Translator ();
		$request = new Request ();
		try {
			
			$id = array_shift ( $this->param );
			$request->approve ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_approved}</h1>
      </div>
   <p class="lead">{$translator->Request_approved_Desc}</p>
      <p >{$translator->Error_Backrequests}</p>
</div>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Request_error}</h1>
      </div>
   <p class="lead">{$translator->Request_error_Desc}</p>
      <p >{$translator->Error_Backrequests}</p>
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
		$request = new Request ();
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
		$cnt = $request->getCount ( $search );
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		$category = new Category ();
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
			$categories = '<option></option>' . $this->makeFilterCategoryList ( $items, 0, $category_id );
		}
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<h2  class="page-header">{$translator->Requests_header} <span class="badge">$cnt</span></h2>
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
		    <label for="username" class="control-label col-sm-3">{$translator->User}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="username" name="username" value="{$values[username]}">
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
                  <th>{$translator->User}</th>
                  <th>{$translator->Date}</th>
                  <th>{$translator->Planned}</th>
                </tr>
              </thead>
              <tbody>

EOF;
		$enum = $category->enumerate ();
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
		}
		$enum = $request->enumerate ( $search, $offset, ITEMS_IN_PAGE );
		if (is_object ( $enum )) {
			$i = 1 + $offset;
			foreach ( $enum as $key => $data ) {
				
				$categories = $this->makeCategoryList ( $items, $data ['category_id'] );
				
				echo "                <tr>
                  <td>{$i}</td>
                  <td>" . ($data ['active'] == 'f' ? '<del>' : '') . "<p class=\"text-muted\">{$categories}</p>
			<a href=\"/course/{$data[course_id]}\" target=\"_blank\">{$data[course_name]}</a></td>
                  <td><a href=\"/admin_user/edit/{$data[user_id]}\" target=\"_blank\">{$data[user_name]}</a>
			<p>$data[user_company] <small>$data[user_position]</small></p></td>
                  <td>{$data[created]}</td>
                  <td>{$data[planned_date]}</td>
                  <td>" . ($data ['active'] == 't' ? "<a href=\"/admin_requests/approve/{$data[id]}\" title=\"{$translator->approve}\"><span class=\"glyphicon glyphicon-ok\"></span></a>" : '') . "</td>
                  <td><a href=\"/admin_requests/delete/{$data[id]}\" title=\"{$translator->del}\" onClick=\"return confirm('{$translator->AreYouSure}');\"><span class=\"glyphicon glyphicon-trash\"></span></a></td>
                </tr>" . NL;
				$i ++;
			}
		}
		
		echo <<< EOF
	    </table>
	</div>

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
		$this->role = User::ADMIN;
	}
}
