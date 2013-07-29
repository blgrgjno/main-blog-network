<?php
/**
  * Plugin Name: Comment Ratings
  * Plugin URI: http://www.metronet.no/
  * Description: Allows users to vote on comments, sorting them via AJAX by vote
  * Version: 0.1
  * Author: Ryan Hellyer / Metronet / Justin Sainton
  * Author URI: http://www.metronet.no/
  *
  * This code was built based on code provided by Justin Sainton
  **/

require( 'inc/class-comment-ratings.php' );
require( 'inc/class-comment-ratings-widget.php' );

define( 'CR_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'CR_URL', WP_PLUGIN_URL . '/' . basename( CR_DIR )  . '' ); // Plugin folder URL

/**
 * Comment Rating statistics widget
 */
function cr_load_widget() {
	register_widget( 'Comments_Rating_Widget' );
}
add_action( 'widgets_init', 'cr_load_widget' );
