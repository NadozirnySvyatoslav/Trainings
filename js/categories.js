$(function(){
    $("a[data-toggle='toggler']").each(function(){
//	    alert($(this).attr("parent-id"));
	$(this).click(function(){
	    var list_index=$(this).attr("parent-id");

	    $('.category-list[index="'+list_index+'"]').append("<li class=\"list-group-item\">"+
	    "<form action=\"/admin_categories/add/"+list_index+
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