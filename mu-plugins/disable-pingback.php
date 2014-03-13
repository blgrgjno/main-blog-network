<?php

/*
 * Disable pingback for security purposes
 */

add_filter( 'xmlrpc_methods', function( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
});