<?php
/*
Plugin Name: DSS Admin
Plugin URI: http://metronet.no/
Description: Admin plugin for the DSS multisite network. Under development. Includes maintenance mode.
Author: Metronet / Ryan Hellyer
Version: 0.1
Author URI: http://metronet.no/
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
function dss_admin_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'dss_admin_hidden_plugin', 5, 2 );


function wpr_maintenance_mode() {
    if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
        wp_die('Maintenance, please come back soon.');
    }
}
//add_action('get_header', 'wpr_maintenance_mode');



/**
 * Define some constants
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryan@metronet.no>
 */
define( 'MN_ADMIN_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'MN_ADMIN_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL

/**
 * Load included files
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryan@metronet.no>
 */
require( 'inc/class-mn-admin-setup.php' );

/**
 * Instantiate classes
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryan@metronet.no>
 */
new Mn_Admin_Setup();
