<?php

get_header(); ?>
			</header><!--header-->
			<div id="content">
				
				<?php /* The loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
					<?php comments_template(); ?>
				<?php endwhile; ?>
					
			</div><!--content-->
			
<?php get_footer(); ?>