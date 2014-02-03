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
		$curl = curl_init();
		curl_setopt_array( $curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'http://blogg.regjeringen.no/?e=404'
		) );
		$result = curl_exec( $curl );
		ob_end_clean();
		ob_start();
		print $result;
		ob_end_flush();
		die();
	}
}
add_action( 'template_redirect', 'blogg_global_404' );

?>