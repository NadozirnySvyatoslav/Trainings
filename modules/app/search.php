<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../course.php';
include_once __DIR__ . '/../category.php';
include_once __DIR__ . '/../training.php';
include LC_PATH . '/courses.php';
class SearchPage extends AuthorizedPage {
	function displayBody() {
		parent::displayBody ();
		
		$translator = new Translator ();
		$course = new Course ();
		$page = $_GET ['page'];
		$offset = intval ( $page ) * ITEMS_IN_PAGE;
		if (isset ( $_GET ['search'] )) {
			$search=$_GET['search'];
			unset ( $_GET ['search'] );
			unset ( $_GET ['page'] );
			
		} else {
			$search = null;
		}
		$cnt = $course->getCount ( array('search' => $search ));
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		echo <<< EOF
<div class="container-fluid">
<div class="col-md-10 col-lg-8 col-lg-offset-2 ">
	<h4 class="page-header">{$translator->Search_result}: <span class="badge">$cnt</span></h4>
	{$pagination}
EOF;
	
$enum=$course->enumerate(array('search' => $search),$offset, ITEMS_IN_PAGE);
	if (isset($enum)){
foreach($enum as $data){
	echo <<< EOF
		<h4>{$data[name]}</h4>
		<h6>{$data[category_name]}</h6>
		<p>{$data[describe]}</p>
		<p><a href="/course/{$data[id]}">{$translator->Go_to}...</a></p>
EOF;
	
}
	}else{
		$search=htmlspecialchars($search);
		echo <<< EOF
		<h4>{$translator->no_results} по "$search"</h4>		
EOF;
		
	}
	
	
echo <<< EOF
	{$pagination}
	</div>
</div> <!-- /container -->

EOF;
	}
	function defaultRole() {
		$this->role = User::USER;
	}
}
