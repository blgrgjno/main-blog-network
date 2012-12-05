<?php
/*

Plugin Name: Delete Delete Site
Plugin URI: http://pixopoint.com/products/delete-delete-site/
Description: The <a href="http://pixopoint.com/products/delete-delete-site/">Delete Delete Site Plugin</a> removes the "Delete site" link from your WordPress multisite admin panel.

Author: Ryan Hellyer / Metronet / DSS
Version: 1.0
Author URI: http://pixopoint.com/

Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/


/**
 * Remove menus
 *
 * Adapted from Ryans Simple CMS ... http://wordpress.org/extend/plugins/ryans-simple-cms/
 *
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
function dds_remove_menus () {

	// Bail out now if it's a super admin ... they're like site gods, therefore are allowed to do whatever they like
	global $user_ID;
	if ( is_super_admin( $user_ID ) )
		return;

	// Remove the page
	remove_submenu_page( 'tools.php', 'ms-delete-site.php' );

}
add_action( 'admin_menu', 'dds_remove_menus' );
