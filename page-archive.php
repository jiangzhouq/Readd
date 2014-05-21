<?php
/*
  Template Name: Archive
 */
?>
<?php

get_header(); ?>
			</header><!--header-->
			<div id="content">
				
				<?php /* The loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="page-header">
							
						</header>
						
						<div class="entry-content">
							<?php
								the_archives();
							?>
						</div>
						
					</article><!--post-->
					
				<?php endwhile; ?>
					
			</div><!--content-->
			
<?php get_footer(); ?>