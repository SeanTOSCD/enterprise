<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * @package Enterprise
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function enterprise_body_classes( $classes ) {

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) :
		$classes[] = 'group-blog';
	endif;
	
	// Adds body class based on page template
	if ( is_page_template( 'templates/full-width.php' ) ) :		
		$classes[] = 'full-width';
	elseif ( is_page_template( 'templates/landing-page.php' ) ) :		
		$classes[] = 'landing-page';
	endif;
	
	// Adds body class based on column configuration
	if ( 'cs' == get_theme_mod( 'enterprise_columns_layout' ) ) :
		$classes[] = 'content-sidebar';
	endif;
	
	// Adds classes based on feature box widget configuration
	$count = 0;
	$widget_areas = array( 'feature-box-1', 'feature-box-2', 'feature-box-3');
	foreach ( $widget_areas as $widget ) {
		if ( is_active_sidebar( $widget ) ) :
			$count = $count + 1;
		endif;
	}
	$classes[] = 'fb-widgets-' . $count;
	
	return $classes;
}
add_filter( 'body_class', 'enterprise_body_classes' );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function enterprise_wp_title( $title, $sep ) {
	if ( is_feed() ) {
		return $title;
	}

	global $page, $paged;

	// Add the blog name
	$title .= get_bloginfo( 'name', 'display' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	// Add a page number if necessary:
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= " $sep " . sprintf( __( 'Page %s', '_s' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'enterprise_wp_title', 10, 2 );

/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function enterprise_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	}
}
add_action( 'wp', 'enterprise_setup_author' );
