/*
 *Gallery by dong
 *version 1.0
 *Author URL:http://www.dearzd.com
 */
var photo = new Array(),//所有图片
	gallery_index = 0,gallery_index_old;

function gallery(){
	if ( $( '.album' )[0] ){//如果相册，则只绑定相册，否则则绑定文章内容的所有图片
		$( '.album' ).each(function(){
			album(this);
			//alert(gallery_index);
		});
		gallery_bind($( '.the-photo' ));
	}else{
		$( '.entry-content a' ).has('img').each(function(){
			photo[gallery_index] = $(this).attr("href");
			gallery_bind($(this));
			gallery_index++;
		});
	}
	
}
//绑定图片点击展示gallery
function gallery_bind(_this){
	_this.click(function(e){//点击大图放大
		var the_photo_src = $(this).attr("href");
		//找到当前大图的index
		for(gallery_index=0;gallery_index<photo.length;gallery_index++){
			if ( the_photo_src == photo[gallery_index] ) break;
		}
		gallery_load();//Gallery完全大图载入
		//alert(gallery_index);
		e.preventDefault();
	});
}
//所有相册事件
function album(_this){
	var _this_index = gallery_index,//当前相册的初始index
		thumb_left = 0,//缩略图左边距
		img_src,//点击时需要显示的图片地址
		thumb_width = 0,//缩略图总宽度
		photo_wrap = $('<div class=photo-wrap/>'),
		the_photo = $('<a class=the-photo/>'),
		phwrap_lt = $('<div class="phwrap-corner phwrap-lt"/>'),
		phwrap_rt = $('<div class="phwrap-corner phwrap-rt"/>'),
		phwrap_lb = $('<div class="phwrap-corner phwrap-lb"/>'),
		phwrap_rb = $('<div class="phwrap-corner phwrap-rb"/>'),
		new_img = new Image();
	
	the_photo.html('<img src=""/>');
	photo_wrap.append(the_photo).append(phwrap_lt).append(phwrap_rt).append(phwrap_lb).append(phwrap_rb);
	$(_this).find( '.thumb-wrap' ).after(photo_wrap);
	
	//获取当前相册缩略图的元素
	var _this_thump_wrap = $(_this).find( '.thumb-wrap' ),
		_this_thump = $(_this).find( '.thumb' ),
		_this_thump_a = _this_thump.find('a').has('img'),
		_this_thump_left = $(_this).find( '.thumb-left' ),
		_this_thump_right = $(_this).find( '.thumb-right' );
	
	//遍历所有缩略图保存地址并绑定事件
	_this_thump_a.each(function(){
		photo[gallery_index] = $(this).attr("href");
		$(this).click(function(e){//点击缩略图显示大图
			var new_img = new Image();//防止onload不起作用
			img_src = $(this).attr("href");//防止中文不相等错误
			gallery_index_old = the_photo.attr("href");//目前显示的图片地址
			if ( img_src != gallery_index_old ){
				the_photo.find('img').hide();
				photo_wrap.addClass("loading");
				_this_thump_a.find('img').removeClass('thumb-current');//移除所有缩略图的current
				$(this).find('img').addClass('thumb-current');//当前鼠标点击的缩略图添加current
				new_img.src = img_src;
				//alert(img_src);
				new_img.onload=function(){
					photo_wrap.removeClass("loading");
					the_photo.attr("href",img_src);
					the_photo.find('img').attr("src",img_src).fadeIn();
				}
			}
			e.preventDefault();
			
		});
		gallery_index++;
		thumb_width += 69;
	});
	//alert(thumb_width);
	_this_thump.css({'width':thumb_width});
	
	//初始化显示大图
	photo_wrap.addClass("loading");
	new_img.src = photo[_this_index];
	new_img.onload=function(){
		photo_wrap.removeClass("loading");
		the_photo.attr("href",photo[_this_index]);
		the_photo.find('img').attr("src",photo[_this_index]).fadeIn();
	}
	_this_thump.find( 'a:first' ).find('img').addClass('thumb-current');
	
	//缩略图一些动画
	_this_thump_left.click(function(){
		thumb_left = 544+thumb_left;
		if ( thumb_left <= 0 ) {
			_this_thump.stop().animate({left:thumb_left},600);
		}else{
			thumb_left = -544+thumb_left;
		}
	});
	_this_thump_right.click(function(){
		thumb_left = -544+thumb_left;
		if ( -thumb_left < thumb_width ) {
			_this_thump.stop().animate({left:thumb_left},600);
		}else{
			thumb_left = 544+thumb_left;
		}
	});
	
	_this_thump_a.find('img').hover(function(){
		_this_thump.find('.thumb-current').stop().animate({opacity:1},200);
		$(this).stop().animate({opacity:0.6},200);
	},function(){
		$(this).stop().animate({opacity:1},200);
		_this_thump.find('.thumb-current').stop().animate({opacity:0.6},200);
	});
	
	_this_thump_wrap.hover(function(){
		_this_thump_left.css({'border-color':'rgba(0,0,0,0.3)','background-position':'-24px top'});
		_this_thump_right.css({'border-color':'rgba(0,0,0,0.3)','background-position':'left top'});
	},function(){
		_this_thump_left.css({'border-color':'rgba(0,0,0,0.1)','background-position':'left top'});
		_this_thump_right.css({'border-color':'rgba(0,0,0,0.1)','background-position':'right top'});
	});
	
	photo_wrap.hover(function(){
		$(this).find('.phwrap-corner').css({'border-color':'rgba(0,0,0,0.4)'});
	},function(){
		$(this).find('.phwrap-corner').css({'border-color':'rgba(0,0,0,0.1)'});
	});
	
	_this_thump_wrap.mousewheel(function(event) {
		if ( event.deltaY < 0 ){
			_this_thump_right.click();
		}else if ( event.deltaY > 0 ){
			_this_thump_left.click();
		}
		return false;
	});
	
}
function gallery_load(){
	var gallery_wrap = $('<div id=gallery-wrap/>'),
		gallery_the_img = $('<div id=gallery-the-img/>'),
		gallery_nav_l = $('<div class=gallery-nav-l/>'),
		gallery_nav_r = $('<div class=gallery-nav-r/>'),
		gallery_des = $('<p class=gallery-dex/>');
		gallery_img = new Image();
	
	gallery_img.setAttribute("ID","gallery-img");
	gallery_wrap.append(gallery_the_img).append(gallery_nav_l).append(gallery_nav_r);
	gallery_the_img.append(gallery_img).append(gallery_des);
	$( '#gallery-wrap' )[0] || $('body').append(gallery_wrap);
	
	//$(gallery_img).css({'width':gallery_wrap.width()*0.8,'height':gallery_wrap.height()*0.8});
	gallery_show();
	
	gallery_wrap.click(function(){
		gallery_quit();
	});
	
	if ( photo.length > 1 ){//下面keybind中继续排除掉Esc
		//上一张下一张
		gallery_nav_l.bind("click",function(e){
			gallery_switch("prev");
			e.stopPropagation();
		});
		gallery_nav_r.bind("click",function(e){
			gallery_switch("next");
			e.stopPropagation();
		});
		
		$(gallery_img ).click(function(e){
			if (  e.pageX>$(window).width()/2 ){
				gallery_switch("next");
			}else{
				gallery_switch("prev");
			}
			e.stopPropagation();
		});
	}
	gallery_keybind();
	gallery_des.click(function(e){
		e.stopPropagation();
	});
	
}
function gallery_show(){
	var new_img = new Image();
	var gallery_dex = $( '.gallery-dex' );
	//加载显示大图
	$( 'body' ).css({'overflow':'hidden'});
	$( '#gallery-wrap' ).show();
	$( '#gallery-wrap' ).addClass("loading");
	gallery_dex.html('');//查看大图清空
	
	//
	$( '.gallery-nav-l' ).removeClass("hide");
	$( '.gallery-nav-r' ).removeClass("hide");
	if ( gallery_index == 0 ){
		$( '.gallery-nav-l' ).addClass("hide");
	}
	if ( gallery_index == photo.length-1 ){
		$( '.gallery-nav-r' ).addClass("hide");
	}
	
	new_img.src=photo[gallery_index];
	new_img.onload=function(){
		var gallery_img = $( '#gallery-img' );
		$( '#gallery-wrap' ).removeClass("loading");
		gallery_img.attr("src",new_img.src).animate({opacity:1},200);
		gallery_dex.html('<a href="' + photo[gallery_index] + '" target="_blank">查看原图（' + new_img.width + '*' + new_img.height + '）</a>');
		gallery_pos($( '#gallery-wrap' ),gallery_img,new_img);
		$(window).resize(function(){//改变窗口调整大小
			gallery_pos($( '#gallery-wrap' ),gallery_img,new_img);
		});
	};	
}
function gallery_pos(w,img,n_img){
	var LH,//压缩宽度得到的高度
		LW,//压缩高度得到的宽度
		w_width = w.width(),
		w_height = w.height(),
		n_width = n_img.width,
		n_height = n_img.height;
	if ( n_width > w_width*0.8 ){
		LH = n_height/n_width*w_width*0.8;
	}else{
		LH = n_height;
	}
	if ( n_height > w_height*0.8 ){
		LW = n_width/n_height*w_height*0.8;
	}else{
		LW = n_width;
	}
	
	if( LH>=(w_height*0.8) ){//压缩高度
		img.css({
			'height':'auto',
			'width':LW,
			'margin-top':(w_height-w_height*0.8)/2
		});
		//alert('LH=' + LH);
	}else if( LW>=(w.width()*0.8) ){//压缩宽度
		img.css({
			'width':'auto',
			'height':LH,
			'margin-top':(w_height-LH)/2
		});
		//alert('LW=' + LW);
	}else{//显示原图
		img.css({
			'width':LW,
			'height':LH,
			'margin-top':(w_height-LH)/2
		});
	}
	
	/*img.animate({marginLeft:(w.width()-img.width())/2,marginTop:(w.height()-img.height())/2},240);*/
}
function gallery_quit(){
	$( '#gallery-wrap' ).hide().remove();
	$(document).unbind('keydown');
	$( 'body' ).css({'overflow':'auto'});
}
function gallery_switch(switch_text){
	$( '#gallery-img' ).stop().animate({opacity:0},0,function(){
		if ( switch_text == "prev" ) {
			if ( gallery_index > 0 ){
				gallery_index = gallery_index-1;
			}else{
				gallery_index = photo.length-1;
			}
		}else if ( switch_text == "next" ) {
			if( gallery_index<photo.length-1 ){
				gallery_index +=1;
			}else{
				gallery_index = 0;
			}
		}
		gallery_show(photo[gallery_index]);
	});
}
function gallery_keybind(){
	$(document).bind('keydown',function(e){
		e = e.which;
		if ( photo.length > 1 ){
			if ( e==37 || e==38 ){
				gallery_switch("prev");
				return false;
			};
			if ( e==39 || e==40 ){
				gallery_switch("next");
				return false;
			}
		}
		e == 27&&gallery_quit();
	});
	if ( photo.length > 1 ){
		$('#gallery-wrap').mousewheel(function(event) {
			//console.log(event.deltaX, event.deltaY, event.deltaFactor);
			if ( event.deltaY < 0 ){
				gallery_switch("next");
			}else if ( event.deltaY > 0 ){
				gallery_switch("prev");
			}
			return false;
		});
	}
	
}
	
$(document).ready(function(){
	gallery();
});	