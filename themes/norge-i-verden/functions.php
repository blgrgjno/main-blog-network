<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function niv_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'niv_hidden_theme', 5, 2 );

/*
 * Adds category specific site headings when on category or single posts pages
 * Intended for use only with the DSS Framework theme
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function niv_site_heading( $name ) {

	if ( is_single() || is_category() ) {
		$cats = get_the_category( $post->ID );
		$cats = $cats[0];
		$name = $cats->name;
	}
	return $name;
}
add_filter( 'dss_site_heading', 'niv_site_heading' );

/*
 * Adds category specific site headings when on category or single posts pages
 * Intended for use only with the DSS Framework theme
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @global int $post
 */
function niv_thumbnails( $thumb ) {
	global $post;

	if ( is_front_page() ) {
		$thumb = get_avatar( $post->post_author, 75 );
	}
	return $thumb;
}
add_filter( 'dss_thumbnails', 'niv_thumbnails' );

/*
 * Adds category specific site headings when on category or single posts pages
 * Intended for use only with the DSS Framework theme
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @global int $post
 */
function niv_top_menu( $top_menu ) {
	global $post;

	if ( is_single() || is_category() ) {
		$categories = get_the_category( $post->ID );
		$category_object = $categories[0];
		$category_name = $category_object->name;
		$category_link = get_category_link( $category_object->term_id );
//		print_r( $cats );
		$top_menu = $top_menu . '<a class="top-menu-link" href="' . esc_url( $category_link ) . '">Hjem til ' . esc_html( $category_name ) . '</a>';
	}

	return $top_menu;
}
add_filter( 'dss_top_menu', 'niv_top_menu' );

/*
 * Filter for displaying "Profile Widget" plugin on category pages
 * Modifies the result of the is_single() check to allow for categories as well
 * 
 * This functionality is abnormal since categories typically have more than one author, and therefore
 * the profile widget would usually not be desired on the categories page.
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @return bool
 */
function niv_confirm_display() {
	if ( is_category() || is_single() )
		return false;
	else
		return true;
}
add_filter( 'profwid_confirm_display', 'niv_confirm_display' );

/*
 * Filters the author bio information selection (turns it off)
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @return bool
 */
function niv_show_author_bio() {
	return false;
}
add_filter( 'dss_show_author_bio', 'niv_show_author_bio' );

/*
 * Filters the author bio information selection (turns it off)
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @return bool
 */
function dss_logo_url() {
	$logo_url = get_stylesheet_directory_uri() . '/images/logo.png';
	return $logo_url;
}
add_filter( 'dss_logo_url', 'dss_logo_url' );
