<?php
/*

Plugin Name: Export site as HTML
Plugin URI: http://geek.ryanhellyer.net/products/export-site/
Description: Export your site as raw HTML
Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2013 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/




/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@pixopoint.com>
 */
define( 'EXS_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'EXS_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL
define( 'EXS_QUERYVAR', 'export-site' ); // Query var used to access the PDF file




new Export_Site();

/**
 * Convert WordPress posts and comments to PDF
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Export_Site {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks or shortcodes
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {

		// Bail out now if not in admin panel
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		if ( isset( $_GET[EXS_QUERYVAR] ) ) {
			add_action( 'admin_init', array( $this, 'create_site' ) );
		}
	}

	/**
	 * Add the admin menu item
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	function admin_menu() {

		add_management_page(
			__( 'Export site', 'exportsite' ), // Page title
			__( 'Export site', 'exportsite' ), // Menu title
			'manage_options',                  // Capability
			'exportsite',                      // Menu slug
			array( $this, 'admin_page' )       // The page content
		);
	}

	/**
	 * The admin page contents
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @global string $page_content Crude hack to dump meta redirect into admin page
	 */
	public function admin_page() {
		global $page_content;

		?>
		<style type="text/css">
		#icon-exportsite-icon {
			background: url(<?php echo plugins_url( 'admin-icon.png' , __FILE__ ); ?>) no-repeat;
		}
		#page-title {
			line-height: 52px;
		}
		</style>
		<div class="wrap">
			<h2 id="page-title"><?php screen_icon( 'exportsite-icon' ); ?><?php _e( 'Export site as HTML', 'exportsite' ); ?></h2>
			<p><?php _e( 'You can convert the HTML page to PDF by exporting the page via your web browser.', 'exportsite' ); ?></p><?php

			if ( ! isset( $_GET[EXS_QUERYVAR] ) ) :
			$url = admin_url( 'tools.php?page=exportsite&' . EXS_QUERYVAR . '=0' );
			?>
			<p>
				<a href="<?php echo wp_nonce_url( $url, 'exportsite' ); ?>" class="button-primary"><?php _e( 'Export site', 'exportsite' ); ?></a>
			</p><?php
			endif; ?>
		</div>
		<?php

	}

	/**
	 * Create site
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @return string
	 * @global array    $post          Internal WordPress global, used here for processing posts
	 * @global integer  $blog_id       Internal WordPress global, used here to set the file name
	 * @global string   $page_content  Crude hack to allow for creating a meta tag early on, then dumping it out into the middle of the admin page later on
	 * 
	 */
	function create_site() {
		global $post, $blog_id, $page_content;

		// Stop the server from timing out
		set_time_limit( 600 );
		ini_set( 'max_execution_time', 600 );

		// Bail out now if user doesn't have permission to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Nonce check
		check_admin_referer( 'exportsite' );

		// Grab IDs of all posts and pages and place into an array ($the_posts)
		$count = 0;
		$args = array( 'numberposts' => -1 );
		$myposts = get_posts( $args );
		foreach( $myposts as $post ) {
			setup_postdata( $post );
			$the_posts[$count] = $post->ID;
			$count++;
		}

		$args = array( 'numberposts' => -1, 'post_type' => 'page' );
		$myposts = get_posts( $args );
		$the_posts[$count] = '';
		foreach( $myposts as $post ) {
			setup_postdata( $post );
			$the_posts[$count] = $post->ID;
			$count++;
		}

		// 
//		header( 'Content-type: text/plain' );
//		header( 'Content-Disposition: attachment; filename="export-site.html"' );

		?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<meta charset="UTF-8" />
	<title><?php bloginfo( 'title' ); ?></title>
	<style>
		* {
			margin: 0;
			padding: 0;
		}
		.post {
			page-break-after: always
		}
		
		a {
			font-family: sans-serif;
		}
		
		p {
			font-family: sans-serif;
			margin: 0 0 25px;
			font-size: 14px;
			line-height: 18px;
		}
		
		blockquote {
			font-family: sans-serif;
			border: none;
			margin: 2px 2px 20px;
			padding: 5px 30px 1px 70px;
		}
		
		blockquote p {
			font-family: sans-serif;
			color: #999;
			font-style: italic;
		}
		
		
		h1,
		h1 a,
		h1 a:visited {
			font-family: sans-serif;
			font-size: 22px;
		}
		
		h2,
		h2 a,
		h2 a:visited {
			font-family: sans-serif;
			font-size: 20px;
		}
		
		h3 {
			font-family: sans-serif;
			font-size: 18px;
		}
		
		h4 {
			font-family: sans-serif;
			font-size: 17px;
		}
		
		h5 {
			font-family: sans-serif;
			font-size: 16px;
		}
		
		h6 {
			font-family: sans-serif;
			font-size: 15px;
		}
		
		
		/* Ordered / Unordered Lists
		------------------------------------------------------------ */
		
		ol,
		ul {
			margin: 0;
			padding: 0 0 15px;
		}
		
		ul li {
			font-size: 14px;
			list-style-type: square;
			margin: 0 0 0 30px;
			padding: 0;
		}
		
		ol li {
			font-size: 14px;
			margin: 0 0 0 35px;
		}
		
		/* Images
		------------------------------------------------------------ */
		
		img {
			height: auto;
			max-width: 100%;
		}
		
		.avatar,
		.featuredpage img,
		.featuredpost img,
		.post-image {
			background-color: #f5f5f5;
			border: 1px solid #ddd;
			padding: 10px;
		}
		
		.post-image {
			margin: 0 2px 10px 0;
		}
		
		img.centered,
		.aligncenter {
			display: block;
			margin: 0 auto 2px;
		}
		
		img.alignnone {
			display: inline;
			margin: 0 0 2px;
		}
		
		img.alignleft {
			display: inline;
			margin: 0 5px 3px 0;
		}
		
		img.alignright {
			display: inline;
			margin: 0 0 2px 15px;
		}
		
		.alignleft {
			float: left;
			margin: 0 15px 2px 0;
		}
		
		.alignright {
			float: right;
			margin: 0 0 2px 15px;
		}
		
		.wp-caption {
			padding: 2px;
			text-align: center;
		}
		
		p.wp-caption-text {
			font-style: italic;
			margin: 2px 0;
		}
		
		.wp-smiley,
		.wp-wink {
			border: none;
			float: none;
		}
		
		.gallery-caption {
		}
	</style>
</head>
<body>

	<img src="<?php echo EXS_URL; ?>/dss-logo.png" style="width:20%;margin-left:40%;" alt="" />
	<hr />
	<br />

	<p><strong><?php _e( 'Archive of' ); ?> <?php bloginfo( 'title' ); ?>. <?php _e( 'PDF created on' ); ?> <?php echo date( 'Y/m/d' ); ?></p></strong><?php

	foreach( $the_posts as $key => $post_id ) {
		// The Query
		if ( 'page' == get_post_type( $post_id ) ) {
			$args = array( 'page_id' => $post_id );
		}
		elseif ( 'post' == get_post_type( $post_id ) ) {
			$args = array( 'p' => $post_id );
		}
		
		$the_query = new WP_Query( $args );
		global $more; // Declare global $more (before the loop).
		
		// The Loop
		while ( $the_query->have_posts() ) {
			global $wpdb;
			$the_query->the_post();
			$more = 1; // Set (inside the loop) to display all content, including text below more.
			?>
			<div class="post">
				<h2><?php the_title(); ?></h2>
				<?php the_content(); ?>
		
				<?php
				// Display the comments
				$comments = $wpdb->get_results("SELECT *,SUBSTRING(comment_content,1,200) AS com_excerpt FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1' ORDER BY comment_date DESC limit 999");
			
				$comments_output = '';
				foreach ( $comments as $comment ) {
					?>
					<hr />
					<h6><?php _e( 'Comment by' ); ?> <?php echo $comment->comment_author; ?>. <?php _e( 'Comment posted on' ); ?> <?php echo $comment->comment_date; ?>.</h6>
					<p><?php echo $comment->comment_content; ?></p><?php
				}
				?>
			</div><?php
		}
	}
?>

</body>
</html><?php

		exit;

	}


}

