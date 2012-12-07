<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function worldheritage_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'worldheritage_hidden_theme', 5, 2 );

/**
 * Do not continue processing since file was called directly
 * @since 0.1
 */
if ( !defined( 'ABSPATH' ) )
	die( 'Eh! What you doin in here?' );

/**
 * Load required files
 * Some files not loaded unless in admin panel
 * @since 0.1
 */
require( get_template_directory() . '/class-reorder.php' );
require( get_template_directory() . '/class-world-heritage-setup.php' );
require( get_template_directory() . '/class-multi-post-thumbnails.php' );

/**
 * Load required files
 * Some files not loaded unless in admin panel
 * @since 0.1
 */
new WorldHeritageSetup();
new Reorder(
	array(
		'post_type' => 'page', 
		'final'     => '<p>Note that only the first six pages will be displayed.</p>', 
		'initial'   => '<p>Control the order of pages as displayed on the home page.</p>'
	)
);
new MultiPostThumbnails(
	array(
		'label'     => 'Home Page Thumbnail',
		'id'        => 'home-thumbs',
		'post_type' => 'page'
	)
);
