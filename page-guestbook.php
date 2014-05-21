<?php
/*
  Template Name: GuestBook
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
							<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						</header>
						
						
							<div id="readerwall">
								<?php
									the_readerwall();
								?>
							</div>
						
						
					</article><!--post-->
					<?php comments_template(); ?>
				<?php endwhile; ?>
					
			</div><!--content-->
			
<?php get_footer(); ?>