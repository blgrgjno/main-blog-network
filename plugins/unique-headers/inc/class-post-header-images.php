<?php

/**
 * Add post specific header images
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
class Post_Header_Images {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 */
	public function __construct() {

		// Add filters
		add_filter( 'theme_mod_header_image',         array( $this, 'header_image_filter' ) );

	}

	/*
	 * Filter for modifying the output of get_header()
	 *
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @since 1.0
	 * @param string $url The header image URL
	 * @global $post Used for accessing the current post/page ID
	 * @return string
	 */
	public function header_image_filter( $url ) {

		global $post;

		// Bail out now if not in post or page
		if ( !is_single() && !is_page() )
			return $url;

		// Grab current post ID
		$post_ID = $post->ID;

		// Pick post type
		if ( is_single() )
			$slug = 'post';
		else
			$slug = 'page';

		// Grab the post thumbnail ID
		$post_thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id( $slug, 'custom-header', $post_ID );

		// If no post thumbnail ID set, then use default
		if ( '' == $post_thumbnail_id )
			return $url;

		// Grab URL from WordPress
		$url = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
		$url = $url[0];

		return $url;
	}

}
