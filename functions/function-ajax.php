<?php
/**
 * Ajax comments by Willin Kan.
 * mode by dong
 *
 */
function ajax_comment(){
/*if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) { //有这句会失效
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}*/

/** Sets up the WordPress Environment. */
	if($_POST['action'] == 'ajax_comment_post' && 'POST' == $_SERVER['REQUEST_METHOD']){
		global $wpdb;
		nocache_headers();
		
		$comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;
		
		$post = get_post($comment_post_ID);

		if ( empty($post->comment_status) ) {
			do_action('comment_id_not_found', $comment_post_ID);
			err(__('Invalid comment status.')); // 將 exit 改為錯誤提示
		}

		// get_post_status() will get the parent status for attachments.
		$status = get_post_status($post);

		$status_obj = get_post_status_object($status);

		if ( !comments_open($comment_post_ID) ) {
			do_action('comment_closed', $comment_post_ID);
			err(__('Sorry, comments are closed for this item.')); // 將 wp_die 改為錯誤提示
		} elseif ( 'trash' == $status ) {
			do_action('comment_on_trash', $comment_post_ID);
			err(__('Invalid comment status.')); // 將 exit 改為錯誤提示
		} elseif ( !$status_obj->public && !$status_obj->private ) {
			do_action('comment_on_draft', $comment_post_ID);
			err(__('Invalid comment status.')); // 將 exit 改為錯誤提示
		} elseif ( post_password_required($comment_post_ID) ) {
			do_action('comment_on_password_protected', $comment_post_ID);
			err(__('Password Protected')); // 將 exit 改為錯誤提示
		} else {
			do_action('pre_comment_on_post', $comment_post_ID);
		}

		$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
		$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
		$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
		$comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;
		$edit_id              = ( isset($_POST['edit_id']) ) ? $_POST['edit_id'] : null; // 提取 edit_id

		// If the user is logged in
		$user = wp_get_current_user();
		if ( $user->ID ) {
			if ( empty( $user->display_name ) )
				$user->display_name=$user->user_login;
			$comment_author       = $wpdb->escape($user->display_name);
			$comment_author_email = $wpdb->escape($user->user_email);
			$comment_author_url   = $wpdb->escape($user->user_url);
			if ( current_user_can('unfiltered_html') ) {
				if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
					kses_remove_filters(); // start with a clean slate
					kses_init_filters(); // set up the filters
				}
			}
		} else {
			if ( get_option('comment_registration') || 'private' == $status )
				err(__('Sorry, you must be logged in to post a comment.')); // 將 wp_die 改為錯誤提示
		}

		$comment_type = '';

		if ( get_option('require_name_email') && !$user->ID ) {
			if ( 6 > strlen($comment_author_email) || '' == $comment_author )
				err( __('Error: please fill the required fields.') ); // 將 wp_die 改為錯誤提示
			elseif ( !is_email($comment_author_email))
				err( __('Error: please enter a valid email address.') ); // 將 wp_die 改為錯誤提示
		}

		if ( '' == $comment_content )
			err( __('Error: please type a comment.') ); // 將 wp_die 改為錯誤提示



		// 增加: 檢查重覆評論功能
		$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
		if ( $comment_author_email ) $dupe .= "OR comment_author_email = '$comment_author_email' ";
		$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
		if ( $wpdb->get_var($dupe) ) {
			err(__('Duplicate comment detected; it looks as though you&#8217;ve already said that!'));
		}

		// 增加: 檢查評論太快功能
		if ( $lasttime = $wpdb->get_var( $wpdb->prepare("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_author = %s ORDER BY comment_date DESC LIMIT 1", $comment_author) ) ) { 
		$time_lastcomment = mysql2date('U', $lasttime, false);
		$time_newcomment  = mysql2date('U', current_time('mysql', 1), false);
		$flood_die = apply_filters('comment_flood_filter', false, $time_lastcomment, $time_newcomment);
		if ( $flood_die ) {
			err(__('You are posting comments too quickly.  Slow down.'));
			}
		}

		$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

		// 增加: 檢查評論是否正被編輯, 更新或新建評論
		if ( $edit_id ){
		$comment_id = $commentdata['comment_ID'] = $edit_id;
		wp_update_comment( $commentdata );
		} else {
		$comment_id = wp_new_comment( $commentdata );
		}

		$comment = get_comment($comment_id);
		do_action('set_comment_cookies', $comment, $user);

		//$location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id; //取消原有的刷新重定向
		//$location = apply_filters('comment_post_redirect', $location, $comment);

		//wp_redirect($location);

		$comment_depth = 1;   //为评论的 class 属性准备的
		$tmp_c = $comment;
		while($tmp_c->comment_parent != 0){
			$comment_depth++;
			$tmp_c = get_comment($tmp_c->comment_parent);
		}

		//此处非常必要，无此处下面的评论无法输出 by mufeng
		$GLOBALS['comment'] = $comment;

		//以下是評論式樣, 不含 "回覆". 要用你模板的式樣 copy 覆蓋.
		?>



		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body">
				<div class="author"><?php echo get_avatar($comment,'33'); ?></div>
				<span class="name"><?php printf( __( '%s' ), get_comment_author_link() ); ?></span>
				<span class="time"><?php echo time_ago(); ?></span>
				<div class="text">
					<?php
						if ($comment->comment_parent):
							$parent_id = $comment->comment_parent;
							$comment_parent = get_comment($parent_id);
					?>
					<span class="comment-to"><a href="<?php echo "#comment-".$parent_id; ?>" title="<?php echo mb_strimwidth(strip_tags(apply_filters( 'the_coment', $comment_parent->comment_content )), 0, 100, "..."); ?>">@<?php echo $comment_parent->comment_author; ?></a></span>
					<?php echo get_comment_text(); ?>
					<?php else: comment_text(); ?>
					<?php endif; ?>
				</div>
				<?php if ( $comment->comment_approved == '0'): ?>
					<em><span class="moderation"><?php _e('Your comment is avaiting moderation.'); ?></span></em>
				<?php endif; ?>
			</div>
		<?php
		die();
	}else{return;}
}
add_action('init','ajax_comment');

