<?php
/*
  Template Name: Album
 */
?>
<?php
get_header(); ?>
			</header><!--header-->
			<div id="content">
				
				<?php /* The loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
						<div class="entry-content">
							<div class="album">
								<div class="thumb-wrap">
									<span class="thumb-left"></span>
									<div class="thumb">
										<?php the_content( __( '','Readd' ) ); ?>
									</div>
									<span class="thumb-right"></span>
								</div>
							</div>
						</div>
						
					</article><!--post-->
					
				<?php endwhile; ?>
					
			</div><!--content-->
			
<?php get_footer(); ?>