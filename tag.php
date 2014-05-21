<?php

get_header(); ?>
			
			</header><!--header-->
			<div id="content">
				<header class="tag-header">
					<h1><?php printf( __( 'Tag: %s', 'Readd' ), single_tag_title( '', false ) ); ?></h1>
				</header>
				<?php if ( have_posts() ) : ?>
				
					<?php /* The loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					<?php endwhile; ?>
					
					<?php pagenavi(); ?>
					
				<?php endif; ?>
			</div><!--content-->
			
<?php get_footer(); ?>