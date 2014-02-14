<?php

/**
 * Remove the excerpt filter. They want their news liste on the page.
 */
function dss_limit_excerpt( $excerpt_param ) {
	$excerpt = get_the_content();
	$excerpt = strip_shortcodes( $excerpt );
	return $excerpt;
}

add_filter( 'comment_form_default_fields', 'dss_remove_url_filtered' );
function dss_remove_url_filtered( $fields ) {
	if ( isset( $fields['url'] ) ) {
		unset( $fields['url'] );
	}
	return $fields;
}

?>

