<?php
/*

Plugin Name: Frontend https
Plugin URI: http://metronet.no/
Description: Uses https for all URLs when visiting an https page.
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
function dss_frontendhttps_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'dss_frontendhttps_hidden_plugin', 5, 2 );


/**
 * Frontend HTTPS class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Frontend_HTTPS {

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

	}

	/**
	 * Converts strings containing http URLs to https URLs when on an https page
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param $string
	 * @return string
	 */
	public function convert_to_ssl( $string ) {
		if ( is_ssl() ) {
			$http_url = get_site_url( '', '', 'http' );
			$https_url = str_replace( 'http://', 'https://', $string );
			$string = str_replace( $http_url, $https_url, $string );
		}
		return $string;
	}

	/**
	 * Begins output buffering
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function template_redirect() {
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
		if ( is_ssl() ) {
			$http_url = get_site_url( '', '', 'http' );
			$https_url = get_site_url( '', '', 'https' );
			$content = str_replace( $http_url, $https_url, $content );
		}

		return $content;
	}

}
$frontend_https = new Frontend_HTTPS();
