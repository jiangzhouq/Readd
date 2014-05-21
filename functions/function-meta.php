<?php
/**
 * theme_meta
 */
function Readd_meta(){ ?>
	<meta charset="UTF-8" />
	<?php
		global $post;
		if ( is_home() ){
			$description = get_bloginfo( 'description' );
			$keywords = get_bloginfo( 'name' );
		}else if ( is_single() ){
		$description = get_post_meta($post -> ID, "description", true);
			if ( $description == ""){
				if($post -> post_excerpt){
				$description = $post -> post_excerpt;
			}else{
					$description = mb_strimwidth(strip_tags($post -> post_content),0,200,'');
			}
			}
			$keywords = get_post_meta($post -> ID, "keywords", true);
			if ( $keywords == "" ){
				$tags = wp_get_post_tags($post->ID);
				foreach ($tags as $tag){
					$keywords = $keywords.$tag->name.",";
				}
				$keywords = rtrim($keywords, ', ');
			}
		}else if( is_page() ){
			$description = get_post_meta($post -> name, "description", true);
			$keywords = get_post_meta($post->name, "keywords", true);
		}else if( is_category() ){
			$description = category_description();
			$keywords = single_cat_title('', false);
		}else if( is_tag() ){
			$description = tag_description();
			$keywords = single_tag_title('', false);
		}
		$description = trim(strip_tags($description));
		$keywords = trim(strip_tags($keywords));
	?>
	<meta name="description" content="<?php echo $description; ?>" />
	<meta name="keywords" content="<?php echo $keywords; ?>" />
	<?php if( wp_is_mobile() ){?> <meta name="viewport" content="initial-scale=1.0,user-scalable=no" /> <?php }?>
	<link rel="shortcut icon" type="images/x-icon" href="<?php bloginfo("template_url"); ?>/images/favicon.ico" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'rss2_url' ); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'atom_url' ); ?>" />
<?php
}
?>