<?php

/*
 * Block some super admins from accessing the file system
 */
function dss_block_some_super_admins() {
	$user_id = get_current_user_id();
	if (
		1 != $user_id &&  // Ryan Hellyer
		75 != $user_id && // Gorm Haug Eriksen
		181 != $user_id   // Per Soderlin
	) {
		define( 'DISALLOW_FILE_MODS', true );
	}
	
}
add_action( 'plugins_loaded', 'dss_block_some_super_admins' );

