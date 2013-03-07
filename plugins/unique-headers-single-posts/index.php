<?php
/*
Plugin Name: Unique Headers Single Posts
Plugin URI: http://geek.ryanhellyer.net/products/unique-headers-single-posts
Description: Forces single posts pages to grab the first category from a post and use it's header image for that post
Version: 1.0
Author: Ryan Hellyer / Metronet
Author URI: http://metronet.no/

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


/*
 * Disabling plugin update checks
 * This is to avoid malicious use of the WordPress.org plugin repository to force updates on this plugin
 * Based on code from http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @param unknown $r
 * @param string $url
 */
function uniq_sph_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'uniq_sph_hidden_plugin', 5, 2 );


/**
 * Set single post header images to use same header as category
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Single_Post_Header_Images {

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Bail out now if taxonomy meta data plugin not installed
		if ( ! class_exists( 'Taxonomy_Metadata' ) && ! class_exists( 'Taxonomy_Header_Images' ) )
			return;

		add_action( 'init', array( $this, 'init'     ) );
	}

	/**
	 * Print styles to admin page
	 */
	public function init() {

		// Add filters
		add_filter( 'theme_mod_header_image', array( $this, 'header_image_filter' ) );

	}

	/*
	 * Filter for modifying the output of get_header()
	 */
	public function header_image_filter( $url ) {
		global $post;

		// Bail out now if not in category
		if ( ! is_single() )
			return $url;

		// Grab category ID
		$category = get_the_category( $post->ID );
		$category = $category[0];
		$cat_id = $category->term_id;

		// Grab stored taxonomy header
		$new_url = get_term_meta( $cat_id, 'taxonomy-header-image', true );

		// If no URL set, then bail out now
		if ( '' != $new_url )
			$url = $new_url;

		return $url;
	}

}
new Single_Post_Header_Images;
