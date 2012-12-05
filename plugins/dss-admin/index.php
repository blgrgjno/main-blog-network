<?php
/*
Plugin Name: DSS Admin
Plugin URI: http://metronet.no/
Description: Admin plugin for the DSS multisite network. Under development. Includes maintenance mode.
Author: Metronet / Ryan Hellyer
Version: 0.1
Author URI: http://metronet.no/
*/



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
