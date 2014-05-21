<?php
if ( post_password_required() )
	return;
?>

<div id="comments">

	<?php if ( have_comments() ) : ?>
		<div class="comment-title">
			<span><?php echo get_comments_number(); ?> COMMENTS</span>
		</div>
		
		<ol class="comment-list">
			<?php wp_list_comments('type=comment&callback=commentlist'); ?>
		</ol>
		
		<div class="comment-navi">
			<span id="cp_post_id" style="display:none;">
			</span>
			<?php paginate_comments_links(array('prev_text' => '<', 'next_text' => '>')); ?>
		</div>
	<?php endif; ?><!--comentlist-->
	
	<?php if ( comments_open() ): ?>
		<div class="comment-title">
			<span>LEAVE A REPLY</span>
		</div>
		<div id="respond">
			
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<span class="cancel_comment_reply">
					<?php cancel_comment_reply_link('取消回复'); ?>
				</span>
				<?php if($user_ID): ?>
					<div class="logged">已登录<a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>&nbsp;，&nbsp;<a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">注销？</a></div>
				<?php else: ?>
					
					<?php if($comment_author): ?>
						<div class="welcome">欢迎回来，<?php echo $comment_author; ?>！<span class="info-edit">修改</span></div>
					<?php endif; ?>
					
					<div class="author-info">
						<div>
							<label>名字：</label>
							<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" <?php if($req) echo "aria-required='true'"; ?> />
							
						</div>
						<div>
							<label>邮箱：</label>
							<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" <?php if($req) echo "aria-required='true'"; ?> />
							
						</div>
						<div>
							<label>网站：</label>
							<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="3" />
							
						</div>
					</div>
				<?php endif; ?>
				<div class="comment-textarea">
					<textarea name="comment" id="comment" tabindex="4" onkeydown="if(event.ctrlKey&&event.keyCode==13){document.getElementById('submit').click();return false};"></textarea>
				</div>
				<div>
					<input name="submit" type="submit" id="submit" tabindex="5" value="发布" />
					<?php comment_id_fields(); ?>
				</div>
				<?php do_action('comment_form',$post->ID); ?>
			</form>
		</div>
	<?php endif; ?>
</div>