// 增加: 錯誤提示功能
function err($ErrMsg) {
    header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain;charset=UTF-8');
    echo $ErrMsg;
    exit;
}

//ajax评论翻页
function AjaxCommentsPage(){
	if( isset($_GET['action'])&& $_GET['action'] == 'AjaxCommentsPage'  ){
		global $post,$wp_query, $wp_rewrite;
		$postid = isset($_GET['post']) ? $_GET['post'] : null;
		$pageid = isset($_GET['page']) ? $_GET['page'] : null;
		if(!$postid || !$pageid){
			fail(__('Error post id or comment page id.'));
		}
		// get comments
		$comments = get_comments('post_id='.$postid);
		$post = get_post($postid);
		if(!$comments){
			fail(__('Error! can\'t find the comments'));
		}
		//if( 'desc' != get_option('comment_order') ){
		//	$comments = array_reverse($comments);
		//}
		$comments = array_reverse($comments);//?有点不明白
		// set as singular (is_single || is_page || is_attachment)
		$wp_query->is_singular = true;
		// base url of page links
		$baseLink = '';
		if ($wp_rewrite->using_permalinks()) {
			$baseLink = '&base=' . user_trailingslashit(get_permalink($postid) . '/comment-page-%#%', 'commentpaged');
		}
		
		wp_list_comments('callback=commentlist&type=comment&page=' . $pageid . '&per_page=' . get_option('comments_per_page'), $comments);
		echo '<!--winysky-AJAX-COMMENT-PAGE-->';
		echo '<span id="cp_post_id" style="display:none;">
			'.$post->ID.'
		</span>';
		paginate_comments_links(array('current' => $pageid . $baseLink, 'prev_text' => '<', 'next_text' => '>') );
		die;
	}
}
add_action('init', 'AjaxCommentsPage');

/*
 *
 *sidebar recent_comments by dong
 *
 */
