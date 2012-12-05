<?php
/*
Plugin Name: DSS Statistics
Plugin URI: http://blogg.regjeringen.no/
Description: Adds statistics tracking codes for multi-site
Version: 1.0
Author: Ryan Hellyer / Metronet
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
define( 'DSSSTATS_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'DSSSTATS_URL', WP_PLUGIN_URL . '/' . basename( DSSSTATS_DIR ) ); // Plugin folder URL

/**
 * Load class
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
require( 'class-dss-statistics.php' );

/**
 * Instantiate class
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
new DSS_Statistics();

