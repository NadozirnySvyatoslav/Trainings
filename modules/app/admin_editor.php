<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/categories.php';
class AdminEditorPage extends AuthorizedPage {
	function init() {
		$func = array_shift ( $this->param );
		$this->id = array_shift ( $this->param );
		
		$this->path=implode('/', $this->param);
			$root=$_SERVER['DOCUMENT_ROOT'].'/../files/courses/'.$this->id;
			if ($this->path) $root.='/'.$this->path;
			switch ($func) {
				case 'save' :
					//touch($root.'/'.$_POST['name']);
					file_put_contents($root, $_POST['data']);
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'get_image':
					readfile($root);
					exit;
					break;
				case 'view':
					break;
				default:
					header ( 'Location: /404' );
					exit ();
			}
		
	}
	function displayFooter() {
		parent::displayFooter ();
		echo <<<EOF
<script src="/js/tinymce.min.js"></script>
<script>
tinymce.init({selector:'textarea#data',
		    plugins: [
			    "advlist autolink lists link image charmap print preview anchor",
			    "searchreplace visualblocks code fullscreen",
			    "insertdatetime media table contextmenu paste"
			],
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image",
			autosave_ask_before_unload: false,
			min_height: 160,
			height : 180});				
</script>
EOF;
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$course = new Course ();
		$category = new Category ();
		$data = $course->get ( array (
				'id' => $this->id
		) );
		foreach ( $data as $key => $val ) {
			$data [$key] = htmlspecialchars ( $val, ENT_QUOTES );
		}
		$category_id = $data ['category_id'];
		;
		
		$enum = $category->enumerate ( null );
		if ($enum) {
			foreach ( $enum as $key => $val ) {
				if ($val ['id'] != 0)
					$items [$val ['id']] = $val;
			}
			$categories = $this->makeCategoryList ( $items, $category_id );
		}
		$path=preg_replace('/\//','</li><li>',$this->path);
		echo <<< EOF
<h4>$categories</h4>
<h2  class="page-header">{$data[name]}</h2>
<ol class="breadcrumb">
	<li><a href="/admin_manager/list/{$this->id}"><span class="glyphicon glyphicon-home"></span> {$translator->Back}</a>
	<li>{$path}
</ol>

EOF;
    
if (preg_match('/.*(jpe?g|png|gif|svg)$/i',$this->path))
	$type="image";
if (preg_match('/.*(txt|htm|html)$/i',$this->path))
	$type="text";

	
switch($type){	
	case 'text':
		echo <<< EOF
<div class="col-lg-8">
    <form action="/admin_editor/save/{$this->id}/{$this->path}" method="post">
		<textarea name="data" id="data" rows=5 class="form-control">

EOF;
		echo htmlspecialchars(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../files/courses/'.$this->id.'/'.$this->path));
		echo <<< EOF
    		</textarea>
<button type="submit" class="btn btn-md btn-primary">{$translator->Save}</button>		
    </form>
</div>
EOF;
		break;
	case 'image':
		echo <<< EOF
		<img src="/admin_editor/get_image/{$this->id}/{$this->path}" class="img-responsive">
		
EOF;
		break;
}
	}
	function makeCategoryList(&$items, $category_id) {
		$category = $items [$category_id] ['name'];
		if ($items [$category_id] ['parent_id'] != 0)
			$category = $this->makeCategoryList ( &$items, $items [$category_id] ['parent_id'] ) . "<span class=\"glyphicon glyphicon-menu-right\"></span>" . $category;
			return $category;
	}	

	function defaultRole() {
		$this->role = User::EDITOR;
	}
}