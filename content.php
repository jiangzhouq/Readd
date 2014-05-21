<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	</header>
	
	<div class="entry-content">
		<?php the_content( __( '','Readd' ) ); ?>
	</div>
	
	<footer>
		<ul class="entry-meta">
			<li class="meta-date"><?php the_time('Y-m-d'); ?></li>
			<?php if(get_the_tags()): ?><li class="meta-tag"><?php the_tags('','&nbsp;,&nbsp;',''); ?></li><?php endif; ?>
			
			<?php 
				if ( !is_singular() ){
					echo '<li class="meta-comm">';
					if(comments_open() ){
						comments_popup_link('0 COMMENTS','1 COMMENTS','% COMMENTS');
					}else{
					echo '评论关闭';
					}
					echo '</li>';
				}else{
					echo '<li class="meta-author">文 / '.get_the_author().'</li>';
				}
			?>
			
		</ul>
		<?php if( is_singular() ): ?>
		<div class="share">
			<ul class="share-ul">
				<li><a href="http://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" target="_blank" rel="nofollow" class="twitter-share" title="Twitter"></a></li>
				<li><a href="http://facebook.com/share.php?u=<?php the_permalink(); ?>&t=<?php the_title(); ?>" target="_blank" rel="nofollow" class="facebook-share" title="facebook"></a></li>
				<li><a href="http://v.t.sina.com.cn/share/share.php?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank" rel="nofollow" class="sina-share" title="新浪微博"></a></li>
				<li><a href="http://v.t.qq.com/share/share.php?title=<?php the_title(); ?>&url=<?php the_permalink(); ?>&site=<?php bloginfo('url');?>" target="_blank" rel="nofollow" class="tencent-share" title="腾讯微博"></a></li>
				<li><a href="http://www.douban.com/recommend/?url=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank" rel="nofollow" class="douban-share" title="豆瓣网"></a></li>
				<li><a href="http://fanfou.com/sharer?u=<?php the_permalink(); ?>&t=<?php the_title(); ?>" target="_blank" rel="nofollow" class="fanfou-share" title="饭否网"></a></li>
				<li><a href="http://share.renren.com/share/buttonshare?link=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank" rel="nofollow" class="renren-share" title="人人网"></a></li>
			</ul>
			<span class="share-c">分享到</span>
		</div>
		<?php endif; ?>
	</footer>
	<div class="clear"></div>
</article><!--post-->