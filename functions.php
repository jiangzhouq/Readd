<?php

register_nav_menu( 'primary', __( '导航菜单', 'Readd' ) );
register_nav_menu( 'second', __( '分类菜单', 'Readd' ) );

if (is_admin() ){
	get_template_part( 'functions/function-setting' );
}else{
	get_template_part( 'functions/function-meta' );
	get_template_part( 'functions/function-ajax' );
}
function Readd_wp_title( $title, $sep ) {
	global $paged, $page;
	
	if ( is_feed() )
		return $title;
	
	//Add the site name.
	$title .= get_bloginfo( 'name' );
	
	//Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";
		
	//Add a page number of necessary.
	if ( $paged >=2 || $page >=2 )
		$title = "$title $sep" . sprintf( __( 'Page %s', 'Readd' ), max( $paged, $page ) );
	
	return $title;
}
add_filter( 'wp_title', 'Readd_wp_title', 10, 2 );

function Readd_scripts_styles() {
	
	wp_enqueue_script( 'jquery1.10.2', get_template_directory_uri() . '/js/jquery-1.10.2.min.js', array(), '1.10.2', true);
	wp_enqueue_script( 'lazyload' , get_template_directory_uri() . '/js/jquery.lazyload-1.5.0.js', array(), '1.5.0', true);
	wp_enqueue_script( 'jplayer' , get_template_directory_uri() . '/js/jquery.jplayer.min.js', array(), '2.5.0', true);
	wp_enqueue_script( 'mousewheel' , get_template_directory_uri() . '/js/jquery.mousewheel.js', array(), '3.1.11', true);
	wp_enqueue_script( 'Readd-script' , get_template_directory_uri() . '/js/index.js', array(), '1.3', true);
	if ( !wp_is_mobile() ){
		wp_enqueue_style( 'Readd-style', get_template_directory_uri() . '/style.css', array(), '1.4', 'screen' );
		wp_enqueue_script( 'Readd-slider' , get_template_directory_uri() . '/js/slider.js', array(), '1.0', true);
		wp_enqueue_script( 'recent-comments' , get_template_directory_uri() . '/js/recent-comments.js', array(), '1.1', true);
		wp_enqueue_script( 'Gallery' , get_template_directory_uri() . '/js/gallery.js', array(), '1.0', true);
		wp_enqueue_script( 'bg' , get_template_directory_uri() . '/js/bg.js', array(), '1.0', true);
	}else{
		wp_enqueue_style( 'mobile-style', get_template_directory_uri() . '/css/mobile.css', array(), '1.3', 'screen' );
	}
	$ajaxurl = home_url("/");
	wp_localize_script( 'Readd-script', 'Readd' ,array("ajaxurl" => $ajaxurl));
	if ( is_singular() && comments_open() ){
		global $post;
		$postid = $post->ID;
		wp_localize_script( 'Readd-script', 'Readd' ,array("postId" => $postid,"ajaxurl" => $ajaxurl));
		
		wp_enqueue_script( 'comment-ajax', get_template_directory_uri(). '/js/comment-ajax.js', array(), '1.1', true);
	}
	
}
add_action( 'wp_enqueue_scripts', 'Readd_scripts_styles' );

//mp3 player
function mp3link($atts, $content=null){
	extract(shortcode_atts(array("auto"=>'0',"replay"=>'0',),$atts));
	return '
	<div id="jp_container" class="jp-audio">
		<span rel="' .$content. '" class="play-switch play"  title="play"></span>
		<span class="play-switch stop"  title="stop"></span>
		<span rel=" '.$auto.' "class="auto" ></span>
		<div class="seek-bar">
			<div class="play-bar"></div>
		</div>
		<span class="current-time">00:00</span>
	</div>';
}
add_shortcode('mp3','mp3link');

//文章分页
 function pagenavi($range = 7){
	global $paged, $wp_query;
	if ( !$max_page ) {$max_page = $wp_query->max_num_pages;}
	if($max_page > 1){
		if(!$paged){$paged = 1;}
		echo "<div id='page-navi'>";
		if($paged > 1) { echo "<a href='" . get_pagenum_link(1) . "'title='跳转到首页' class='first'><</a>"; }
		if($max_page > $range){
			if($paged <= $range){
				for($i = 1; $i <= ($range + 1); $i++){
					if($i==$paged) echo "<span class='current'>$i</span>";
					else echo"<a href='" . get_pagenum_link($i) ."'>$i</a>";
				}
			}
			elseif($paged > $range && $paged < ($max_page - $range)){
				for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
					if($i==$paged) echo "<span class='current'>$i</span>";
					else echo"<a href='" . get_pagenum_link($i) ."'>$i</a>";
				}
			}
			elseif($paged >= ($max_page - $range)){
				for($i = $max_page - $range; $i <= $max_page; $i++){
					if($i==$paged) echo "<span class='current'>$i</span>";
					else echo"<a href='" . get_pagenum_link($i) ."'>$i</a>";
				}
			}
		}
		else{
			for($i = 1; $i <= $max_page; $i++){
				if($i==$paged) echo "<span class='current'>$i</span>";
				else echo"<a href='" . get_pagenum_link($i) ."'>$i</a>";
			}
		}
		echo "<a href='" . get_pagenum_link($max_page) . "'title='跳转到最后一页' class='last'>></a></div>";
	}
}

