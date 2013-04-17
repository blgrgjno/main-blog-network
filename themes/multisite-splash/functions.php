<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function splash_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'splash_hidden_theme', 5, 2 );

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

require( 'inc/class-multisite-splash-admin.php' );
new Multisite_Splash_Admin();

require( 'inc/class-dss-simple-cms.php' );
new DSS_Simple_CMS();

