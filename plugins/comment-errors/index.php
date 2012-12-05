<?php
/*
Plugin Name: Comment Errors
Plugin URI: http://pixopoint.com/products/comment-errors/
Description: The <a href="http://pixopoint.com/products/comment-errors/">Comment Errors Plugin</a> prevents your site visitors from ever seeing the ugly default error page in WordPress and instead redirects them back to where they were attempting to post to, and adds an appropriate error message to the comments section.

Author: Ryan Hellyer / Metronet
Version: 1.0.1
Author URI: http://metronet.no/

Copyright 2012 Metronet

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
 * Define some constants
 * 
 * @since 0.1
 * @author Ryan Hellyer <ryan@metronet.no>
 */
define( 'COMMENT_ERRORS_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'COMMENT_ERRORS_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL

/**
 * Comment Errors
 * 
 * @copyright Copyright (c), Metronet
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 0.1
 */
class Comment_Errors {

	/**
	 * Constructor
	 * Add methods to appropriate hooks
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		add_action( 'wp_head',          array( $this, 'error_css' ) );
		add_action( 'wp_die_handler',   array( $this, 'error_processor' ) );
		add_action( 'comment_form_top', array( $this, 'error_notice' ) );
		add_action( 'init',             array( $this, 'localization' ) );
	}

	/**
	 * Add localization support
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function localization() {
		load_plugin_textdomain( 'comment_errors', false, COMMENT_ERRORS_DIR . 'languages/' );
	}

	/**
	 * Comment error processor
	 * Redirects back to post along with query var (used for displaying error message)
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function error_processor() {
	
		// If no comment loaded, then serve 'nocomment' error
		if ( '' == $_POST['comment'] )
			$comment_error = 'nocomment';
		else
			$comment_error = '';
	
		// Grab post ID to redirect to
		$comment_post_ID = (int) $_POST['comment_post_ID'];
	
		wp_redirect(
			home_url() . '?comment_error=' . $comment_error . '&p=' . $comment_post_ID . '#respond', // URL to redirect to
			302 // Temporary redirect
		);
		exit;
	}

	/**
	 * Comment error processor
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function error_notice() {
	
		// Bail out now if comment_error query var not set
		if ( empty( $_GET['comment_error'] ) )
			return;
	
		// Set the notice to display
		if ( 'nocomment' == $_GET['comment_error'] )
			$comment_notice = __( '<strong>ERROR:</strong> please fill the required fields.', 'comment_errors' );
		else
			$comment_notice = __( '<strong>ERROR:</strong> sorry, but there was an error with your comment submission.', 'comment_errors' );
	
		// Display notice
		echo '<div id="comments-error-message"><p>' . $comment_notice . '</p></div>';
	}

	/**
	 * Load styles for error message
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function error_css() {

		// Bail out now if comment_error query var not set
		if ( empty( $_GET['comment_error'] ) )
			return;

		wp_register_style( 'error-css', COMMENT_ERRORS_URL . '/comment-errors.css' );
		wp_enqueue_style( 'error-css' );
	}

}

new Comment_Errors;

