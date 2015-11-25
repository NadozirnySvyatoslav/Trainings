$(function(){
    $('.role_id').each(function(){
        // Event Handlers
        $(this).on('click', function () {
	var bits = Number($('#role_id').val());
	    if ($(this).is(':checked')){
		bits=bits | $(this).val();
	    }
	    else {
		bits=bits ^ $(this).val();
	    }
	    $('#role_id').val(bits);
        });

    });
});
