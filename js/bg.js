/*
 *Site Background by dong
 *version 1.0
 *Author URL:http://www.dearzd.com
 */
var bg = $('<div id=bg />'),
	bg_img = new Image();
function bg_image_pos(W,o,bg_img){
	var LH=bg_img.height/bg_img.width*W.width(),//对firefox，汗~~啊
		LW=bg_img.width/bg_img.height*W.height();
	if(LH>=W.height()){
		$(bg_img).css({
			'width':W.width(),
			'height':LH
		});
		o.css({
			'margin-left':0,
			'margin-top':(W.height()-LH)/2//垂直居中
		});
		
	}else{
		$(bg_img).css({
			'height':W.height(),
			'width':LW
		});
		o.css({
			'margin-top':0,
			'margin-left':(W.width()-LW)/2//水平居中
		});
	}
}
$(document).ready(function(){
	
	//bg_img.src = "http://localhost/DBlog/wp-content/themes/Readd/images/picture.jpg";
	$.ajax({
		type:'GET',
		url: Readd.ajaxurl+"?action=ajax_site_bg",
		success:function(data){
			bg_img.src = data;
		}
    });
	bg_img.onload = function(){
		bg.append(bg_img);
		bg.css({'position':'fixed','left':'0','top':'0','z-index':'-1','opacity':'0'});
		$( '#wrapper' ).after(bg);
		bg_image_pos($(window),bg,bg_img);
		bg.animate({opacity:1},2000);
		$(window).resize(function(){//改变窗口调整大小
			bg_image_pos($(window),bg,bg_img);
		});
	}
});