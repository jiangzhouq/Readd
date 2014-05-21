/*
 *Recent Comment by dong
 *version 1.1
 *Author URL:http://www.dearzd.com
 */
function recent_comments(){
	var r_div = $( '#recent-comments' );
	var r_ul = $( '#recent-comments ul' );
	var ho;
	$( '#recent-comments li' ).hover(function(){
		var t = $(this);
		r_div.css({'opacity':'1'});
		ho = setTimeout(function(){
			t.find( '.recent-comments-meta' ).css({'opacity':0}).stop().show().animate({opacity:1},0);
		},50);
		
	},function(){
		clearTimeout(ho);
		r_div.css({'opacity':'0.8'});
		$(this).find( '.recent-comments-meta' ).hide();
	});
	$( '.recent-comments-bottom' ).click(function(){
		scroll_down(r_ul);
	});
	$( '.recent-comments-top' ).click(function(){
		scroll_up(r_ul);
	});
	setTimeout(function(){
		r_div.css({'right':'1px'});
		admintest();
	},1000);
	$(window).resize(function(){
		if ( $(window).width()<800 ){
			r_ul.hide();
		}else{
			r_ul.show();
		}
		r_ul.animate({marginTop:36},600);
		admintest();
	});
	
	$( '#recent-comments' ).mousewheel(function(event) {
		if ( event.deltaY < 0 ){
			scroll_down(r_ul);
		}else if ( event.deltaY > 0 ){
			scroll_up(r_ul);
		}
		return false;
	});
}
function scroll_down(r_ul){
	var h1 = $( '#recent-comments' ).height();
	var h2 = $( '#recent-comments ul' ).height();
	var move = h1-h2-34;
	r_ul.stop().animate({marginTop:move},600);
}
function scroll_up(r_ul){
	r_ul.stop().animate({marginTop:36},600);
}
function admintest(){
	var h = $(window).height();
	$( '#recent-comments' ).css({'height':h});
	if ( $( '#wpadminbar' )[0] ){
		$( '#recent-comments' ).css({'margin-top':$( '#wpadminbar' ).height(),'height':h-$( '#wpadminbar' ).height()});
	}
}
$(document).ready(function(){
	//recentcomments
	$.ajax({
		url:Readd.ajaxurl+"?action=ajax_recent_comments_load",
		type:'GET',
		beforeSend:function(){
		
		},
		error:function(){
			
		},
		success:function(data){
			$( '#wrapper' ).after(data);
			recent_comments();
		}
	});
});