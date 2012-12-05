<?php

/*
 * Adds a bright red box on localhost
 * Box contains the server name
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function ryans_localhost() {

	// Do check for localhost IP (remove this if you want to ALWAYS display it)
	if ( '127.0.0.1' != $_SERVER['REMOTE_ADDR'] ) {
		return;
	}

	// Echo's the red box
	echo '
		<div style="
			position: fixed;
			right: 10px;
			bottom: 10px;
			width: auto;
			padding: 0 8px;
			height: 22px;
			background: #ff0000;
			border-radius: 5px;
			box-shadow: 0 2px 5px 2px rgba(0,0,0,0.3);
			z-index: 99999999999999;

			font-family: sans-serif;
			font-size: 13px;
			line-height: 22px;
			color: #fff;
			font-weight: bold;
			text-align: center;
			text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
		">' . php_uname( 'n' ) . '</div>';
}
add_action( 'wp_footer', 'ryans_localhost' );
add_action( 'admin_footer', 'ryans_localhost' );

?>