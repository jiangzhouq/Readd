<!DOCTYPE html>
<html>
	<head>
		<title><?php wp_title( '-', true, 'right'); ?></title>
		<?php Readd_meta(); ?>
		
	<?php wp_head(); ?>
	</head>
	<body>
		<div id="wrapper">
			<header id="main-header">
				<div class="logo">
					<h1><a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'name' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<h2><?php bloginfo( 'description' ); ?></h2>
				</div>
				
				<nav id="main-nav">
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
					<form role="search" method="get" id="search-form" action="<?php echo home_url( '/' ); ?>">
						<div>
							<input type="text" value="" name="s" id="s" placeholder="Search" />
						</div>
					</form>
				</nav>