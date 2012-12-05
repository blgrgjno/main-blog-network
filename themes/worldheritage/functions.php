<?php

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
