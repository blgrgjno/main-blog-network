<?php

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
