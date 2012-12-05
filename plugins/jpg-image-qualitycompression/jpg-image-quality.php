<?php
/*
Plugin Name: JPG Image Quality
Plugin URI: http://fractured-state.com/2009/03/wordpress-image-quality-plugin/
Description: This plugin lets you set the PHP image compression setting that affects image quality when you upload an image.
Version: 1.0.1
Author: Paul Sheldrake
*/

// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//

function image_quality_api_init() {
	// Add the section to reading settings so we can add our fields to it
	add_settings_section('image_quality_section', 'Image Quality', 'image_quality_section_callback_function', 'media');
	
	// Add the field with the names and function to use for our new settings, put it in our new section
	add_settings_field('image_quality_field', 'Image Quality', 'image_quality_callback_function', 'media', 'image_quality_section');
	
	// Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
	register_setting('media','image_quality_field');
	
	// Add Filter
	add_filter('jpeg_quality','filter_image_quality');

	
}// image_quality_api_init()

add_action('admin_init', 'image_quality_api_init');

 
// ------------------------------------------------------------------
// Settings section callback function
// ------------------------------------------------------------------
//
// This function is needed if we added a new section. This function 
// will be run at the start of our section
//

function image_quality_section_callback_function() {
	echo "<p>Adjust the quality of the images when they get uploaded to the server.</p>";
}

// ------------------------------------------------------------------
// Callback function for our example setting
// ------------------------------------------------------------------
//
// creates a textbox to image quality on the Media page
//

function image_quality_callback_function() {
	$image_quality_value = 75;
	
	// Get image quality value if it's set.   Otherwise it's set to 75
	if (get_option('image_quality_field')) {
		$image_quality_value = get_option('image_quality_field');
	}
	
	echo "<input type='text' class='small-text' value='$image_quality_value' id='image_quality_field' name='image_quality_field' maxlength='3' /> Enter a value from 1 - 100.  100 is the highest quality.";

} // image_quality_callback_function()

// ------------------------------------------------------------------
// Filter function 
// ------------------------------------------------------------------
//
// Applies the image quality field to the jpeg_quality filter
//

function filter_image_quality() {
	
	// Get image quality value if it's set.   Otherwise it's set to 75
	if (get_option('image_quality_field')) {
		$image_quality_value = get_option('image_quality_field');
	} else {
		$image_quality_value = 90;
	}
	
	return $image_quality_value;	

} // filter_image_quality()


?>
