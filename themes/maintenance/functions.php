<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function maintainence_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'maintainence_hidden_theme', 5, 2 );

function zwpr_maintenance_mode() {
	if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
		echo 'Maintenance, please come back soon.';
		die;
	}

	die( 'Maintenance, please come back soon.' );
}
if ( !is_admin() ) {
	add_action('get_header', 'zwpr_maintenance_mode');
}
	
	