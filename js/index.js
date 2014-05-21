$(document).ready(function(){
	
	$( '#content img' ).lazyload({effect:"fadeIn"});
	
	//comment
	respond();
	function respond(){
		var hover_name = $('<div id=hover-name/>'),
		this_name;
		$('body').append(hover_name);
		$( '.children .author .avatar' ).mousemove(function(e){//鼠标放在children头像上显示名称
			this_name="";
			this_name = $(this).parent().parent().find('.name').text();
			hover_name.html(this_name);
			hover_name.css({'left':e.pageX+20,'top':e.pageY+10}).fadeIn('slow');
		});
		$( '.children .author .avatar' ).mouseout(function(e){
			hover_name.empty().css({'left':'0','top':'0'}).hide();
		});
		
		
		$( '.children .comment-body' ).hover(function(){
			$(this).find('.reply').show();
		},function(){
			$(this).find('.reply').hide();
		});
		
		$( '#cancel-comment-reply-link' ).click(function(){
			$('#comment').val('');
		});
		
		//隐藏以前主题jQ添加的@
		$( '.children .text' ).find( 'a[rel=nofollow]' ).each(function(){
			var i = $(this).attr("href").match(/comment-/);
			var j = $(this).attr("href");
			if ( i!=null || j == '#undefined'){
				$(this).hide();
			}
		});
		
		if ( $( '.welcome' )[0] ){
			$( '.author-info' ).hide();
			$( 'span.info-edit' ).click(function(){
				$( '.author-info' ).toggle();
			});
		}
		
		$( '#respond input[type=text]' ).focus(function(){
			$(this).css({'color':'rgba(0,0,0,0.6)','border-color':'rgba(0,0,0,0.3)'});
		});
		$( '#respond input[type=text]' ).blur(function(){
			$(this).css({'color':'rgba(0,0,0,0.3)','border-color':'rgba(0,0,0,0.1)'});
		});
		$( '#respond textarea' ).focus(function(){
			$(this).css({'color':'rgba(0,0,0,0.6)'});
			$(this).parent().css({'border-color':'rgba(0,0,0,0.3)'});
		});
		$( '#respond textarea' ).blur(function(){
			$(this).css({'color':'rgba(0,0,0,0.3)'});
			$(this).parent().css({'border-color':'rgba(0,0,0,0.1)'});
		});
	}
	
	//share
	var share_time;
	$( '.share' ).hover(function(){
		clearTimeout(share_time);
		$( '.share ul' ).slideDown('slow');
	},function(){
		share_time=setTimeout(function(){$( '.share ul' ).slideUp('slow');},300);
	});
	
	
	
	//page-archive
	archive();
	function archive(){
		if ( !document.getElementById("archives") ) return false;
		var year = $( '.year:first' ).attr("id").replace("year-", "");
		var old_top = $( '#archives' ).offset().top;
		$( '.year:first, .month:first' ).addClass('selected');
		$( '.year:first' ).parent().addClass('current-year');
		
		$( '.month' ).click(function(){
			var id = "#" + $(this).attr("id").replace("m", "archive");
			var top = $(id).offset().top-40;
			$( '.month.selected' ).removeClass('selected');
			$(this).addClass('selected');
			$( 'body,html' ).scrollTop(top);
		});
		
		$('.year').click(function(){
			if ( !$(this).next().hasClass('selected')){
				$( '.year.selected' ).removeClass('selected');
				$( '.current-year' ).removeClass('current-year');
				$(this).parent().addClass('current-year');
				$(this).addClass('selected');
			}
			$(this).next().click();
		});
		
		$(window).scroll(function(){
			var top = $(this).scrollTop();
			if ( top >= old_top-40 ){
				$( '.archive-nav' ).css({top:40});
			}else{
				$( '.archive-nav' ).css({top:old_top - top});
			}
			$( '.archive-content' ).each(function(){
				var thistop = $(this).offset().top-40,
				thisbottom = thistop + $(this).height();
				var newyear = $(this).attr("id").replace(/archive-(\d*)-\d*/, "$1");
				if ( top >= thistop && top <= thisbottom){
					if ( newyear != year ){
						$( '#year-' + year ).parent().removeClass('current-year');
						$( '#year-' + newyear ).parent().addClass('current-year');
						$( '.year.selected' ).removeClass('selected');
						$( '#year-' + newyear ).addClass('selected');
						year = newyear;
					}
					var id = "#" + $(this).attr("id").replace("archive", "m");
					$( '.month.selected' ).removeClass('selected');
					$(id).addClass('selected');
				}
			});
		});
	}
	
	//jPlayer
	function audio_init(){
		//播放器界面初始化
		$( '#jquery_jplayer' ).jPlayer('destroy');//销毁播放器
		$(' .seek-bar' ).removeClass('jp-seek-bar');
		$(' .play-bar' ).removeClass('jp-play-bar');
		$(' .current-time' ).removeClass('jp-current-time');
		$( '.current-time' ).html('00:00');
		$( '.stop' ).hide();
		$( '.play' ).show();
	}
	//载入jPlayer
	function audio_load(mp3_url){
		$( '#jquery_jplayer' ).jPlayer({
	        swfPath: "http://jplayer.org/latest/js",
	        supplied: "mp3"
	    });
		$( '#jquery_jplayer' ).jPlayer("setMedia",{
			mp3:mp3_url
		});
		$( '#jquery_jplayer' ).jPlayer('play');
		$( '#jquery_jplayer' ).bind(jQuery.jPlayer.event.ended, function(event){
			audio_init();
		});
	}
	$( '.play' ).click(function(){
		
		audio_init();
		
		var _this = $(this);
		_this.hide();
		_this.parent().find( '.stop' ).show();
		
		//jPlayer hook
		_this.parent().find( '.seek-bar' ).addClass('jp-seek-bar');
		_this.parent().find( '.play-bar' ).addClass('jp-play-bar');
		_this.parent().find( '.current-time' ).addClass('jp-current-time');
		
		//do it
		audio_load(_this.attr('rel'));
		
	});
	$( '.stop' ).click(function(){
		$(this).hide();
		$(this).parent().find( '.play' ).show();
		
		$( '#jquery_jplayer' ).jPlayer('stop');
		
		audio_init();
	});
	$( '.auto' ).each(function(){
		if($(this).attr('rel') == 1){
			$(this).parent().find('.play').click();
		}
	});
	
})