<?php
/**
 * @package WordPress
 * @subpackage Metronet Admin
 *
 * @since 0.1
 *
 */


/**
 * Metronet Admin Dashboard widgets
 * 
 * @copyright Copyright (c), Metronet
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 0.1
 */
class Mn_Admin_Setup {

	/**
	 * Constructor
	 * Add methods to appropriate hooks
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'admin_remove_dashboard_widgets' ) ); // Remove dashboard widgets
		add_action( 'admin_print_styles', array( $this, 'css' ) ); // Admin panel specific CSS
		add_action( 'login_head',         array( $this, 'css' ) );
		add_filter( 'admin_footer_text',  array( $this, 'admin_footer' ) );
		add_action( 'admin_head',         array( $this, 'admin_favicon' ) );
	}

	/**
	 * Add favicon
	 * Checks for expected site specific images, before defaulting to Pressabl favicon
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_favicon() {
	
		// Display appropriate favicon
		echo "\n<link rel='shortcut icon' href='" .  MN_ADMIN_URL . "/favicon.ico' />\n";
	
	}
	
	/**
	 * Styling for the login page
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function css() { 
		echo '<link rel="stylesheet" type="text/css" href="' . MN_ADMIN_URL . '/style.css" />';
	}
	
	/**
	 * Replace footer text
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_footer () {
		_e( 'Website by <a href="http://blogg.regjeringen.no/">Regjeringen</a>.', 'mn_admin' );
	}

	/**
	 * Remove menus
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_remove_dashboard_widgets() {
		global $wp_meta_boxes;
	
		// Remove dashboard widgets
		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
	}
}