function recent_comments(){
	if ( isset( $_GET['action'] ) && $_GET['action']=='ajax_recent_comments_load' ){//初始载入
		$my_email = get_bloginfo( 'admin_email' );
		$rc_comms = get_comments( array('status' => 'approve', 'number' => '400') );
		$show_count = 20;
		$rc_wall = array();
		
		foreach( $rc_comms as $rc_comm ){//获取最近评论的20个人放到数组里
			$id = $rc_comm -> comment_ID;
			$name = $rc_comm ->comment_author;
			$email = $rc_comm -> comment_author_email;
			$url = $rc_comm -> comment_author_url;
			$content = $rc_comm -> comment_content;
			$date = $rc_comm -> comment_date;
			if ( $rc_comm->comment_author_email != $my_email ){
				$index = -1;
				foreach( $rc_wall as $key => $value ){
					if ( $email == $value["email"] ){
						$index = $key;
						break;
					}
				}
				if ( $index > -1 ){
					continue;
				}else{
					array_push($rc_wall,array("id" => $id, "name" => $name, "email" => $email, "url" => $url, "content" => $content, "date" => $date));
				}
				if ( $show_count <=0 ) break;
				$show_count--;
				
			}
			
		}
		
		$output = '
		<div id="recent-comments">
			<span class="recent-comments-top"></span>
		<ul>';
		
		foreach( $rc_wall as $key => $value ){//显示最近20个人的信息
			$avatar = get_avatar($value["email"],$size='30');
			$output .= "
				<li>
					<a href='" . esc_url( get_comment_link($value["id"]) ). "' class='recent-comments-avatar'>" .$avatar. "</a>
					<div class='recent-comments-meta'><div class='background-layer'>
						<a href='". esc_url( get_comment_link($value["id"]) ).  "' class='recent-comments-avatar'>" .$avatar. "</a>
						<a href='".$value["url"]."' target='_blank'><span class='recent-comments-author'>" .$value["name"]. "</span></a>
						<p class='recent-comments-content'>" .mb_strimwidth(strip_tags($value["content"]),0,50,''). "</p>
						<div class='recent-comments-time'><span>".$value["date"]."</span><a href='". esc_url( get_comment_link($value["id"]) ). "' class='reply'>回复</a></div>
						<div class='recent-jiao'></div>
					</div>
			</li>
			";
		}
		
		$output .='
		</ul>
			<span class="recent-comments-bottom"></span>
		</div>';
		echo $output;
		die;
	}if ( isset( $_GET['action'] ) && $_GET['action']=='ajax_recent_new' ){//提交新评论
		$new_commentID = isset($_GET['commentId']) ? $_GET['commentId'] : null;
		$new_comment = get_comment( $new_commentID );
		$new_recent_li = '';
		$avatar = get_avatar($new_comment,$size='30');
		$new_recent_li .= "
		<li>
			<a href='" . esc_url( get_comment_link($new_comment->comment_ID) ). "' class='recent-comments-avatar'>" .$avatar. "</a>
			<div class='recent-comments-meta'><div class='background-layer'>
				<a href='". esc_url( get_comment_link($new_comment->comment_ID) ).  "' class='recent-comments-avatar'>" .$avatar. "</a>
				<a href='".$new_comment->comment_author_url."' target='_blank'><span class='recent-comments-author'>" .$new_comment->comment_author. "</span></a>
				<p class='recent-comments-content'>" .mb_strimwidth(strip_tags($new_comment->comment_content),0,50,''). "</p>
				<div class='recent-comments-time'><span>".$new_comment->comment_date."</span><a href='". esc_url( get_comment_link($new_comment->comment_ID) ). "' class='reply'>回复</a></div>
				<div class='recent-jiao'></div>
			</div>
		</li>
		";
		echo $new_recent_li;
		die;
	}else{
		return;
	}
}
add_action('init','recent_comments');

/*
 *
 *slider by dong
 *
 */
function the_slider(){
	if ( isset( $_GET['action'] ) && $_GET['action']=='ajax_slider_bg' ){
		$options = get_option('Readd_options');
		if ( $options['bg1'] ){
			$bg_src = $options['bg1'];
		}
		if ( $options['bg2'] ){
			$bg_src .= '<!--bg_src-->'.$options['bg2'];
		}
		if ( $options['bg3'] ){
			$bg_src .= '<!--bg_src-->'.$options['bg3'];
		}
		if ( $options['bg4'] ){
			$bg_src .= '<!--bg_src-->'.$options['bg4'];
		}
		if ( $options['bg5'] ){
			$bg_src .= '<!--bg_src-->'.$options['bg5'];
		}
		if ( $options['bg6'] ){
			$bg_src .= '<!--bg_src-->'.$options['bg6'];
		}
		echo $bg_src;
		die;
	}else if ( isset( $_GET['action'] ) && $_GET['action']=='ajax_slider_post' ){
		$post_length = $_GET['post_length'];
		$the_query = new WP_Query( array('posts_per_page' => $post_length, 'orderby' => 'rand') );
		$output = '';
		if ( $the_query->have_posts() ){
			while ( $the_query->have_posts() ){
				$the_query->the_post();
				$output .= '<div class="slider-title"><a href="' .get_permalink(). '">' .get_the_title(). '</a></div>';
				$output .= '<div class="slider-content">' .mb_strimwidth(strip_tags(apply_filters('the_content',get_post()->post_content)),0,200). '...</div>';
				$output .= '<!--slider_post-->';
			}
		}
		wp_reset_postdata();
		echo $output;
		die;
	}else{return;}
}
add_action('init','the_slider');

/*
 *
 *site_bg by dong
 *
 */
function site_bg(){
	if ( isset( $_GET['action'] ) && $_GET['action']=='ajax_site_bg' ){
		$option = get_option('Readd_options');
		if ( $option['site_bg'] ){
			$bg_src = $option['site_bg'];
		}else{
			$bg_src = "";
		}
		echo $bg_src;
		die;
	}else{
		return;
	}
}
add_action('init',site_bg());

?>