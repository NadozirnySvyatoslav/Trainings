<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../trainer.php';
include_once __DIR__ . '/../sendmail.php';
include LC_PATH . '/trainers.php';
class AdminTrainerPage extends AuthorizedPage {
	function save() {
		$translator = new Translator ();
		$obj = new Trainer ();
		try {
			
			$data = array (
					'name' => '',
					'email' => '',
					'data' => '' 
			);
			foreach ( $data as $key => $val ) {
				$data [$key] = $_POST [$key];
			}
			$id = array_shift ( $this->param );
			$obj->update ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_updated}</h1>
      </div>
   <p class="lead">{$translator->Trainer_updated_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_trainers';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_error}</h1>
      </div>
   <p class="lead">{$translator->Trainer_error_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>

EOF;
		}
	}
	function add() {
		$translator = new Translator ();
		$trainer = new Trainer ();
		try {
			
			$data = array (
					'name' => '',
					'email' => '',
					'data' => '' 
			);
			foreach ( $data as $key => $val ) {
				if (isset ( $_POST [$key] ))
					$data [$key] = $_POST [$key];
			}
			$id = array_shift ( $this->param );
			$trainer->add ( $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_updated}</h1>
      </div>
   <p class="lead">{$translator->Trainer_updated_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_trainers';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_error}</h1>
      </div>
   <p class="lead">{$translator->Trainer_error_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>

EOF;
		}
	}
	function delete() {
		$translator = new Translator ();
		$user = new Trainer ();
		try {
			
			$id = array_shift ( $this->param );
			$user->delete ( $id, $data );
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_updated}</h1>
      </div>
   <p class="lead">{$translator->Trainer_updated_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>
<script type="text/javascript">
window.location.href='/admin_trainers';
</script>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Trainer_error}</h1>
      </div>
   <p class="lead">{$translator->Trainer_error_Desc}</p>
      <p >{$translator->Error_Backtrainers}</p>
</div>

EOF;
		}
	}
	function edit($create = null) {
		$translator = new Translator ();
		
		if ($create)
			$func = 'add';
		else {
			$func = 'save';
			$trainer = new Trainer ();
			$data = $trainer->get ( array (
					'id' => array_shift ( $this->param ) 
			) );
			foreach ( $data as $key => $val ) {
				$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		echo <<< EOF
<div class="container">
<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" action="/admin_trainer/{$func}/{$data[id]}" method="post">
	<h2>{$translator->edit}</h2>
	    <div class="form-group">
		<input type="text" name="name" id="name" class="form-control input-lg" 
		placeholder="{$translator->Name}" tabindex="3" required value="{$data[name]}">
	    </div>
	    <div class="form-group">
		<input type="email" name="email" id="email" class="form-control input-lg" 
		placeholder="{$translator->Email}" tabindex="3" required value="{$data[email]}">
	    </div>
	    <div class="form-group">
		<label for="data">{$translator->Data}:</label> {$translator->Data_Desc}
		<textarea  name="data" id="data" class="form-control" rows="5">{$data[data]}</textarea>
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
			case 'delete' :
				$this->delete ();
				break;
		}
	}
	function defaultRole() {
		$this->role = User::ADMIN;
	}
}
