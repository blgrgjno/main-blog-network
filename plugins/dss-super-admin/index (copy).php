<?php
/*

Plugin Name: DSS Network Super Admin
Plugin URI: http://metronet.no/
Description: Provides more functionality for super admins
Author: Ryan Hellyer
Version: 1.0
Author URI: http://metronet.no/

Copyright (c) 2012 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/



/**
 * DSS Network Super Admin class
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class DSS_Network_Super_Admin {

	/**
	 * Class constructor
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {

		// Add to hooks
		add_action( 'admin_init',  array( $this, 'register_setting' ) );
		add_action( 'admin_menu',  array( $this, 'admin_menu' ) );
		add_action( 'wp_footer',   array( $this, 'display_script' ) );
		add_action( 'wp_head',     array( $this, 'display_css' ) );
		add_filter( 'the_excerpt', array( $this, 'filter_excerpt' ) );
		add_filter( 'admin_init' , array( &$this , 'register_fields' ) );

	}

	/**
	 * Register settings
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function register_setting() {
		register_setting(
			'dss_super',
			'dss_super',
			array( $this, 'options_validate' )
		);
	}

	/**
	 * Validate inputs
	 * Perform security checks on inputted data
	 * 
	 * Not strictly required since some of the data needs to be passed through directly (eg: JavaScript) but
	 * included for completeness and to ensure that only the data fields specified are allowed.
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function options_validate( $input ) {
		$output = array();

		if ( isset( $input['header-link'] ) ) {
			$output['header-link'] = esc_url( $input['header-link'] );
		}

		// Javascript - not sanitized 
		if ( isset( $input['javascript'] ) ) {
			$output['javascript'] = $input['javascript'];
		}

		// CSS - not sanitized 
		if ( isset( $input['css'] ) ) {
			$output['css'] = $input['css'];
		}

		// Checkboxes
		if ( isset( $input['advanced-excerpt'] ) ) {
			$output['advanced-excerpt'] = (bool) $input['advanced-excerpt'];
		}
		if ( isset( $input['hide-main-menu'] ) ) {
			$output['hide-main-menu'] = (bool) $input['hide-main-menu'];
		}

		return $output;
	}

	/**
	 * Add the admin menu item
	 * Only available to super admins
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	function admin_menu() {

		// Bail out now if not a super admin
		if ( ! is_super_admin() )
			return;

		add_theme_page(
			__( 'Super Admin', 'dss_super' ), // Page title
			__( 'Super Admin', 'dss_super' ), // Menu title
			'manage_options',                 // Capability
			'dss-super-admin',                // Menu slug
			array( $this, 'admin_page' )      // The page content
		);
	}

	/**
	 * The admin page contents
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_page() {
		global $screen_layout_columns;

		?>
	<style type="text/css">
	#icon-jsedit-icon {
		background: url(<?php echo plugins_url( 'admin-icon.png' , __FILE__ ); ?>) no-repeat;
	}
	#page-title {
		line-height: 52px;
	}
	</style>
	<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
	<div class="wrap">
		<h2 id="page-title"><?php screen_icon( 'jsedit-icon' ); ?><?php _e( 'Super Admin options', 'dss_super' ); ?></h2><?php
	
		// Display notice when page is updated
		if ( isset( $_REQUEST['settings-updated'] ) ) { ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'dss_super' ); ?></strong></p></div><?php
		} ?>

		<form id="dss-form" action="options.php" method="post">
			<?php settings_fields( 'dss_super' ); ?>
			
			<p>
				<label><?php _e( 'Custom CSS', 'dss_super' ); ?></label>
				<textarea style="width:100%;height:200px;" id="css" name="dss_super[css]"><?php echo str_replace('</textarea>', '&lt;/textarea&gt', $this->get_option( 'css' ) ); ?></textarea>
			</p>

			<p>
				<label><?php _e( 'Custom Javascript', 'dss_super' ); ?></label>
				<textarea style="width:100%;height:200px;" id="js" name="dss_super[javascript]"><?php echo str_replace('</textarea>', '&lt;/textarea&gt', $this->get_option( 'javascript' ) ); ?></textarea>
			</p><?php

			// Header links for use with DSS Framework theme
			if ( function_exists( 'dss_setup' ) ) { ?>
			<p>
				<label><?php _e( 'Header link', 'dss_super' ); ?></label>
				<br />
				<input id="header-link" name="dss_super[header-link]" value="<?php echo esc_attr( $this->get_option( 'header-link' ) ); ?>" />
			</p><?php
			}

			?>

			<p>
				<label><?php _e( 'Advanced excerpt', 'dss_super' ); ?></label>
				<br />
				<input id="advanced-excerpt" name="dss_super[advanced-excerpt]" type="checkbox" value="<?php
				echo true;
				?>"<?php
				checked( $this->get_option( 'advanced-excerpt' ), true ); ?> />
			</p>

			<p>
				<label><?php _e( 'Hide main menu', 'dss_super' ); ?></label>
				<br />
				<input id="hide-main-menu" name="dss_super[hide-main-menu]" type="checkbox" value="<?php
				echo true;
				?>"<?php
				checked( $this->get_option( 'hide-main-menu' ), true ); ?> />
			</p>

			<p>
				<br />
				<br />
				<?php /*<input type="button" class="button" id="preview" name="preview" value="<?php _e( 'Preview' ) ?>" />*/ ?>
				<input type="submit" class="button" id="save" name="save" value="<?php _e( 'Save &raquo;', 'dss_super') ?>" />
			</p>
		</form>
	</div>
	</div><?php

	}

	/**
	 * Get option from DB
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param string $option
	 * @return string
	 */
	public function get_option( $option ) {
		$options = get_option( 'dss_super' );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
		else {
			return;
		}
	}

	/**
	 * Purge/replace the template cache
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 */
	public function display_script() {

		echo "\n<!-- JavaScript added via the DSS Super Admin plugin -->\n<script>" . $this->get_option( 'javascript' ) . "</script>\n";

	}

	/**
	 * Purge/replace the template cache
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 */
	public function display_css() {

		echo "\n<!-- CSS added via the DSS Super Admin plugin -->\n<style>" . $this->get_option( 'css' ) . "</style>\n";

	}

	/**
	 * Adds an advanced excerpt
	 * Restricts excerpts to 40 words
	 * Finishes at end of sentence
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function filter_excerpt( $content ) {
		// Bail out now if not turned on
		if ( true != $this->get_option( 'advanced-excerpt' ) ) {
			return $content;
		}

		// Grab the content
		$content = get_the_content();

		// Split into chunks of sentences	
		$strings = preg_split('/(\.|!|\?)\s/', $content, 99, PREG_SPLIT_DELIM_CAPTURE);

		$excerpt = '';
		$dots = 0;
		foreach( $strings as $key => $value ) {

			// Add an extra sentence
			if ( ! isset( $end ) ) {
				$excerpt .= $strings[$key];
			}

			// Stop adding sentences once we hit 40 words
			if ( $dots == 1 ) {
				if ( 40 < str_word_count( $excerpt, 0 ) ) {
					$end = true;
				}
				$dots = 0;
			} else {
				$dots++;
			}
		
		}

		// Auto close HTML tags
		$excerpt = $this->fix_HTML( $excerpt );

		// Add excerpt
		$excerpt .= ' ... <a href="'. get_permalink() . '">Les mer</a>';

		// Do shortcodes and WPAUTOP - couldn't use the_content as it threw an error here
		$excerpt = strip_shortcodes( $excerpt );
			$excerpt = wpautop( $excerpt );

		// Finally, spit the excerpt out :)	
		return $excerpt;
	}

	/**
	 * Fix HTML problems, close tags etc.
	 * Code adapted from http://www.hoboes.com/Mimsy/hacks/auto-closing-html-tags-comments/
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param string $option
	 * @return string
	 */
	private function fix_HTML( $comment ) {
		$xml = new DOMDocument( '1.0' );
		if ( @$xml->loadHTML( $comment ) ) {
			// Pull just the body out and save it
			$body = $xml->getElementsByTagName( 'body' );
			$body = $body->item( 0 );
			$xml  = $xml->saveXML( $body );
			// DOMDocument appears to not use utf8 as its default
			$xml = utf8_decode( $xml );
			// Strip out the <body></body> tag
			$xml = substr( $xml, 6, -7 );
			return $xml;
		} else {
			return false;
		}
	}

	/**
	 * Add new fields to wp-admin/options-general.php page
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function register_fields() {
		register_setting( 'general', 'dss_blog_description', 'esc_attr' );
		add_settings_field(
			'fav_color',
			'<label for="favorite_color">' . __( 'Blog description' , 'dss_blog_description' ) . '</label>',
			array( &$this, 'fields_html' ),
			'general'
		);
	}

	/**
	 * HTML for extra settings
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function fields_html() {
		$value = get_option( 'dss_blog_description', '' );
		echo '<input type="text" id="dss_blog_description" name="dss_blog_description" value="' . esc_attr( $value ) . '" />';
	}

}
new DSS_Network_Super_Admin();
