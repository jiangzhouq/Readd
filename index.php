<?php

get_header(); ?>
				<?php $options = get_option('Readd_options');?>
				<div id="picture">
					<?php 
					if ( $options['face'] ){
						echo '<img src="' .$options['face']. '" />';
					}else{
						echo '<img src="' .get_bloginfo('template_url'). '/images/picture.jpg" />';
					}
					?>
				</div>
				<nav id="cate-nav">
					<?php wp_nav_menu( array( 'theme_location' => 'second', 'menu_class' => 'nav-menu' ) ); ?>
				</nav>
			</header><!--header-->
			
			<?php 
			if ( $options['news'] ){
				echo '<div id="news">';
				echo $options['news'] ;
				echo '</div>';
			} ?><!--news-->
			
			<div id="content">
				<?php if ( have_posts() ) : ?>
				
					<?php /* The loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					<?php endwhile; ?>
					
					<?php pagenavi(); ?>
					
				<?php endif; ?>
			</div><!--content-->
			
<?php get_footer(); ?>