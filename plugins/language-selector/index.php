<?php
/*
Plugin Name: Language Selector
Plugin URI: http://geek.ryanhellyer.net/language-selector/
Description: Uber simple language selector for allowing individual users to custom pick which language they choose to use the WordPress admin panel in
Version: 1.2
Author: Ryan Hellyer
Author URI: http://geek.ryanhellyer.net/

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/



/**
 * Language selector
 *
 * Props to Justin Tadlock for code used in this class ... http://justintadlock.com/archives/2009/09/10/adding-and-using-custom-user-profile-fields
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Language_Selector {

	/**
	 * Constructor
	 * Add methods to appropriate hooks
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		// Stash the default language so that we can still use it after we've filtered the original
		define( 'LANG_SELECT_DEFAULT', get_option( 'WPLANG' ) );

		// Add actions
		add_action( 'show_user_profile',        array( $this, 'show_profile_field' ) );
		add_action( 'edit_user_profile',        array( $this, 'show_profile_field' ) );
		add_action( 'personal_options_update',  array( $this, 'save_profile_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_field' ) );

		// Add filters
		add_filter( 'locale',        array( $this, 'change_wplang' ) );
		add_filter( 'mu_dropdown_languages',    array( $this, 'fudge_lang_selector' ) );
	}

	/**
	 * Fudges the language selector selection on the options-general.php page in multisite
	 *
	 * @todo Add support for the network language selection setting (if needed - not sure if it uses the mu_dropdown_languages function or not) and also single site language selector (if it exists)
	 * @since 1.1
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @param string $string HTML for the multisite language selector dropdown on the general options page
	 * @return string
	 */
	public function fudge_lang_selector( $string ) {

		// Remove existing selection
		$string = str_replace( " selected='selected'", '', $string );

		// Update with original selection from before we filtered the language
		$string = str_replace(
			'value="' . LANG_SELECT_DEFAULT . '"',
			'value="' . LANG_SELECT_DEFAULT . '" selected="selected"',
			$string
		);

		return $string;
	}

	/**
	 * Change the WPLANG option
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @return string
	 */
	public function change_wplang() {
		$current_user = wp_get_current_user();
		$wplang = get_the_author_meta( 'WPLANG', $current_user->ID );
		return $wplang;
	}

	/**
	 * Show the profile fields
	 *
	 * Props to Zé Fontainhas (http://ze.fontainhas.com/) for suggesting the get_available_languages() function
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @param object $user The user object
	 */
	public function show_profile_field( $user ) {
		
		// Get the current language seclection
		$wplang = get_the_author_meta( 'WPLANG', $user->ID );

		// If no language selected, then use the language selected for this site
		if ( ! $wplang ) {
			$wplang = get_option( 'WPLANG' );
		}

		?>
	
		<h3><?php _e( 'Select language', 'lang_select' ); ?></h3>
	
		<table class="form-table">
	
			<tr>
				<th><label for="WPLANG"><?php _e( 'Language', 'lang_select' ); ?></label></th>
	
				<td>
					<select name="WPLANG" id="WPLANG"><?php

					// Display default US English option
					echo '<option value="en_US"' . $selected . '>en_US</option>';

					// Loop through all the available languages and displays an option for each
					foreach( get_available_languages() as $key => $lang ) {
						if ( $wplang == $lang ) {
							$selected = ' selected="selected" ';
						}
						else {
							$selected = '';
						}
						echo '<option value="' . $lang . '"' . $selected . '>' . $lang . '</option>';
					}
					?>
					</select>
					<span class="description"><?php _e( 'Choose your language', 'lang_select' ); ?></span>
				</td>
			</tr>
	
		</table>
	<?php }
	
	/**
	 * Save the profile field
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @param int $user_id The user ID to be edited
	 */
	public function save_profile_field( $user_id ) {
	
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		
		$wplang = esc_attr( $_POST['WPLANG'] ); // Sanitise the selection
		update_usermeta( $user_id, 'WPLANG', $wplang ); // Update the user meta

	}

}

new Language_Selector;
