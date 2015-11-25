$('#top').click(function(){
    $('#iframe').attr('src',document.location.href+'/index.html');
return false;
});


$('#next').click(function(){
    var src=$("#iframe").get(0).contentWindow.location.href;
    var prefix=src.replace(/[A-Za-z_\-0-9\.]*$/,'');
    var page=src.replace(/(.*\/.*\/)/,'');
    var i=page.replace(/\..*/,'');
if (isNaN(i)) i=0;
i++;
    $('#iframe').attr('src',prefix+i+'.html');
return false;
});

$('#back').click(function(){
    var src=$("#iframe").get(0).contentWindow.location.href;
    var prefix=src.replace(/[A-Za-z_\-0-9\.]*$/,'');
    var page=src.replace(/(.*\/.*\/)/,'');
    var i=page.replace(/\..*/,'');
i--;
if (isNaN(i) || i==0) i='index';
    $('#iframe').attr('src',prefix+i+'.html');
return false;
});
$('#fullscreen').click(function(){
    $('#window').addClass('fullscreen');
    $('#fullscreen_media').removeClass('embed-responsive');
    $('#fullscreen_media').removeClass('embed-responsive-16by9');
    $('#fullscreen_media').addClass('fullscreen_media');
    $('#iframe').addClass('fullscreen_iframe');
    $('#top_buttons').addClass('top_buttons');
    $('#bottom_buttons').addClass('bottom_buttons');
    $('#fullscreen').addClass('collapse');
    $('#inscreen').removeClass('collapse');
return false;
});

$('#inscreen').click(function(){
    $('#window').removeClass('fullscreen');
    $('#fullscreen_media').addClass('embed-responsive');
    $('#fullscreen_media').addClass('embed-responsive-16by9');
    $('#fullscreen_media').removeClass('fullscreen_media');
    $('#iframe').removeClass('fullscreen_iframe');
    $('#top_buttons').removeClass('top_buttons');
    $('#bottom_buttons').removeClass('bottom_buttons');
    $('#inscreen').addClass('collapse');
    $('#fullscreen').removeClass('collapse');
return false;
});