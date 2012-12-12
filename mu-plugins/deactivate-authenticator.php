<?php
/*
Plugin Name: Deactivate Google Authenticator
Plugin URI: http://geek.ryanhellyer.net/products/deactivate-google-authenticator/
Description: Deactivate Google Authenticator based on IP
Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/
Requires: WordPress 3.5

Copyright 2012  Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



/*
 * Deactivate Google Authenticator when not at correct IP
 *
 * @since 1.0
 * @author Ryan <ryan@metronet.no>
 * @global array $google_authenticator
 */
function deactivate_google_authenticator() {
	global $google_authenticator;

	// Don't force multifactor authentication for users at the correct IP
	if ( '127.0.0.1' == $_SERVER['REMOTE_ADDR'] ) {
		remove_action( 'login_form',   array( $google_authenticator, 'loginform' ) );
		remove_action( 'login_footer', array( $google_authenticator, 'loginfooter' ) );
		remove_filter( 'authenticate', array( $google_authenticator, 'check_otp' ), 50, 3 );
	}
}
add_action( 'init', 'deactivate_google_authenticator', 11 );
