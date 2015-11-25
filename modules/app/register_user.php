<?php
require __DIR__ . '/nonauthorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/registeruser.php';
include_once __DIR__ . '/../user.php';
include_once __DIR__ . '/../sendmail.php';
class RegisterUserPage extends NonAuthorizedPage {
	function displayHeader() {
		parent::displayHeader ();
		echo <<<EOF
<link href="/css/signin.css" rel="stylesheet">
<link href="/css/scrollYou.css" rel="stylesheet">

EOF;
	}
	function displayFooter() {
		parent::displayFooter ();
		
		echo <<<EOF
<script src="/js/button-checkbox.js"></script>
<script type="text/javascript">

</script>

EOF;
	}
	function addUser() {
		$translator = new Translator ();
		$user = new User ();
		try {
			$user->add ( $_POST );
			
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->User_registered}</h1>
      </div>
   <p class="lead">{$translator->User_registered_Desc}</p>
      <p >{$translator->Error_Back}</p>
</div>

EOF;
		} catch ( Exception $e ) {
			echo <<< EOF
<div class="container">
   <div class="page-header">
        <h1>{$translator->Error_User_register}</h1>
      </div>

EOF;
			
			switch ($e->getCode ()) {
				case User::EXISTS :
					echo "   <p class=\"lead\">{$translator->Error_User_exists_Desc}</p>";
					break;
				case User::NO_DATA :
					echo "   <p class=\"lead\">{$translator->Error_User_no_data_Desc}</p>";
					break;
				case User::BAD_PASSWORD :
					echo "   <p class=\"lead\">{$translator->Error_User_bad_password_Desc}</p>";
					break;
				
				default :
					echo "   <p class=\"lead\">{$translator->Error_User_register_Desc}</p>";
					Sendmail::toAdmin ( var_export ( $e, true ) );
			}
			
			echo <<< EOF
      <p >{$translator->Error_Back}</p>
</div>

EOF;
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		if (isset ( $_POST ) && sizeof ( $_POST ) > 0) {
			$this->addUser ();
			return;
		}
		
		echo <<< EOF
<div class="container">
<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">
    <form role="form" action="?" method="post">
	<h2>{$translator->Header_lg} <small>{$translator->Header_sm}</small></h2>
	    <hr class="colorgraph">
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
                        <input type="text" name="first_name" id="first_name" class="form-control input-lg" placeholder="{$translator->First_Name}" tabindex="1" required>
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="last_name" id="last_name" class="form-control input-lg" placeholder="{$translator->Last_Name}" tabindex="2" required>
		    </div>
		</div>
	    </div>
	    <div class="form-group">
		<input type="email" name="email" id="email" class="form-control input-lg" placeholder="{$translator->Email_Address}" tabindex="3" required>
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="password" name="password" id="password" class="form-control input-lg" placeholder="{$translator->Password}" tabindex="4" required>
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="password" name="password_confirmation" id="password_confirmation" class="form-control input-lg" placeholder="{$translator->Confirm_Password}" tabindex="5" required>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="company" id="company" class="form-control input-lg" placeholder="{$translator->Company}" tabindex="6" required>
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="position" id="position" class="form-control input-lg" placeholder="{$translator->Position}" tabindex="7" required>
		    </div>
		</div>
	    </div>
    	    <div class="form-group">
		<input type="phone" name="phone" id="phone" class="form-control input-lg" placeholder="{$translator->Phone}" tabindex="8" required>
	    </div>
	    <div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="country" id="country" class="form-control input-lg" placeholder="{$translator->Country}" tabindex="9" required>
		    </div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-6">
		    <div class="form-group">
			<input type="text" name="city" id="city" class="form-control input-lg" placeholder="{$translator->City}" tabindex="10" required>
		    </div>
		</div>
	    </div>

	    <div class="form-group">
		<label>{$translator->Interface_lan}: </label>
		<select id="interface" name="interface" class="scrollMe" title="" data-size="3">
		<option value="uk">Українська</option>
		<option value="ru">Русский</option>
		<option value="en">English</option>
		</select>
	    </div>
	    <div class="row">
		<div class="col-xs-4 col-sm-3 col-md-3">
		    <span class="button-checkbox">
			<button type="button" class="btn" data-color="info" tabindex="12"> {$translator->I_agree}</button>
                        <input type="checkbox" name="subscribe" id="subscribe" class="hidden" value="1">
		    </span>
		</div>
		<div class="col-xs-8 col-sm-9 col-md-9">
		     {$translator->Subscribe}
		</div>
	    </div>
	    <hr class="colorgraph">
	    <div class="row">
		<div class="col-xs-12 col-md-6"><input type="submit" value="{$translator->Register}" class="btn btn-primary btn-block btn-lg" tabindex="13"></div>
		<div class="col-xs-12 col-md-6"><a href="/signin" class="btn btn-success btn-block btn-lg">{$translator->Sign_in}</a></div>
	    </div>
    </form>
</div> <!-- /container -->

EOF;
	}
}
