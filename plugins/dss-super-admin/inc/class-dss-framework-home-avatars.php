<?php

/**
 * Display author avatars in listings
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class DSS_Framework_Home_Avatars extends DSS_Network_Super_Admin {

	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( true != $this->get_option( 'avatar-listings' ) )
			return;

		add_filter( 'wp_head',        array( $this, 'css' ) );
		add_filter( 'dss_thumbnails', array( $this, 'thumbnails' ) );
	}

	/**
	 * Add CSS for styling post area
	 */
	public function css() {
		echo '<style>
/* Thumbnails */
.post-thumbnail {
	width: 90px;
}
body .no-thumbnail .entry-header {
	margin-left: 0; /* We do not want white space appearing when no thumbnail is set */
}
body.home .no-thumbnail .entry-header,
.search-results .entry-header,
.archive .entry-header,
.home .entry-header {
	margin-left: 130px;
	min-height: 90px;
}
.post-content {
	margin-left: 0;
}
.singular .entry-header,
.single .entry-header {
	margin-left: 0;	
}
.entry-date {
	display: block;
}
</style>';
	}

	/*
	 * Adds category specific site headings when on category or single posts pages
	 * Intended for use only with the DSS Framework theme
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @global int $post
	 */
	function thumbnails( $thumb ) {
		global $post;
	
		if ( is_front_page() ) {
			$thumb = get_avatar( $post->post_author, 75 );
		}
		return $thumb;
	}

}
