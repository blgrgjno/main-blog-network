<?php
/*
Plugin Name: Holidays Admin
Plugin URI: http://geek.ryanhellyer.net/products/holidays-admin/
Description: Super fast and super customizable social sharing
Version: 0.1
Author: Ryan Hellyer / Metronet
Author URI: http://geek.ryanhellyer.net/
*/


/**
 * Define some constants
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryan@metronet.no>
 */
define( 'HOLIDAYS_ADMIN_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'HOLIDAYS_ADMIN_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL

/**
 * Add some Christmas holly
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function holidays_admin() {
	echo '
	<style>
		#holiday-footer-left {
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100px;
			height: 49px;
			background: url(' . HOLIDAYS_ADMIN_URL . '/images/holly-left.png);
		}
		#holiday-footer-right {
			position: fixed;
			right: 0;
			bottom: 0;
			width: 100px;
			height: 49px;
			background: url(' . HOLIDAYS_ADMIN_URL . '/images/holly-right.png);
		}
	</style>
	<div id="holiday-footer-left"></div>
	<div id="holiday-footer-right"></div>';
}
add_action( 'admin_footer', 'holidays_admin' );

