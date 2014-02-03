<?php

/**
 * Remove the excerpt filter. They want their news liste on the page. 
 */
function dss_limit_excerpt( $excerpt ) {
	return $excerpt;
}

?>

