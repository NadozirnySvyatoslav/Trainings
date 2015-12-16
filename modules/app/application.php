<?php

/**
 Main application
 
 */
class Application {
	const DEFAULT_LOCALE = 'uk'; // ukrainian as default
	const DEFAULT_LIFETIME = 14400; // 4 hours to save session
	function __construct() {
		$this->detectLanguage ();
		$this->setSession ();
		$this->setModule ();
	}
	
	/**
	 * Load module by name in url host/module_name
	 * If no module found, load module with error 404 - "Page not found"
	 * After module loading create page and display it.
	 */
	function loadModule($modname, $param = null) {
		if (file_exists ( __DIR__ . '/' . $modname . '.php' )) {
			require __DIR__ . '/' . $modname . '.php';
			include __DIR__ . '/modules_config.php';
			if (class_exists ( $pages [$modname] )) {
				try{
				    $page = new $pages [$modname] ( $param );
				    $page->display ();
				}catch(NoAccessException $e){
				    header ( "Location: /403/" . $modname );
				    echo "No access to ".$_SESSION['user_name'];
				}
			} else
				header ( "Location: /500/" . $modname );
		} else
			header ( "Location: /404/" . $modname );
	}
	
	/**
	 * detect module name from URL and load it
	 * if no module found, redirect to default pages
	 */
	function setModule() {
		$url = preg_match_all ( '/\/([A-Za-z_0-9\%\.\ \-\p{L}]*)/u', $_SERVER ['REDIRECT_URL'], $list );
		$param = $list [1];
		if (isset ( $param ))
			$modname = array_shift ( $param );
		if (! $modname) {
			if (! isset ( $_SESSION ['user'] ))
				header ( 'Location: /signin' );
			else {
				include_once __DIR__ . '/../user.php';
				if ($_SESSION ['role_id'] == User::GUEST)
					header ( 'Location: /noaccessyet' );
				else
					header ( 'Location: /trainings' );
			}
			exit ();
		} else
			$this->loadModule ( $modname, $param );
	}
	
	/**
	 * Load session from table session
	 */
	function setSession() {
		try {
			include __DIR__ . '/../session.php';
			$this->session = new Session ();
		} catch ( Exception $e ) {
			echo "No session support or error, exiting... Try clean cookies and repeat " . $e->getMessage ();
			exit ();
		}
	}
	/**
	 * Detect how to set default language
	 * by browser settings or by previous session
	 */
	function detectLanguage() {
		if (! isset ( $_COOKIE ['locale'] )) {
			/**
			 * Set default locale from browser
			 * Example: HTTP_ACCEPT_LANGUAGE="ru;q=0.8,en-US;q=0.5,en;q=0.3"
			 * best match=ru with 0.8, and uk will be selected if root/lang/ru folder exists
			 * otherwise default "uk" will be selected
			 */
			
			if (($list = strtolower ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ))) {
				if (preg_match_all ( '/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list )) {
					$language = array_combine ( $list [1], $list [2] );
					foreach ( $language as $n => $v )
						$language [$n] = $v ? $v : 1;
					arsort ( $language, SORT_NUMERIC );
				}
			}
			foreach ( $language as $lc => $val ) {
				$res = $this->setLocale ( $lc );
				if ($res)
					break;
			}
			if (! $res)
				$this->setLocale ( self::DEFAULT_LOCALE );
		} else {
			/* Set default locale from previous session */
			if (! $this->setLocale ( $_COOKIE ['locale'] ))
				$this->setLocale ( self::DEFAULT_LOCALE );
		}
		setcookie ( 'locale', LC, time () + self::DEFAULT_LIFETIME, '/' );
	}
	
	/**
	 * Set global variables for language and path to language files
	 */
	function setLocale($locale) {
		if (file_exists ( __DIR__ . '/../../lang/' . $locale )) {
			define ( LC, $locale );
			define ( LC_PATH, $_SERVER ['DOCUMENT_ROOT'] . '/lang/' . LC );
			return true;
		}
		return false;
	}
}
