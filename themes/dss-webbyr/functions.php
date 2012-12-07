<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function webbyr_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'webbyr_hidden_theme', 5, 2 );

// REMOVE TWENTY ELEVEN DEFAULT HEADER IMAGES
function wptips_remove_header_images() {
  unregister_default_headers( array('wheel','shore','trolley','pine-cone','chessboard','lanterns','willow','hanoi') );

  // remove singular so that we can use sidebars
  remove_filter( 'body_class', 'twentyeleven_body_classes' );
}
add_action( 'after_setup_theme', 'wptips_remove_header_images', 11 );

?>