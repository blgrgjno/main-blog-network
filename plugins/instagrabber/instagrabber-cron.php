<?php
/**
 * Call this file from crontab or any other systems to load all images.
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';
require_once 'instagrabber.php';
require_once 'includes/database.class.php';

if ( get_option( 'instagrabber_auto_post_creation' ) == 'true' ) {
	
	return;
}

//Mark as running
update_option( 'instagrabber_auto_post_creation', 'true' );

//get streams
$streams = Database::get_streams();
$instagrabber = new Instagrabber;
foreach ( $streams as $key => $stream ) {
	$instagrabber->import_images( $stream );
}


//Mark as done
update_option( 'instagrabber_auto_post_creation', false );
delete_option( 'instagrabber_auto_post_creation' );
?>
