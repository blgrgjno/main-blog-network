<?php



function zwpr_maintenance_mode() {

	if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
		echo 'Maintenance, please come back soon.';
		die;
	}

	die( 'Maintenance, please come back soon.' );
}
if ( !is_admin() )
	add_action('get_header', 'zwpr_maintenance_mode');
	
	