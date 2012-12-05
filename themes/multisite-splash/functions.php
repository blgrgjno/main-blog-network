<?php

/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
define( 'MSS_DIR', get_template_directory() ); // Plugin folder DIR
define( 'MSS_URL', get_template_directory_uri() );
define( 'MSS_COMMENT_TIME_BUFFER', 60 * 60 * 60 * 6 );

require( 'inc/class-multisite-splash-core.php' );
$ms_splash = new Multisite_Splash_Core();

if ( is_admin() ) {
	require( 'inc/class-multisite-splash-admin.php' );
	new Multisite_Splash_Admin();
}
