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
					Security::checkEditor($this->id);
					//touch($root.'/'.$_POST['name']);
					file_put_contents($root, $_POST['data']);
					header ( 'Location: /admin_manager/list/'.$this->id );
					exit ();
					break;
				case 'get_image':
					$filename=basename($root);
					if (preg_match('/pdf$/i',$filename))
					    header("Content-type: application/pdf");
					else if (preg_match('/jpe?g$/i',$filename))
					    header("Content-type: image/jpeg");
					else if (preg_match('/gif$/i',$filename))
					    header("Content-type: image/gif");
					else if (preg_match('/png$/i',$filename))
					    header("Content-type: image/png");
					else if (preg_match('/svg$/i',$filename))
					    header("Content-type: image/svg+xml");
					else if (preg_match('/mp4$/i',$filename))
					    header("Content-type: video/mp4");
					else if (preg_match('/wmv$/i',$filename))
					    header("Content-type: video/x-ms-wmv");
					else if (preg_match('/webm$/i',$filename))
					    header("Content-type: video/webm");
					else if (preg_match('/mov$/i',$filename))
					    header("Content-type: video/quicktime");
					else    
					    header("Content-type: application/octet-stream");
					    
					header("Content-Disposition: inline; filename=\"$filename\"");
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
if (preg_match('/.*(swf|pdf)$/i',$this->path))
	$type="embed";
if (preg_match('/.*(mov|mp4|wmv|avi)$/i',$this->path))
	$type="video";
if (preg_match('/.*(mp3)$/i',$this->path))
	$type="audio";
	
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

	case 'video':
		echo <<< EOF
		<div class="embed-responsive embed-responsive-16by9">
		  <video class="embed-responsive-item" controls>
		  <source src="/admin_editor/get_image/{$this->id}/{$this->path}" ></source>
		  </video>
		</div>
EOF;
		break;
	case 'audio':
		echo <<< EOF
		<div class="">
		  <audio class="embed-responsive-item" controls>
		  <source src="/admin_editor/get_image/{$this->id}/{$this->path}" >
		  </audio>
		</div>
EOF;
		break;
	case 'embed':
		echo <<< EOF
		<div class="embed-responsive embed-responsive-16by9">
		  <object data="/admin_editor/get_image/{$this->id}/{$this->path}" class="embed-responsive-item"></object>
		</div>
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
		$this->role = User::EDITOR | User::EDITOR_SIMPLE;
	}
}