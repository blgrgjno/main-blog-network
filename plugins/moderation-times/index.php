<?php
/*
Plugin Name: Moderation times
Plugin URI: http://blogg.regjeringen.no/
Description: Adds ability to set when comments are moderated and when they are left open. An example of when this would be useful, is scenarios in which you are able to actively delete inappropriate comments (eg: during office time) at certain times of the day, but are unable to at others (eg: during the evenings) and therefore require moderation to be turned on and off at specific times during the day.
Version: 1.0
Author: Ryan Hellyer / Metronet / DSS
Author URI: http://blogg.regjeringen.no/

------------------------------------------------------------------------
Copyright Metronet AS

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
 * Do not continue processing since file was called directly
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
if ( !defined( 'ABSPATH' ) )
	die( 'Eh! What you doin in here?' );
	
/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
define( 'MODERATION_TIMES_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'MODERATION_TIMES_URL', WP_PLUGIN_URL . '/' . basename( MODERATION_TIMES_DIR )  . '' ); // Plugin folder URL

/**
 * Load required files
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
require( 'inc/class-moderation-times.php' );
require( 'inc/class-moderation-cron-job.php' );

/**
 * Instantiate the classes
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
new Moderation_Times();
new Moderation_Cron_Job();
