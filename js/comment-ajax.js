$(document).ready(function(){
	/**
	 * WordPress jQuery-Ajax-Comments v1.3 by Willin Kan.
	 * 
	*/
	var edit_mode = '1', // 再編輯模式 ( '1'=開; '0'=不開 )
		txt1 = '<div id="loading">Sending...</div>',
		txt2 = '<div id="error">#</div>',
		txt3 = '">留言成功',
		edt1 = ', 刷新页面之前可以<a rel="nofollow" class="comment-reply-link" href="#edit" onclick=\'return addComment.moveForm("',
		edt2 = ')\'>重新编辑</a>',
		cancel_edit = '取消编辑',
		edit,
		re_edit,
		num = 1,
		comm_array=[],
		$comments = $('#comments-title'), // 評論數的 ID
		$cancel = $('#cancel-comment-reply-link'); cancel_text = $cancel.text(),
		$submit = $('#commentform #submit'); $submit.attr('disabled', false),
		$('#submit').after( txt1 + txt2 ); $('#loading').hide(); $('#error').hide(),
		$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
		comm_array.push(''); //没有的话重新编辑不显示内容
		
	/** submit */
	$('#commentform').submit(function() {
	
		$('#loading').show();
		$submit.attr('disabled', true).fadeTo('slow', 0.5);
		if ( edit ) $('#comment').after('<input type="text" name="edit_id" id="edit_id" value="' + edit + '" style="display:none;" />');
		
	/** Ajax */
		$.ajax({
			url: Readd.ajaxurl,
			data: $(this).serialize() + "&action=ajax_comment_post",
			type: $(this).attr('method'),
			error: function(request) {
				$('#loading').hide();
				$('#error').show().html(request.responseText);
				setTimeout(function() {$submit.attr('disabled', false).fadeTo('slow', 1); $('#error').hide();}, 1500);
			},
			success: function(data) {
				
				$('#loading').hide();
				comm_array.push($('#comment').val());
				$('textarea').each(function() {this.value = ''});
				var t = addComment, cancel = t.I('cancel-comment-reply-link'), temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId), post = t.I('comment_post_ID').value, parent = t.I('comment_parent').value;
				
				// comments
				if ( ! edit && $comments.length ) {
					n = parseInt($comments.text().match(/\d+/));
					$comments.text($comments.text().replace( n, n + 1 ));
					
				}
				// show comment
				new_htm = '"id="new_comm_' + num + '"></';
				new_htm = ( parent == '0' ) ? ('\n<div style="clear:both;" class="new-comment-list' + new_htm + 'div>') : ('\n<ul class="children' + new_htm + 'ul>');
				ok_htm = '\n <div class="ajaxtipsdiv"><span class="ajaxtips" id="success_' + num + txt3;
				if ( edit_mode == '1' ) {
					div_ = (document.body.innerHTML.indexOf('div-comment-') == -1) ? '' : ((document.body.innerHTML.indexOf('li-comment-') == -1) ? 'div-' : '');
					ok_htm = ok_htm.concat(edt1, div_, 'comment-', parent, '", "', parent, '", "respond", "', post, '", ', num, edt2);
				}
				ok_htm += '</span><span></span></div>\n';

				if( ( parent == '0' ) ){
					if ( !$( 'ol.comment-list' )[0] ) {
						$( '.comment-title' ).after('<ol class="comment-list"></ol>');//配合主题comments.php文件最初无评论列表时
					}
					$( 'ol.comment-list' ).append(new_htm);
				}else{
					$('#respond').before(new_htm);
				}
				$('#new_comm_' + num).hide().append(data);//插入新提交评论
				$('#new_comm_' + num + ' li').append(ok_htm);
				$('#new_comm_' + num).fadeIn(400);//新提交成功评论渐现

				//$body.animate( { scrollTop: $('#new_comm_' + num).offset().top - 200}, 900);
				countdown(); num++ ; edit = ''; $('*').remove('#edit_id');
				cancel.style.display = 'none';//“取消回复”消失
				cancel.onclick = null;
				t.I('comment_parent').value = '0';
				if ( temp && respond ) {
					temp.parentNode.insertBefore(respond, temp);
					temp.parentNode.removeChild(temp)
				}
				
				//Add by dong:recent-comments
				recent_comments_new(data);
			}
		}); // end Ajax
	  return false;
	}); // end submit
	/** comment-reply.dev.js */
	addComment = {
		moveForm : function(commId, parentId, respondId, postId, num) {
			var t = this, div, comm = t.I(commId), respond = t.I(respondId), cancel = t.I('cancel-comment-reply-link'), parent = t.I('comment_parent'), post = t.I('comment_post_ID');
			if ( edit ) exit_prev_edit();
			num ? (
				t.I('comment').value = comm_array[num],
				edit = t.I('new_comm_' + num).innerHTML.match(/(comment-)(\d+)/)[2],
				$new_sucs = $('#success_' + num ), $new_sucs.hide(),
				$new_comm = $('#new_comm_' + num ), $new_comm.hide(),
				$cancel.text(cancel_edit)
			) : $cancel.text(cancel_text);

			t.respondId = respondId;
			postId = postId || false;

			if ( !t.I('wp-temp-form-div') ) {
				div = document.createElement('div');
				div.id = 'wp-temp-form-div';
				div.style.display = 'none';
				respond.parentNode.insertBefore(div, respond)
			}

			!comm ? ( 
				temp = t.I('wp-temp-form-div'),
				t.I('comment_parent').value = '0',
				temp.parentNode.insertBefore(respond, temp),
				temp.parentNode.removeChild(temp)
			) : comm.parentNode.insertBefore(respond, comm.nextSibling);



			if ( post && postId ) post.value = postId;
			parent.value = parentId;
			cancel.style.display = '';

			cancel.onclick = function() {
				if ( edit ) exit_prev_edit();
				var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);

				t.I('comment_parent').value = '0';
				if ( temp && respond ) {
					temp.parentNode.insertBefore(respond, temp);
					temp.parentNode.removeChild(temp);
				}
				this.style.display = 'none';
				this.onclick = null;
				return false;
			};

			try { t.I('comment').focus(); }
			catch(e) {}

			return false;
		},

		I : function(e) {
			return document.getElementById(e);
		}
	}; // end addComment

	function exit_prev_edit() {
		$new_comm.show(); $new_sucs.show();
		$('textarea').each(function() {this.value = ''});
		edit = '';
	}

	var wait = 10, submit_val = $submit.val();
	function countdown() {
		if ( wait > 0 ) {
			$submit.val(wait); wait--; setTimeout(countdown, 1000);
		} else {
			$submit.val(submit_val).attr('disabled', false).fadeTo('slow', 1);
			wait = 10;
		}
	}
	
	//recent_comments新评论提交更新
	function recent_comments_new(data){
		if( !$( '.logged' )[0] ){
			var new_comment_id = $(data).find('.comment-body').attr("id").replace(/comment-(\d*)/, "$1");
			var new_comment_name = $(data).find('.url').text();
			$.ajax({
				url:Readd.ajaxurl+"?action=ajax_recent_new&commentId=" + new_comment_id,
				type:'GET',
				beforeSend:function(){
				
				},
				error:function(){
				
				},
				success:function(data){
					$( '.recent-comments-author' ).each(function(){//遍历，如果最近20个人里有，就隐藏
						if( $(this).text() == new_comment_name ){
							$(this).parent().parent().parent().parent().slideUp("slow");
							return false;
						}
					});
					$( '#recent-comments ul').prepend(data);
					$( '#recent-comments ul li:first' ).hide().slideDown("slow");
					var ho;
					//重载hover
					$( '#recent-comments li' ).hover(function(){
						var t = $(this);
						$( '#recent-comments' ).css({'opacity':'1'});
						ho = setTimeout(function(){
							t.find( '.recent-comments-meta' ).css({'opacity':0}).stop().show().animate({opacity:1},0);
						},50);
						
					},function(){
						clearTimeout(ho);
						$( '#recent-comments' ).css({'opacity':'0.8'});
						$(this).find( '.recent-comments-meta' ).hide();
					});
				}
			});
		}
	}
	
	//ajax评论翻页
	comment_page_ajax();
	function comment_page_ajax(){
		$('.comment-navi a').click(function(){
			var wpurl=$(this).attr("href").split(/(\?|&)action=AjaxCommentsPage.*$/)[0];
			var commentPage = 1;
			if (/comment-page-/i.test(wpurl)) {
				commentPage = wpurl.split(/comment-page-/i)[1].split(/(\/|#|&).*$/)[0];
			} else if (/cpage=/i.test(wpurl)) {
				commentPage = wpurl.split(/cpage=/)[1].split(/(\/|#|&).*$/)[0];
			};

			var loading='<div class="commnav_loding">正在努力读取中......</div>';
			$.ajax({
				url:Readd.ajaxurl + "?action=AjaxCommentsPage&post=" + Readd.postId + "&page=" + commentPage,
				type: 'GET',
				beforeSend: function() {
					$('.comment-list').empty().html(loading);
				},
				error: function(request) {
						alert(request.responseText);
					},
				success:function(data){
					var responses=data.split('<!--winysky-AJAX-COMMENT-PAGE-->');
					$('.comment-list').empty().html(responses[0]).hide().fadeIn('slow');
					$('.comment-navi').empty().html(responses[1]);
					comment_page_ajax();//自身重载一次
					comment_list();//重载评论列表相关
				}//返回评论列表顶部
			});
			
			return false;
		});
	}
	function comment_list(){
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
		
		//隐藏以前主题jQ添加的@
		$( '.children .text' ).find( 'a[rel=nofollow]' ).each(function(){
			var i = $(this).attr("href").match(/comment-/);
			var j = $(this).attr("href");
			if ( i!=null || j == '#undefined'){
				$(this).hide();
			}
		});
		
		$('.comment-list img').lazyload({effect : "fadeIn"});
		
		$( '.children .comment-body' ).hover(function(){
			$(this).find('.reply').show();
		},function(){
			$(this).find('.reply').hide();
		});
	}
})