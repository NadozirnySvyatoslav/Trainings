<?php
require __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/users.php';
include_once __DIR__ . '/../user.php';
include_once __DIR__ . '/../sendmail.php';
class AdminUserPage extends AuthorizedPage {
	function save() {
		$translator = new Translator ();
		$user = new User ();
		try {
			
			$data = array (
					'first_name' => '',
					'last_name' => '',
					'email' => '',
					'password' => '',
					'company' => '',
					'position' => '',
					'phone' => '',
					'country' => '',
					'city' => '',
					'interface' => '',
					'role_id' => 0,
					'subscribe' => false,
					'active' => false 
			);
			foreach ( $data as $key => $val ) {
				if (isset ( $_POST [$key] ))
					$data [$key] = $_POST [$key];
			}
			$data ['name'] = $_POST ['last_name'] . ' ' . $_POST ['first_name'];
			foreach ( $user->roles as $key => $val ) {
				if (isset ( $_POST ['role' . $val] ))
					$data ['role_id'] += $val;
			}
			$id = array_shift ( $this->param );
			$user->update ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_updated}</h1>
      </div>
   <p class="lead">{$translator->User_updated_Desc}</p>
      <p >{$translator->Error_Backusers}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_users';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_error}</h1>
      </div>
   <p class="lead">{$translator->User_error_Desc}</p>
      <p >{$translator->Error_Backusers}</p>
</div>

EOF;
		}
	}
	function delete() {
		$translator = new Translator ();
		$user = new User ();
		try {
			
			$id = array_shift ( $this->param );
			$user->delete ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_updated}</h1>
      </div>
   <p class="lead">{$translator->User_updated_Desc}</p>
      <p >{$translator->Error_Backusers}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_users';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_error}</h1>
      </div>
   <p class="lead">{$translator->User_error_Desc}</p>
      <p >{$translator->Error_Backusers}</p>
</div>

EOF;
		}
	}
	function edit() {
		$translator = new Translator ();
		$user = new User ();
		
		$data = $user->get ( array (
				'id' => array_shift ( $this->param ) 
		) );
		foreach ( $data as $key => $val ) {
			$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		if ($data ['subscribe'] == 't')
			$subscribe = "checked";
		if ($data ['active'] == 't')
			$active = "checked";
		$$data ['interface'] = 'selected';
		
		foreach ( $user->roles as $key => $val ) {
			if (($val & $data ['role_id']) > 0) {
				$valname = 'role' . $val;
				$$valname = 'checked';
			}
		}
		
		echo <<< EOF
<div class="container">
<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" action="/admin_user/save/{$data[id]}" method="post">
	<h2>{$translator->edit}</h2>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control input-lg" 
			placeholder="{$translator->First_Name}" tabindex="1" required value="{$data[first_name]}">
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="last_name" id="last_name" class="form-control input-lg" 
			placeholder="{$translator->Last_Name}" tabindex="2" required value="{$data[last_name]}">
		    </div>
		</div>
	    </div>
	    <div class="form-group">
		<input type="email" name="email" id="email" class="form-control input-lg" 
		placeholder="{$translator->Email_Address}" tabindex="3" required value="{$data[email]}">
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="password" id="password" class="form-control input-lg" 
			placeholder="{$translator->Password}" tabindex="4" required value="{$data[password]}">
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="company" id="company" class="form-control input-lg" 
			placeholder="{$translator->Company}" tabindex="6" required value="{$data[company]}">
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="position" id="position" class="form-control input-lg" 
			placeholder="{$translator->Position}" tabindex="7" required value="{$data[position]}">
		    </div>
		</div>
	    </div>
    	    <div class="form-group">
		<input type="phone" name="phone" id="phone" class="form-control input-lg" 
		placeholder="{$translator->Phone}" tabindex="8" required value="{$data[phone]}">
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="country" id="country" class="form-control input-lg" 
			placeholder="{$translator->Country}" tabindex="9" required value="{$data[country]}">
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="city" id="city" class="form-control input-lg" 
			placeholder="{$translator->City}" tabindex="10" required value="{$data[city]}">
		    </div>
		</div>
	    </div>

	    <div class="form-group">
		<label>{$translator->Interface_lan}: </label>
		<select id="interface" name="interface" title="" data-size="3">
		<option value="uk" $uk>Українська</option>
		<option value="ru" $ru>Русский</option>
		<option value="en" $en>English</option>
		</select>
	    </div>
	    <div class="form-group">
		<label>{$translator->Roles}: </label>
		<div class="checkbox">

EOF;
		
		foreach ( $user->roles as $key => $val ) {
			$valname = 'role' . $val;
			echo "		<label><input type=\"checkbox\" name=\"$valname\"  " . $$valname . " value=\"$val\"> " . User::RolesAsString ( $val ) . "</label>" . NL;
		}
		
		echo <<< EOF
		</div>
	    </div>
	    <div class="form-group">
		    <span class="button-checkbox">
			<input type="checkbox" name="subscribe" id="subscribe"  value="true" {$subscribe}>
			{$translator->Subscribe}
		    </span>
	    </div>
	    <div class="form-group">
		    <span class="button-checkbox">
                        <input type="checkbox" name="active" id="active"  value="true" {$active}>
			{$translator->Active}
		    </span>
	    </div>
	    <div class="form-group">
	    {$translator->Created}: {$data[created]}
	    </div>
	    <div class="form-group">
	    {$translator->Last_in}: {$data[lastin]}
	    </div>

	    <div class="row">
		<div class="col-xs-12 col-md-6"><input type="submit" value="{$translator->Save}" class="btn btn-primary" tabindex="13"></div>
	    </div>
    </form>
</div>
</div> <!-- /container -->

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		
		if (! is_array ( $this->param ))
			return;
		$func = array_shift ( $this->param );
		switch ($func) {
			case 'edit' :
				$this->edit ();
				break;
			case 'save' :
				$this->save ();
				break;
			case 'delete' :
				$this->delete ();
				break;
		}
	}
	function defaultRole() {
		$this->role = User::ADMIN;
	}
}
