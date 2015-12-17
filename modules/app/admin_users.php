<?php
require __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/users.php';
include LC_PATH . '/common.php';
class AdminUsersPage extends AuthorizedPage {
	function displayFooter() {
		parent::displayFooter ();
		echo <<<EOF
<script src="/js/roles_checkbox.js"></script>

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$user = new User ();
		$page = $_GET ['page'];
		$offset = intval ( $page ) * ITEMS_IN_PAGE;
		if (isset ( $_GET ['search'] )) {
			unset ( $_GET ['search'] );
			unset ( $_GET ['page'] );
			$search = $_GET;
		} else {
			$search = null;
		}
		$cnt = $user->getCount ( $search );
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
			foreach ( $user->roles as $key => $val ) {
				if (($_GET ['role_id'] & $val) > 0) {
					$valname = 'role' . $val;
					$$valname = 'checked';
				}
			}
			if ($_GET ['role_id'] == 0) {
				$role0 = 'checked';
				$values ['role_id'] = 0;
			}
		}
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<h2  class="page-header">{$translator->Users_header} <span class="badge">$cnt</span></h2>
    <div class="row">
	<div class="col-md-1">
	<button class="btn btn-sm btn-default"  data-toggle="collapse" data-target="#filter">
	    <span class="glyphicon glyphicon-filter"></span> {$translator->Filter}</button>
	</div>
    </div>
    <div class="row">
        <div class="col-md-5 collapse" id="filter">
	    <form role="form" class="form-horizontal" action="?" method="get">
		<div class="form-group">
		    <div class="form-group">
		    <label for="name" class="control-label col-sm-3">{$translator->Name}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="name" name="name" value="{$values[name]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="email" class="control-label col-sm-3">{$translator->Email}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="email" name="email" value="{$values[email]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="company" class="control-label col-sm-3">{$translator->Company}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="company" name="company" value="{$values[company]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="phone" class="control-label col-sm-3">{$translator->Phone}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="phone" name="phone"  value="{$values[phone]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="country" class="control-label col-sm-3">{$translator->Country}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="country" name="country" value="{$values[country]}">
		    </div>
		    </div>
		    <div class="form-group">
		    <label for="city" class="control-label col-sm-3">{$translator->City}:</label>
		    <div class="col-sm-8">
		    <input type="text" class="form-control" id="city" name="city" value="{$values[city]}">
		    </div>
		    </div>
		    <div class="col-sm-offset-3">

EOF;
		
		foreach ( $user->roles as $key => $val ) {
			$valname = 'role' . $val;
			
			echo "		    <input type=\"checkbox\" class=\"role_id\" id=\"$valname\" value=\"$val\" " . $$valname . ">
		    <label for=\"$valname\">" . User::RolesAsString ( $val ) . "</label>" . NL;
		}
		
		echo <<<EOF
		    <input class="sr-only" id="role_id" name="role_id" value="{$values[role_id]}">
		    <input class="sr-only" name="search" value="1">
		    </div>
		    <div class="form-group">
		    <div class="col-sm-offset-3 col-md-10">
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
                  <th>{$translator->Name}</th>
                  <th>{$translator->Roles}</th>
                  <th>{$translator->Email}</th>
                  <th>{$translator->Phone}</th>
                  <th>{$translator->Company}</th>
                  <th>{$translator->Place}</th>
                </tr>
              </thead>
              <tbody>

EOF;
		
		$enum = $user->enumerate ( $search, $offset, ITEMS_IN_PAGE , 'users.last_name,users.first_name');
		if (is_object ( $enum )) {
			$i = 1 + $offset;
			foreach ( $enum as $key => $data ) {
				if ($data ['active'] == 'f')
					$active = " class=\"not-active\"";
				else
					$active = "";
				
				echo "                <tr>
                  <td>{$i}</td>
                  <td" . $active . ">{$data[name]}</td>
                  <td>" . User::RolesAsString ( $data ['role_id'] ) . "</td>
                  <td>{$data[email]}</td>
                  <td>{$data[phone]}</td>
                  <td>{$data[company]} <small>{$data[position]}</small></td>
                  <td>{$data[country]} <small>{$data[city]}</small></td>
                  <td><a href=\"/admin_user/edit/{$data[id]}\" title=\"{$translator->edit}\"><span class=\"glyphicon glyphicon-user\"></span></a></td>
                  <td><a href=\"/admin_user/delete/{$data[id]}\" title=\"{$translator->del}\" onClick=\"return confirm('{$translator->AreYouSure}');\"><span class=\"glyphicon glyphicon-trash\"></span></a></td>
                </tr>
";
				$i ++;
			}
		}
		
		echo <<< EOF
	    </table>
	</div>

EOF;
	}
	function defaultRole() {
		$this->role = User::ADMIN;
	}
}
