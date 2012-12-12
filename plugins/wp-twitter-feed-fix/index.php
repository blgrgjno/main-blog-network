<?php
/*
Plugin Name:  Twitter Feed for WordPress Fix
Plugin URI:   http://geek.ryanhellyer.net/products/wp-twitter-feed-fix/
Description:  Fix for https bugs in WP Twitter feed plugin
Version:      1.0.1
Author:       Ryan Hellyer
Author URI:   http://geek.ryanhellyer.net/
Contributors: ryanhellyer, pleer

Copyright (C) Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/


/*
 * Disabling plugin update checks
 * This is to avoid malicious use of the WordPress.org plugin repository to force updates on this plugin
 * Based on code from http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @param unknown $r
 * @param string $url
 * @since 1.0.1
 */
function twitterfeedfix_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'twitterfeedfix_hidden_plugin', 5, 2 );

/**
 * WP Twitter Feed Fix class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class WP_Twitter_Feed_Fix {

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
			$content = str_replace( 'http://si0.twimg.com/images/dev/cms/intents/icons/retweet.png', plugins_url( '/retweet.png', __FILE__ ), $content );
			$content = str_replace( 'http://a0.twimg.com', 'https://si0.twimg.com', $content );
		}

		return $content;
	}

}
$wp_twitter_feed_fix = new WP_Twitter_Feed_Fix();