//评论列表
function commentlist( $comment, $args, $depth){
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-body">
			<div class="author"><?php echo get_avatar($comment,'33'); ?></div>
			<span class="name"><?php printf( __( '%s' ), get_comment_author_link() ); ?></span>
			<span class="time"><?php echo time_ago(); ?></span>
			<div class="reply"><?php comment_reply_link(array_merge( $args, array( 'reply_text' => '回复', 'depth' =>$depth, 'max_depth' =>$args['max_depth'] ) ) ); ?></div>
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
}

//邮件回复
function comment_mail_notify($comment_id) {
	$admin_notify = '1';
	$admin_email = get_bloginfo ('admin_email');
	$comment = get_comment($comment_id);
	$comment_author_email = trim($comment->comment_author_email);
	$parent_id = $comment->comment_parent ? $comment->comment_parent : '';
	global $wpdb;
	if ($wpdb->query("Describe {$wpdb->comments} comment_mail_notify") == '')
		$wpdb->query("ALTER TABLE {$wpdb->comments} ADD COLUMN comment_mail_notify TINYINT NOT NULL DEFAULT 0;");
	if (($comment_author_email != $admin_email && isset($_POST['comment_mail_notify'])) || ($comment_author_email == $admin_email && $admin_notify == '1'))
		$wpdb->query("UPDATE {$wpdb->comments} SET comment_mail_notify='1' WHERE comment_ID='$comment_id'");
	$notify = $parent_id ? '1' : '0';
	$spam_confirmed = $comment->comment_approved;
	if ($parent_id != '' && $spam_confirmed != 'spam' && $notify == '1') {
		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		$to = trim(get_comment($parent_id)->comment_author_email);
		$subject = '你在' . get_option("blogname") . '的留言有了回复';
		$message = '
		<div id="mailtou" style="width:39em;max-width:90%;height:auto;margin-top:10px;margin-bottom:48px;margin-left:auto;margin-right:auto;padding:40px;border:1px solid #ededed;font-size:13px;line-height:14px;">
			<p class="mail_title" style="font-size:15px;color:#5e84d7;margin-bottom:30px;">你在&#8968; '. get_the_title($comment->comment_post_ID) .' &#8971;留言：</p>
			<p style="border:1px solid #EEE;overflow:auto;padding:10px;margin:1em 0;"><span style="color:#5e84d7;">'. trim(get_comment($parent_id)->comment_author) .'</span>:'. trim(get_comment($parent_id)->comment_content) .'</p>
			<p style="border:1px solid #EEE;overflow:auto;padding:10px;margin:1em 0 1em 60px;"><span style="color:#5e84d7;">'. trim($comment->comment_author) .'</span>:'. trim($comment->comment_content) .'</p>
			<p style="margin-bottom:10px">点击<a href="' . htmlspecialchars(get_comment_link($parent_id)) . '" style="color:#5e84d7;text-decoration:none;outline:none;">查看完整内容</a></p>
			<p style="margin-bottom:10px">(此邮件由系统发出,无需回复.)</p>
		</div>';
		$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
		$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
		wp_mail( $to, $subject, $message, $headers );
	}
}
add_action('comment_post', 'comment_mail_notify');

//评论作者新标签打开
function hu_popuplinks($text) {
	$text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank'>", $text);
	return $text;
}
add_filter('get_comment_author_link', 'hu_popuplinks', 6);

//冒充评论检验
function CheckEmailAndName(){
	global $wpdb;
	$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
	$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
	if(!$comment_author || !$comment_author_email){
		return;
	}
	$result_set = $wpdb->get_results("SELECT display_name, user_email FROM $wpdb->users WHERE display_name = '" . $comment_author . "' OR user_email = '" . $comment_author_email . "'");
	if ($result_set) {
		if ($result_set[0]->display_name == $comment_author){
			err(__('警告: 您不能使用博主的昵称！'));
		}else{
			err(__('警告: 您不能使用博主的邮箱！'));
		}
		fail($errorMessage);
	}
}
add_action('pre_comment_on_post', 'CheckEmailAndName');

