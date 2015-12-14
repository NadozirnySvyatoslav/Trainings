$(function(){
    $("a[data-toggle='toggler']").each(function(){
//	    alert($(this).attr("parent-id"));
	$(this).click(function(){
	    var list_index=$(this).attr("parent-id");
	    var element_type=$(this).attr("type");
	    $('.category-list[index="'+list_index+'"]').append("<li class=\"list-group-item\">"+
	    "<form action=\"/admin_manager/add_"+element_type+"/"+list_index+
	    "\" method=\"post\"><div class=\"row\"><div class=\"col-lg-4\">"+
	    "<div class=\"input-group\">"+
	    "<input name=\"name\" id=\"input\" class=\"form-control\">"+
	    "<span class=\"input-group-btn\"><button type=\"submit\" class=\"btn btn-primary\">"+
	    "<span class=\"glyphicon glyphicon-plus\"></span></button></span></div></div></div></form></li>");
	    $("#input").focus();
	return false;
	});

});

    $("a[data-toggle='imagetoggler']").each(function(){
//	    alert($(this).attr("parent-id"));
	$(this).click(function(){
	    var list_index=$(this).attr("parent-id");
	    
	    $('.category-list[index="'+list_index+'"]').append("<li class=\"list-group-item\">"+
	    "<form action=\"/admin_manager/add_image/"+list_index+
	    "\" method=\"post\" enctype=\"multipart/form-data\"><div class=\"row\"><div class=\"col-lg-4\">"+
	    "<div class=\"input-group\">"+
	    "<input name=\"file\" id=\"input\" type=\"file\" class=\"form-control\">"+
	    "<span class=\"input-group-btn\"><button type=\"submit\" class=\"btn btn-primary\">"+
	    "<span class=\"glyphicon glyphicon-plus\"></span></button></span></div></div></div></form></li>");
	    $("#input").focus();
	return false;
	});

}); 

});