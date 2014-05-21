<?php

get_header(); ?>
			
			</header><!--header-->
			<div id="content">
				<header class="search-header">
					<h1><?php printf( __( 'Search: %s', 'Readd' ), get_search_query() ); ?></h1>
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