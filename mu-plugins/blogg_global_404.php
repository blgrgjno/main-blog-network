<?php
/*
Plugin Name: redirect 404 to home
Plugin URI: http://soderlind.no/
Description: 
Version: 0.5
Author: Per Soderlind
Author URI: http://soderlind.no/
Network: true
 
*/

function blogg_global_404() {
	if ( is_404() ){
		wp_redirect( network_home_url('/') . '?e=404' );
		die();
	}
}
add_action( 'template_redirect', 'blogg_global_404' );
?>