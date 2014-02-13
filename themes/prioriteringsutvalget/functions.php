<?php

/**
 * Remove the excerpt filter. They want their news liste on the page.
 */
function dss_limit_excerpt( $excerpt_param ) {
	$excerpt = get_the_content();
	$excerpt = strip_shortcodes( $excerpt );
	return $excerpt;
}

?>

