<?php

/* Add valid comment tags */

function hod_preprocess_comment($data) {
	global $allowedtags; 
	$allowedtags['ul'] = array();
	$allowedtags['ol'] = array();
	$allowedtags['li'] = array();
	$allowedtags['u'] = array();
	$allowedtags['h1'] = array();
	$allowedtags['h2'] = array();
	$allowedtags['h3'] = array();
	$allowedtags['h4'] = array();
	$allowedtags['h5'] = array();
	$allowedtags['h6'] = array();
	return $data;
}
add_filter('preprocess_comment','hod_preprocess_comment');

?>