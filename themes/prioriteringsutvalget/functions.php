<?php


/**
 * Change logo image
 */
add_filter( 'dss_logo_url', function($url) { 
	return get_stylesheet_directory_uri() . '/images/riksvapen.jpg';
});




?>