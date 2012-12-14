<?php
/*
Plugin Name: Unique Headers
Plugin URI: http://geek.ryanhellyer.net/
Description: Unique Headers
Version: 1.1
Author: Ryan Hellyer / Metronet
Author URI: http://geek.ryanhellyer.net/

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/



/**
 * Do not continue processing since file was called directly
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
if ( !defined( 'ABSPATH' ) )
	die( 'Eh! What you doin in here?' );

/**
 * Load classes
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
require( 'inc/class-taxonomy-header-images.php' );
require( 'inc/class-post-header-images.php' );
require( 'inc/class-multi-post-thumbnails.php' );

/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
define( 'UNIQUEHEADERS_DIR',     dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'UNIQUEHEADERS_URL',     WP_PLUGIN_URL . '/' . basename( UNIQUEHEADERS_DIR )  . '' ); // Plugin folder URL
define( 'UNIQUEHEADERS_OPTION', 'hyper-headers' );

/**
 * Instantiate classes
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
new Taxonomy_Header_Images();
new Post_Header_Images();
new MultiPostThumbnails(
	array(
		'label'     => __( 'Custom Header', 'unique_headers' ),
		'id'        => 'custom-header',
		'post_type' => 'post'
	)
);
new MultiPostThumbnails(
	array(
		'label'     => __( 'Custom Header', 'unique_headers' ),
		'id'        => 'custom-header',
		'post_type' => 'page'
	)
);
