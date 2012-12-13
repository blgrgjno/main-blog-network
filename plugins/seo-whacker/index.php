<?php
/*

Plugin Name: SEO Whacker
Plugin URI: http://geek.ryanhellyer.net/products/seo-whacker/
Description: Removes features from the WordPress SEO plugin which are often unrequired
Author: Ryan Hellyer
Version: 1.1
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2012 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/* 
 * Disable plugin updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 * @since 1.0.1
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function seowhacker_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'seowhacker_hidden_plugin', 5, 2 );

/*
 * Remove tooltips and tracking option
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
function seowhacker_tooltips() {
	if ( ! is_admin() )
		return;

	$options = get_option( 'wpseo' );
	$new_options['yoast_tracking'] = 'off';
	$new_options['ignore_tour'] = true;
	if ( $options != $new_options ) {
		update_option( 'wpseo', $new_options );
	}

}
seowhacker_tooltips();

/*
 * Remove admin menu options for non-super admins and multisite
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
function seowhacker_remove_menus() {
	if ( is_super_admin() || ! is_multisite() )
		return;

	remove_menu_page( 'wpseo_dashboard' );
}
add_action( 'admin_menu', 'seowhacker_remove_menus', 999 );

/*
 * Removing admin bar junk from view
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.1
 */
function seowhacker_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wpseo-menu');
}
add_action( 'wp_before_admin_bar_render', 'seowhacker_admin_bar' );

/*
 * Removes unneeded sections in the post edit screen
 * Removes the advanced and page analysis sections
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.1
 */
function seowhacker_remove_blocks() {
	?>
	<style>
	.wpseo-metabox-tabs-div .advanced,
	.wpseo_tablink,
	.wpseo-metabox-tabs-div li.general,
	#linkdex {
		display: none;
	}
	</style><?php
}
add_action( 'admin_head', 'seowhacker_remove_blocks' );
