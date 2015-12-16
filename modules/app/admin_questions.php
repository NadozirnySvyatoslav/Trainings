<?php
include_once __DIR__ . '/authorizedpage.php';
include_once __DIR__ . '/../translator.php';
include_once __DIR__ . '/../question.php';
include LC_PATH . '/common.php';
include LC_PATH . '/questions.php';
class AdminQuestionsPage extends AuthorizedPage {
	function init() {
		$question = new Question ();
		$func = array_shift ( $this->param );
		if (is_numeric ( $func )) {
			$this->id = $func;
		} else if ($func != '') {
			$_SERVER ['REQUEST_METHOD'] = 'POST';
			$course_id = array_shift ( $this->param );
			Security::checkEditor($course_id);
			switch ($func) {
				case 'save' :
					$data ['course_id'] = $course_id;
					$data ['type_id'] = $_POST ['type_id'];
					$data ['data'] = $_POST ['data'];
					$data ['count'] = $_POST ['count'];
					$data ['answer'] = $_POST ['answer'];
					$id = $_POST ['id'];
					try {
						$question->update ( $id, $data );
					} catch ( Exception $e ) {
					}
					header ( 'Location: /admin_questions/' . $data ['course_id'] );
					exit ();
					break;
				case 'delete' :
					try {
						$question_id = array_shift ( $this->param );
						$question->delete ( $question_id );
						echo "ok";
					} catch ( Exception $e ) {
						echo $e;
					}
					header ( 'Location: /admin_questions/' . $course_id );
					exit ();
					break;
				case 'find' :
					try {
						$data = array (
								'course_id' => $course_id,
								'type_id' => 0,
								'data' => '',
								'answer' => '' 
						);
						$q_id = $question->add ( $data );
						$data ['id'] = $q_id;
						echo json_encode ( $data );
					} catch ( Exception $e ) {
						echo $e;
					}
					exit ();
					break;
				case 'load' :
					try {
						$question_id = array_shift ( $this->param );
						$data = $question->get ( array (
								'course_id' => $course_id,
								'id' => $question_id 
						) );
						echo json_encode ( $data );
					} catch ( Exception $e ) {
						echo $e;
					}
					exit ();
					break;
				default :
			}
		} else {
			header ( 'Location: /404' );
			exit ();
		}
	}
	function displayBody() {
		parent::displayBody ();
		$translator = new Translator ();
		$question = new Question ();
		$cnt = $question->getCount ( array (
				'course_id' => $this->id 
		) );
		
		$page = $_GET ['page'];
		$offset = intval ( $page ) * ITEMS_IN_PAGE;
		
		if (sizeof ( $_GET ) > 0) {
			foreach ( $_GET as $key => $val ) {
				$values [$key] = htmlspecialchars ( $val, ENT_QUOTES );
			}
		}
		
		if ($cnt > ITEMS_IN_PAGE)
			$pagination = $this->addPaginator ( $cnt, $page );
		
		$enum = $question->enumerate ( array (
				'course_id' => $this->id 
		), $offset, ITEMS_IN_PAGE );
		$i = $offset + 1;
		
		echo <<< EOF
<h2  class="page-header">{$translator->Questions_header} <span class="badge">$cnt</span></h2>
<div class="col-lg-8">
    {$pagination}

    <div class="row">
	<div class="btn-group">
	<a href="#" class="btn btn-sm btn-primary" data-toggle="add" parent-id="{$this->id}">
	    <span class="glyphicon glyphicon-plus"></span> {$translator->add}</a>
	</div>
    </div>
    <ol id="question-list" index="question-list" start="$i">

EOF;
		if (is_object ( $enum ))
			foreach ( $enum as $data ) {
				$type = $question->types [$data ['type_id']];
				$type = $translator->$type;
				if (! $type)
					$type = "<span class=\"alert-warning\">{$translator->No_text}</span>";
				if (! $data ['data'])
					$data ['data'] = "<span class=\"alert-warning\">{$translator->No_text}</span>";
				if (! $data ['answer'])
					$data ['answer'] = "<span class=\"alert-warning\">{$translator->No_text}</span>";
				if (! $data ['count'])
					$data ['count'] = "<span class=\"alert-warning\">{$translator->No_text}</span>";
				
				echo <<< EOF
	<li class="question-item" item-id="{$data[id]}">
	    <a href="#" class="btn btn-sm" data-toggle="edit" item-id="{$data[id]}" parent-id="{$this->id}">
	    <span class="glyphicon glyphicon-pencil"></span></a>
	    <a href="#" class="btn btn-sm" data-toggle="del" item-id="{$data[id]}" parent-id="{$this->id}">
	    <span class="glyphicon glyphicon-trash"></span></a>
	    $type
	    <p>{$data[data]}</p>
	    <p>{$translator->Answer}: <small>{$data[answer]} / {$data[count]}</small></p>
	</li>

EOF;
			}
		
		echo <<< EOF
    </ol>
{$pagination}
</div>
EOF;
	}
	function displayFooter() {
		parent::displayFooter ();
		$translator = new Translator ();
		$question = new Question ();
		foreach ( $question->types as $key => $name ) {
			$types .= "<option value=\"$key\">" . htmlspecialchars ( $translator->$name ) . "</option>";
		}
		echo <<< EOF
<script src="/js/tinymce.min.js"></script>
<script>
$(function() {
    $.ajaxSetup({
        async: false
    });

 $("a[data-toggle='add']").each(function(){
        $(this).click(function(){
	    $.getJSON("/admin_questions/find/{$this->id}",
    		function(data){
        	    $('#question-list').before('<li class="list-group-item"><form action="/admin_questions/save/{$this->id}" method="post">'+
                    '<input type="hidden" name="id" value="'+data['id']+'">'+
		    '<select name="type_id" class="form-control">{$types}</select>'+
		    '<textarea name="data" id="data" rows=5 class="form-control"></textarea>'+
		    '<input name="count" type="number" min="1" placeholder="Answers" class="form-control">'+
		    '<input name="answer" class="form-control">'+
		    '<input type="submit" class="btn btn-primary">'+
		    '<a href="/admin_questions/delete/{$this->id}/'+data['id']+'" class="btn btn-danger">{$translator->Cancel}</a>'+"</form></li>\\n");
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

    		});
    	    return false;
    	});

});
 $("a[data-toggle='del']").each(function(){
        $(this).click(function(){
	    document.location.href="/admin_questions/delete/"+$(this).attr('parent-id')+"/"+$(this).attr('item-id');
    	});

});

 $("a[data-toggle='edit']").each(function(){
        $(this).click(function(){
	    item=$(this).attr('item-id');
	    $.getJSON("/admin_questions/load/{$this->id}/"+item,
    		function(data){
        	    $('.question-item[item-id="'+data['id']+'"]').html('<form action="/admin_questions/save/{$this->id}" method="post">'+
                    '<input type="hidden" name="id" value="'+data['id']+'">'+
		    '<select name="type_id" class="form-control type-id" id="'+data['id']+'">{$types}</select>'+
		    '<textarea name="data" id="data" rows=5 class="form-control">'+data['data']+'</textarea>'+
		    '<input name="count" class="form-control" type="number" min="1" placeholder="Answers" value="'+data['count']+'">'+
		    '<input name="answer" class="form-control" value="'+data['answer']+'">'+
		    '<input type="submit" class="btn btn-primary">'+
		    '<a href="/admin_questions/delete/{$this->id}/'+data['id']+'" class="btn btn-danger">{$translator->Cancel}</a>'+"</form>\\n");
		    $('#'+data['id']+'.type-id').val(data['type_id']);
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
    		});
	    return false;
    	});

});
  });


</script>


EOF;
	}
	function defaultRole() {
		$this->role = User::EDITOR | User::EDITOR_SIMPLE;
	}
}
