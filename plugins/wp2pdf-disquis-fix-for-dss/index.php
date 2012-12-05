<?php
/*

Plugin Name: Fix for DSS to make WP 2 PDF
Plugin URI: http://blogg.regjeringen.no/
Description: Fix for Disquis comments which were not showing up for home page comments due to them being listed with no URL
Author: Ryan Hellyer
Version: 1.0
Author URI: http://metronet.no/

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
 * Disabling plugin update checks
 * This is to avoid malicious use of the WordPress.org plugin repository to force updates on this plugin
 * Based on code from http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @param unknown $r
 * @param string $url
 */
function dss_wp2pdffix_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'dss_wp2pdffix_hidden_plugin', 5, 2 );


/**
 * Fix arguments via filter
 * By leaving post_id blank, it will grab ALL comments regardless of post ID and display them in the PDF for the home page
 *
 * @todo Make it only display comments specifically with no URL specified
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function wp2pdf_dss_fix( $args ) {
	$post_id = $args['post_id'];

	if ( $post_id == get_option( 'page_on_front' ) && 'page' == get_option( 'show_on_front' ) ) {
		$args = array(
			'status'  => 'approve',
			'offset'  => $args['offset'],
			'number'  => $args['number'],
		);
	}

	return $args;
}
add_filter( 'wp2pdf_comment_args', 'wp2pdf_dss_fix' );
