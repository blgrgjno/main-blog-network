<?php

/// Register the new menus
function hearing_register_nav_menus() {
	register_nav_menus(
		array(
			'primary-menu' => __( 'Toppmeny' ),
			'article-menu' => __( 'Artikkelliste' ),
			'footer-menu' => __( 'Bunnmeny' )
		)
	);
}
add_action( 'init', 'hearing_register_nav_menus' );

?>