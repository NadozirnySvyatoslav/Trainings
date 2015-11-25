<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../category.php';
include LC_PATH . '/common.php';
include LC_PATH . '/categories.php';
class AdminCategoriesPage extends AuthorizedPage {
	function init() {
		$obj = new Category ();
		$func = array_shift ( $this->param );
		$id = array_shift ( $this->param );
		if ($func != '') {
			$_SERVER ['REQUEST_METHOD'] = 'POST';
			switch ($func) {
				case 'add' :
					$data ['parent_id'] = $id;
					$data ['name'] = $_POST ['name'];
					$data ['active'] = true;
					try {
						$obj->add ( $data );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_categories' );
					exit ();
					break;
				case 'save' :
					$data ['name'] = $_POST ['name'];
					try {
						$obj->update ( $id, $data );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_categories' );
					exit ();
					break;
				case 'delete' :
					try {
						$obj->delete ( $id );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_categories' );
					exit ();
					break;
				case 'activate' :
					try {
						$obj->invert ( $id );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_categories' );
					exit ();
					break;
			}
		}
	}
	function displayFooter() {
		parent::displayFooter ();
		echo <<<EOF
<script src="/js/categories.js"></script>

EOF;
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$obj = new Category ();
		$cnt = $obj->getCount ();
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		$enum = $obj->enumerate ();
		foreach ( $enum as $key => $val ) {
			if ($val ['id'] != 0)
				$this->items [$val ['parent_id']] [] = $val;
		}
		foreach ( $this->items as $key => $val ) {
			asort ( $this->items [$key] );
		}
		
		echo <<< EOF
<h2  class="page-header">{$translator->Categories_header} <span class="badge">$cnt</span></h2>
<div class="col-lg-8">
    <ul class="list-group category-list" index="0">

EOF;
		
		$this->put_list ();
		
		echo <<< EOF
    </ul>
    <div class="row">
	<div class="btn-group">
	<a href="#" class="btn btn-sm" data-toggle="toggler" parent-id="0">
	    <span class="glyphicon glyphicon-plus"></span> {$translator->add}</a>
	</div>
    </div>

</div>
EOF;
	}
	function put_list($parent = 0) {
		$translator = new Translator ();
		foreach ( $this->items [$parent] as $key => $data ) {
			
			echo "                <li class=\"list-group-item" . ($data ['active'] == 'f' ? ' not-active' : '') . "\" >
                  <span class=\"item-name\" id=\"$data[id]\">{$data[name]}</span>
		  <a href=\"/admin_categories/activate/{$data[id]}\"  title=\"{$translator->activate}\">
		    <span class=\"glyphicon glyphicon-ok\"></span></a>
                  <a href=\"#\" title=\"{$translator->edit}\" data-toggle=\"editor\" id=\"$data[id]\">
		    <span class=\"glyphicon glyphicon-pencil\"></span></a>
                  <a href=\"/admin_categories/delete/{$data[id]}\" title=\"{$translator->del}\" 
		    onClick=\"return confirm('{$translator->AreYouSure}');\">
		    <span class=\"glyphicon glyphicon-trash\"></span></a>
" . NL;
			echo "<ul class=\"list-group category-list\" index=\"{$data[id]}\">" . NL;
			if (isset ( $this->items [$data ['id']] ) && $data ['id'] != 0) {
				$this->put_list ( $data ['id'] );
			}
			echo "</ul>
		  <a href=\"#\"  data-toggle=\"toggler\" parent-id=\"$data[id]\" title=\"{$translator->add}\">
		    <span class=\"glyphicon glyphicon-plus\"></span></a>
</li>" . NL;
		}
	}
	function defaultRole() {
		$this->role = User::EDITOR;
	}
}
