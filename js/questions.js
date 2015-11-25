$(function(){
    $("a[data-toggle='toggler']").each(function(){
	$(this).click(function(){
	    var list_index=$(this).attr("parent-id");

	    alert(list_index);
	    $('.question-list[index="'+list_index+'"]').append("<li class=\"list-group-item\">"+
	    "<form action=\"/admin_questions/add/"+list_index+
	    "\" method=\"post\"><div class=\"row\"><div class=\"col-lg-4\">"+
	    "<div class=\"input-group\">"+
	    "<input name=\"name\" id=\"input"+list_index+"\" class=\"form-control\">"+
	    "<span class=\"input-group-btn\"><button type=\"submit\" id=\"add_category\" class=\"btn btn-primary\">"+
	    "<span class=\"glyphicon glyphicon-plus\"></span></button></span></div></div></div></form></li>");
	    $("#input"+list_index).focus();
	return false;
	});

});

    $("a[data-toggle='editor']").each(function(){

	$(this).click(function(){
	    var list_index=$(this).attr("id");
//	    alert($(this).attr("id"));
	    $('.item-name[id="'+list_index+'"]').html(
	    "<form action=\"/admin_categories/save/"+list_index+
	    "\" method=\"post\"><div class=\"row\"><div class=\"col-lg-4\">"+
	    "<div class=\"input-group\">"+
	    "<input id=\"input"+list_index+"\" name=\"name\" value=\""+$('.item-name[id="'+
	    list_index+'"]').html()+"\" class=\"form-control\">"+
	    "<span class=\"input-group-btn\"><button type=\"submit\" id=\"add_category\" class=\"btn btn-primary\">"+
	    "<span class=\"glyphicon glyphicon-floppy-save\"></span></button></span>"+
	    "</div></div></div></form></li>");
	    $('#input'+list_index).focus();
	return false;
	});

});

});

<script>
$(function() {
    $.ajaxSetup({
        async: false
    });
    $( ".datetimepicker" ).each(function(){
        $(this).datetimepicker({
            locale: '{$locale}',
            format: 'YYYY-MM-DD HH:mm'
            });
        });
    $("#category_id").change(function(){
        $.getJSON("/admin_training/courses/"+$("#category_id :selected").val(),
                 function(data){
                    $("#course_id").html('');
                    $.each(data,function(i,field){
                        $('#course_id').append($('<option>', {
                            value: field[0],
                            text: field[1]
                            }));
                    });
            });
    });
    $("#searchuser").keyup(function(){
        $('#usermenu').html('');
        if ($("#searchuser").val().length > 0)
        $.getJSON("/admin_training/finduser/{$this->id}?search="+$("#searchuser").val(),
        function(res){
            $.each(res,function(i,data){
            $('#usermenu').append('<a class="list-group-item adduser list-group-item-info" '+
                                    'href="#" onclick="return adduser('+data['id']+');"><h4>'+
                                    data['name']+' <small>'+data['email']+'</small></h4><p>'+data['city']+' / '+data['company']+
                    '</p></a>'+"\\n");
                    });
        });
    });
  });
function removeuser(id){
    $.getJSON("/admin_training/removeuser/{$this->id}/"+id,function(data){
        $('table#users tr#row'+id).remove();
    });
    return false;
}
function adduser(id){
    $.getJSON("/admin_training/adduser/{$this->id}/"+id,function(data){
        $('#users').find('tbody').append('<tr id="row'+data['id']+'"><td>'+data['name']+'</td><td>'+
                data['email']+'</td><td>'+data['company']+' / '+data['city']+'</td><td>'+
                '<a href="#" onclick="return removeuser('+data['id']+');">'+
                '<span class="glyphicon glyphicon-trash"></span></a></td></tr>');
        $('#usermenu').html('');
    });

    return false;
}

</script>
