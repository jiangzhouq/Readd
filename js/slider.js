/*
 *Slider by dong
 *version 1.0
 *Author URL:http://www.dearzd.com
 */
var img_src = new Array(),
	slider_post =new Array(),
	slider = $('<div id=slider/>'),
	slider_bg = $('<div class=slider-bg/>'),
	slider_wrapper = $('<div class=slider-wrapper/>'),
	slider_title = $('<div class=slider-title/>'),
	slider_content = $('<div class=slider-content/>'),
	slider_nav = $('<div class=slider-nav/>'),
	slider_nav_ul = $('<ul class=slider-nav-ul/>'),
	index=0,index_old=0,time,time2;
function slide_load(){	
	$.ajax({//获取图片地址
		url:Readd.ajaxurl+"?action=ajax_slider_bg",
		type:'GET',
		beforeSend:function(){
		
		},
		error:function(){
		
		},
		success:function(data){
			img_src =data.split('<!--bg_src-->');
			
			if(img_src[0]){//获取文章内容
				$.ajax({
					url:Readd.ajaxurl+"?action=ajax_slider_post&&post_length="+img_src.length,
					type:'GET',
					beforeSend:function(){
						
					},
					error:function(){
						
					},
					success:function(data){
						slider_post = data.split('<!--slider_post-->');
						slide(slider_post);
					}
				});
			}else return;
		}
	});

}
function slide(slider_post){//slider初始化
	$( '#picture' ).append(slider);
	slider.append(slider_bg).append(slider_wrapper).append(slider_nav);
	
	//循环显示slider背景
	for( var i=0;i<img_src.length;i++){
		var bg_new = new Image;
		bg_new.src = img_src[i];
		slider_bg.append('<span id=slider-bg-'+i+'/>');
		$( '#slider-bg-'+i).append(bg_new).hide();
	}
	
	slider_nav.append(slider_nav_ul);
	//循环显示导航图片
	for(var i=0;i<img_src.length;i++){
		var nav_new = new Image();
		nav_new.src=img_src[i];
		slider_nav_ul.append('<li id=slider-nav-'+i+'/>');
		$( '#slider-nav-'+i ).append(nav_new);
	}
	
	slider_wrapper.append(slider_post[index]);
	slide_show(index);//显示第一张幻灯
	
	//导航图片切换
	$( '.slider-nav li:first' ).addClass('slider-current');
	$( '.slider-nav li' ).click(function(){
		index = $(this).attr("id").replace("slider-nav-","") -1;
		index_old = $( '.slider-current' ).attr("id").replace("slider-nav-","") -1;
		if( index!=index_old ){slide_next(index);}
	});	
	slide_hover();
	
	time = setTimeout(function(){
		slide_next(index);
	},8000);
	
}
function slide_next(index){//slider播放
	clearTimeout(time);
	if( index<img_src.length-1 ){
		index +=1;
	}else{
		index = 0;
	}
	
	$( '.slider-title' ).stop().animate({marginLeft:-30,opacity:0},500);
	$( '.slider-content' ).stop().animate({marginTop:30,opacity:0},500,function(){
		$( '.slider-wrapper' ).empty();
		$( '.slider-wrapper' ).append(slider_post[index]);
		slide_show(index);
	});
	
	$( '.slider-current' ).removeClass('slider-current');
	$( '#slider-nav-'+index ).addClass('slider-current');
	time = setTimeout(function(){
		slide_next(index);
	},8000);
}
function slide_hover(){//导航栏响应鼠标hover事件
	$( '.slider-nav li').css({'bottom':'-33px','opacity':'0'});
	var nav_li=new Array(),i=0;
	nav_li = $( '.slider-nav li' );
	$( '#slider' ).hover(function(){
		slide_hover_up(i,nav_li);
	},function(){
		slide_hover_down(i,nav_li);
	});
	
}
function slide_hover_up(i,nav_li){
	$( nav_li[i] ).stop().animate({bottom:0,opacity:1},340);
	i++;
	if(i<nav_li.length){
		setTimeout(function(){
			slide_hover_up(i,nav_li);
		},40);
	}
}
function slide_hover_down(i,nav_li){
	$( nav_li[i] ).stop().animate({bottom:-33,opacity:0},340);
	i++;
	if(i<nav_li.length){
		setTimeout(function(){
			slide_hover_down(i,nav_li);
		},40);
	}
}
function slide_show(i){
	$( '.slider-title' ).animate({marginLeft:-30,opacity:0},0);
	$( '.slider-content' ).animate({marginTop:30,opacity:0},0);
	setTimeout(function(){
		$( '.slider-title' ).animate({marginLeft:0,opacity:1},500,function(){
		});
	},200);
	$( '.slider-content' ).animate({marginTop:0,opacity:1},500);
	//切换背景图片
	if( $( '.bg-current' ) ){$( '.bg-current' ).fadeOut(1200,function(){$(this).removeClass('bg-current');});}
	$( '#slider-bg-'+i).fadeIn(1200,function(){$(this).addClass('bg-current');});
}
$(document).ready(function(){
	slide_load();
});