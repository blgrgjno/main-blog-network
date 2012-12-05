<?php

/**
 * Core functionality for Multisite Splash
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Multisite_Splash_Core {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks or shortcodes
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		add_theme_support( 'menus' );
		register_nav_menus(
			array(
				'footer' => 'The footer menu'
			)
		);
	}

	/**
	 * Limit string to set character length
	 * Doesn't split in middle of a word
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param  string $string     The string of text to be shortened
	 * @param  int    $nb_caracs  Number of characters to limit the string to
	 * @param  string $separator  The text to add at the end when string is shortened
	 * @return string
	 */
	function limit_string( $string, $nb_caracs, $separator ){
		$string = strip_tags( html_entity_decode( $string ) );
		if ( strlen( $string ) <= $nb_caracs ){
			$final_string = $string;
		} else {
			$final_string = '';
			$words = explode( ' ', $string );
			foreach( $words as $value ) {
				if ( strlen( $final_string . ' ' . $value ) < $nb_caracs ) {
					if ( !empty( $final_string ) ) $final_string .= ' ';
					$final_string .= $value;
				} else {
					break;
				}
			}
			$final_string .= $separator;
		}
		return $final_string;
	}

	/**
	 * Get option from DB
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param string $option
	 * @return string
	 */
	public function get_option( $option = '' ) {
		$options = get_option( 'ms_splash' );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
		elseif( '' == $option ) {
			return $options;
		}
		else {
			return;
		}
	}

}

