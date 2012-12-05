<?php

function add_query_vars($query_v) {
	$query_v[] = "firstchar";
	return $query_v;
}

function add_rewrite_rules( $wp_rewrite ) {
	$new_rules = array();
	
	$new_rules['(.+?)/forbokstav/([^/]+)/?$'] = 'index.php?pagename=$matches[1]&firstchar=$matches[2]';
	
	$old_rules = $wp_rewrite->rules;
	
	$wp_rewrite->rules = $new_rules + $old_rules;
	
	return $wp_rewrite->rules;
}
add_action('query_vars','add_query_vars');
add_filter('generate_rewrite_rules', 'add_rewrite_rules');

?>