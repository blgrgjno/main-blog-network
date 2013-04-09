<?php 
/**
 * Theme functions for this plugin. both gets and prints functions.
 * TODO: Comment this page
 */

//get images
function instagrabber_get_images($stream_id, $args = array()){
	return Database::get_images($stream_id, $args );
}

function instagrabber_get_image_caption($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->caption;
}
function instagrabber_image_caption($post_id = ''){
	echo instagrabber_get_image_caption($post_id);
}


function instagrabber_get_image_url($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->pic_url;
}
function instagrabber_image_url($post_id = ''){
	echo instagrabber_get_image_url($post_id);
}


function instagrabber_get_image_thumbnail_url($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->pic_thumbnail;
}
function instagrabber_image_thumbnail_url($post_id = ''){
	echo instagrabber_get_image_thumbnail_url($post_id);
}


function instagrabber_get_image_instagram_url($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->pic_link;
}
function instagrabber_image_instagram_url($post_id = ''){
	echo instagrabber_get_image_instagram_url($post_id);
}


function instagrabber_get_image_time($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->pic_timestamp;
}
function instagrabber_image_time($post_id = ''){
	echo instagrabber_get_image_time($post_id);
}


function instagrabber_get_image_user($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->user_name;
}
function instagrabber_image_user($post_id = ''){
	echo instagrabber_get_image_user($post_id);
}

function instagrabber_get_image_id($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->pic_id;
}
function instagrabber_image_id($post_id = ''){
	echo instagrabber_get_image_id($post_id);
}

function instagrabber_get_image_filter($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return $image->filter;
}
function instagrabber_image_filter($post_id = ''){
	echo instagrabber_get_image_filter($post_id);
}


function instagrabber_image_is_published($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	
	if($image->published == 1){
		return true;
	}else{
		return false;
	}
}

function instagrabber_get_image_location($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return unserialize($image->location);
}

function instagrabber_get_image_tags($post_id = ''){
	$image = instagrabber_get_image_info($post_id);
	return unserialize($image->tags);
}

function instagrabber_get_image_info($post_id = ''){
	global $post;
	$post_id = empty($post_id) ? $post->ID : $post_id;
	$image_id = get_post_meta($post_id , '_instagrabber_image_id', true);
	if($image_id){
		
		return Database::get_images_by_id($image_id);
	}else{
		
		$image_id = get_post_meta($post_id , '_instagrabber_insta_id', true);
		return Database::get_image_by_pic_id($image_id);
	}
	
}

?>