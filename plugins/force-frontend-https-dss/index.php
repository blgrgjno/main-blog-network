<?php
/*

Plugin Name: Force frontend https for DSS
Plugin URI: http://metronet.no
Description: Forces frontend of a single site on a multisite network to use https only. Uses HTTP_HOST due to issues with standard current page URL functions when used with domain mapping on DSS network.
Author: Ryan Hellyer
Version: 1.0
Author URI: http://metronet.no/

Copyright (c) 2012 Ryan Hellyer / Metronet


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
function dss_forcehttps_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'dss_forcehttps_hidden_plugin', 5, 2 );

/**
 * Force Frontend HTTPS class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Force_Frontend_HTTPS {

	/**
	 * Class constructor
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {

		// Bail out if in admin panel since this is only intended to work on the front-end
		if ( is_admin() )
			return;

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'init',              array( $this, 'init' ) );
	}

	/**
	 * Begins output buffering
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function template_redirect() {
		echo '<!-- test -->';
		ob_start( array( $this, 'ob' ) );
	}

	/**
	 * Callback for output buffer
	 * Filters URLs
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @return string
	 */
	public function ob( $content ) {

		// Rewrite uploads URLs
		$content_url = content_url();
		$http = str_replace( 'https://', 'http://', $content_url );
		$content = str_replace( $http, $content_url, $content );

		return $content;
	}

	/**
	 * Do redirect
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function init() {
		$current_location = $this->current_page_url();
		$pos = strpos( $current_location, 'http://' );

		if($pos === false) {}
		else {
			$new_location = str_replace( 'http://', 'https://', $this->current_page_url() );
			$new_location = str_replace( $_SERVER['SERVER_NAME'], $_SERVER['HTTP_HOST'], $new_location );

			wp_redirect( $new_location, 301 );
			exit;
		}
	}

	/**
	 * Add the admin menu item
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function current_page_url() {
		$pageURL = 'http';

		// Add "s" if https is on
		if ( $_SERVER["HTTPS"] == 'on' )
			$pageURL .= 's';

		$pageURL .= '://';
		if ( $_SERVER['SERVER_PORT'] != '80' ) {
			$pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}

}
new Force_Frontend_HTTPS();

