<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include LC_PATH . '/common.php';

class AdminManagerPage extends AuthorizedPage {
	function init() {
		$func = array_shift ( $this->param );
		$this->id = array_shift ( $this->param );
		$path=implode('/', $this->param);
			$root=$_SERVER['DOCUMENT_ROOT'].'/../files/courses/'.$this->id;
			if ($path) $root.='/'.$path;
			if ($_SERVER ['REQUEST_METHOD'] == 'POST') { 
			    Security::checkEditor($this->id);
			    if (!file_exists($root)) mkdir($root);
			}
			switch ($func) {
				case 'add_file' :
					touch($root.'/'.$_POST['name']);
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'add_folder' :
					mkdir($root.'/'.$_POST['name']);
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'add_image' :
					var_dump($_FILES);
					if (is_array($_FILES['file'])){
						
						copy($_FILES['file']['tmp_name'], $root."/".$_FILES['file']['name']);
						
					}
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
					
				case 'delete' :
					Security::checkEditor($this->id);
					try {
						
						unlink( $root );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'delete_dir' :
					Security::checkEditor($this->id);
					try {
						
						rmdir( $root );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'list':
					break;
				default:
					header ( 'Location: /404' );
					exit ();
			}
		
	}
	function displayFooter() {
		parent::displayFooter ();
		echo <<<EOF
<script src="/js/manager.js"></script>

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$course = new Course ();
		$data = $course->get ( array (
				'id' => $this->id
		) );
		foreach ( $data as $key => $val ) {
			$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		
		echo <<< EOF
<h2  class="page-header">{$data[name]}</h2>
<h4>{$data[category_name]}</h4>
<div class="col-lg-8">
    <ul class="list-group category-list" index="{$this->id}/">

EOF;
		$root=$_SERVER['DOCUMENT_ROOT'].'/../files/courses/'.$this->id;
		$this->put_list ($root);

		echo <<< EOF
    </ul>
    <div class="row">
	
	<a href="#"  data-toggle="toggler" parent-id="{$this->id}/" title="{$translator->addfile}" type="file">
			<span class="glyphicon glyphicon-file"></span> </a>
			<a href="#"  data-toggle="toggler" parent-id="{$this->id}/" title="{$translator->addfolder}" type="folder">
			<span class="glyphicon glyphicon-folder-open"></span> </a>
	
    </div>

</div>
EOF;
	}
	function put_list($parent = 0) {
		$translator = new Translator ();
		//error_reporting(E_ALL);
		$files=glob($parent."/*");
		foreach ( $files as $filename ) {
			$rootpath=$_SERVER['DOCUMENT_ROOT'].'/../files/courses/'.$this->id;
			$filepath=substr($filename,strlen($rootpath)+1);
			$filename=basename($filename);	
			
			if (is_dir($parent.'/'.$filename)){
			echo "
			<li class=\"list-group-item\" ><strong>{$filename}/</strong>
			<a href=\"#\"  data-toggle=\"toggler\" parent-id=\"{$this->id}/{$filepath}\" title=\"{$translator->addfile}\" type=\"file\">
			<span class=\"glyphicon glyphicon-file\"></span> </a>
			<a href=\"#\"  data-toggle=\"imagetoggler\" parent-id=\"{$this->id}/{$filepath}\" title=\"{$translator->addpicture}\" >
			<span class=\"glyphicon glyphicon-picture\"></span> </a>
			<a href=\"#\"  data-toggle=\"toggler\" parent-id=\"{$this->id}/{$filepath}\" title=\"{$translator->addfolder}\" type=\"folder\">
			<span class=\"glyphicon glyphicon-folder-open\"></span> </a>
			<a href=\"/admin_manager/delete_dir/{$this->id}/{$filepath}\" title=\"{$translator->del}\"
			onClick=\"return confirm('{$translator->AreYouSure}');\">
			<span class=\"glyphicon glyphicon-trash\"></span></a>		
			
					<ul class=\"list-group category-list\" index=\"{$this->id}/$filepath\">" . NL;
				$this->put_list ( $parent.'/'.$filename);
			echo "</ul>
					".NL;
			}else{
				echo "
			<li class=\"list-group-item\" >
			<a href=\"/admin_editor/view/{$this->id}/{$filepath}\" title=\"{$translator->edit}\" >
			{$filename}</a>
			<a href=\"/admin_manager/delete/{$this->id}/{$filepath}\" title=\"{$translator->del}\"
			onClick=\"return confirm('{$translator->AreYouSure}');\">
			<span class=\"glyphicon glyphicon-trash\"></span></a>".NL;
			}
			echo "
			</li>" . NL;
		}
	}
	function makeCategoryList(&$items, $category_id) {
		$category = $items [$category_id] ['name'];
		if ($items [$category_id] ['parent_id'] != 0)
			$category = $this->makeCategoryList ( &$items, $items [$category_id] ['parent_id'] ) . "<span class=\"glyphicon glyphicon-menu-right\"></span>" . $category;
			return $category;
	}
	function defaultRole() {
		$this->role = User::EDITOR | User::EDITOR_SIMPLE;
	}
}