//评论时间
function time_ago( $type = 'commennt', $day = 30 ) {
	$d = $type == 'post' ? 'get_post_time' : 'get_comment_time';
	$timediff = time() - $d('U');
	if ($timediff <= 60*60*24*$day){
		echo  human_time_diff($d('U'), strtotime(current_time('mysql', 0))), '前';
	}
	if ($timediff > 60*60*24*$day){
		echo  date('Y/m/d',get_comment_date('U'));
	};
}

/*
 *
 *缓存版读者墙
 *
 */
function the_readerwall($limit_number=16){
	if( $mostactive = get_option( 'mostactive' ) ){
		echo $mostactive;
	}else{
		$comments = get_comments( array('status' => 'approve') );//获取评论
		$my_email = get_option( 'admin_email' );
		$mostactive = array();
		$tmp = array();
		$comment_date = array();
		
		foreach( $comments as $comment ){//计算每个人评论数
			$author = $comment -> comment_author;
			$email = $comment -> comment_author_email;
			$url = $comment -> comment_author_url;
			$date = $comment -> comment_date;
			$comment_year = explode("-" ,$date);
			if ( $comment_year[0] < date("Y") ){//今年的评论
				break;
			}
			if( $email != $my_email ){
				$i = -1;
				$index = -1;
				foreach( $mostactive as $item => $comm ){
					if( $email == $comm["email"] ){
						$index = $item;
						break;
					}
				}
				if( $index > -1 ){
					$mostactive[$index]["number"] += 1;
				}else{
					array_push($mostactive,array( "author" => $author, "email" => $email, "url" => $url, "date" => $date, "number" => 1 ));
				}
			}
		}
		
		//数组按评论数逆序排序
		foreach( $mostactive as $item => $comm){
			$tmp[$item] = $comm['number'];
			$comment_date[$item] = $comm['date'];
		}
		array_multisort( $tmp, SORT_DESC, $comment_date, SORT_ASC, $mostactive );//评论数相同时按照最后评论时间升序排序
		
		if( empty($mostactive) ){
			$output = '<ul><li>none data.</li></ul>';
		}else{
			$output = '<ul>';
			foreach( $mostactive as $item => $comm){
				$avatar = get_avatar($comm["email"], 90);
				$author = $comm["author"];
				$url = $comm["url"];
				$number = $comm["number"];
				$output.='<li>' . '<a href="'. $url . '" target="_blank" title="今年'. $number .'条评论">' . $avatar .'<span class="wall_name">'.$author.'</span></a></li>';
				$limit_number--;
				if ( $limit_number <= 0 ){
					break;
				}
			}
			$output .= '</ul>';
		}
		echo $output;
		update_option('mostactive', $output);
	}
}
function clear_mostactive(){
	delete_option('mostactive');
}
add_action('comment_post', 'clear_mostactive');
add_action('edit_comment', 'clear_mostactive');

//文章归档
function the_archives(){
	$the_query = new WP_Query( array( 'posts_per_page' => -1, 'ignore_sticky_posts ' => 1) );
	$year = 0;
	$month = 0;
	$day = 0;
	$date = array();
	echo '<div id="archives">';
	//The Loop
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$year_temp = get_the_time('Y');
			$month_temp = get_the_time('n');
			if ( $month != $month_temp && $month > 0 ) echo '</ul></div>';
			if ( $year != $year_temp ) {
				$year = $year_temp;
				$date[$year] =array();
			}
			if ( $month != $month_temp) {
				$month = $month_temp;
				array_push( $date[$year], $month );
				echo '<div class="archive-content" id="archive-'.$year.'-'.$month.'"><h3 class="archive-month">' .get_the_time('Y年 m月'). '</h3><ul>';
			}
			echo '<li><span>' .get_the_time("d日"). '</span><a href="' .get_permalink(). '" target="_blank">' .get_the_title(). '</a><span class="msg">&#40;' ;if(function_exists('the_views')){the_views();} echo'&#41;</span></li>';
		}
	}else{
		echo '<div>none data.</div>';
	}
	wp_reset_postdata();
	echo '</ul></div></div>';
	
	//echo date-nav
	$output = '<div id="archive-nav"><div  class="archive-nav"><span>Map</span><ul>';
	$year_now = date("Y");
	foreach( $date as $key => $value ){
		$output .='<li class="one-year" id="'.$key.'"><ul><li class="year" id="year-'.$key.'">' .$key. '年</li>';
		foreach( $value as $item => $m ){
			$output .='<li class="month" id="m-'.$key.'-'.$m.'">' .$m. '月</li>';
		}
		$output .='</ul></li>';
	}
	$output .= '</ul></div></div>';
	?>
	
	<?php echo $output; ?>
	<?php
}

?>