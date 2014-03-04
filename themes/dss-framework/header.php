<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage DSS_Framework
 * @since DSS Framework 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<meta name="DCSext.regj.blogg" content="<?php bloginfo( 'name' ); ?>" />
<title><?php is_home() ? bloginfo('name') : wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php 
	if ( is_singular() ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_head(); 

	// Grab URL of header link
	$options = get_option( 'dss_super' );
	if ( ! isset( $options['header-link'] ) ) {
		$options['header-link'] = '';
	}
	if ( '' != $options['header-link'] ) {
		$header_link = $options['header-link'];
	} else {
		$header_link = home_url( '/' );
	}

?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="hfeed">
	<header id="branding" role="banner">
		<a href="<?php echo esc_url( $header_link ); ?>">
			<img id="logo" src="<?php
				$logo_url = get_template_directory_uri() . '/images/logo.png';
				if ('stat' == dss_get_theme_option( 'theme_sender' ) ) {
					$logo_url = get_template_directory_uri() . '/images/riksvapen.jpg';
				}

				$logo_url = apply_filters( 'dss_logo_url', $logo_url );
				echo $logo_url;
			?>" alt="Regjeringen logo" />
		</a>
		<div id="header-content">
			<a class="top-menu-link" href="http://blogg.regjeringen.no/personvern/">Personvern</a><?php

			/*
			 * If 'top-menu' menu slug is specified, then it will pull
			 * that menu out and display it in place of the default
			 * regjeringen.no link
			 */
			$menu_name = 'top-menu';
			$locations = get_nav_menu_locations();
			$top_menu_object = wp_get_nav_menu_object( $menu_name );
			if ( isset( $top_menu_object->term_id ) ) {
				$top_menu_items = wp_get_nav_menu_items( $top_menu_object->term_id );
				$top_menu_links = '';
				$count = 0;
	
				foreach ( (array) $top_menu_items as $key => $top_menu_item ) {
					$title = $top_menu_item->title;
					$url = $top_menu_item->url;
					if ( '' != $url ) {
						$top_menu_links .= '<a class="top-menu-link" id="top-menu-item-' . $count . '" href="' . $url . '">' . $title . "</a>\n";
					}
					$count++;
				}
			}
			
			// If no menu specified, then defaults to link to regjeringen.no
			if ( empty( $top_menu_links ) )
				$top_menu_links = '<a class="top-menu-link" href="http://regjeringen.no/">regjeringen.no</a>';

			// Display the list of links
			echo apply_filters( 'dss_top_menu', $top_menu_links );

			// Display the search form, only if "Disable Search" plugin is not activated
			if ( ! class_exists( 'c2c_DisableSearch' ) )
				get_search_form();

			// Display site title
			if ( '' != dss_get_theme_option( 'theme_heading' ) ) {
				echo '<h1 id="site-title">';

				echo '<a href="' . $header_link . '">';

				// Theme name
				echo dss_get_theme_option( 'theme_heading' );

				echo '</a>';

				echo '</h1>';
			}

			// Display site description
			if ( get_bloginfo( 'description' ) ) {
			?>
			<h2 id="site-description"><?php echo get_bloginfo( 'description' ); ?></h2><?php
			} ?>
		</div><?php


			// Check to see if the header image has been removed
			$header_image = get_header_image();
			if ( $header_image ) :
				// We need to figure out what the minimum width should be for our featured image.
				// This result would be the suggested width if the theme were to implement flexible widths.
				$header_image_width = get_theme_support( 'custom-header', 'width' );
				$header_url = apply_filters( 'dss_header_url', home_url( '/' ) );

		?>
		<a id="header-image" href="<?php echo esc_url( $header_url ); ?>">
			<?php
				// The header image
				// Check if this is a post or page, if it has a thumbnail, and if it's a big one
				$header_image_width  = get_custom_header()->width;
				$header_image_height = get_custom_header()->height;
			?>
			<img src="<?php header_image(); ?>" width="<?php echo $header_image_width; ?>" height="<?php echo $header_image_height; ?>" alt="" />
		</a>
		<?php endif; // end check for removed header image

		// Display the site heading
		if ( '' != get_bloginfo( 'name', 'display' ) ) {
			echo '<div id="site-heading-wrapper"><h2 id="site-heading">';
			$name = get_bloginfo( 'name' );
			$name = apply_filters( 'dss_site_heading', $name );
			echo $name;
			echo '</h2></div>';
		}

		// Hide main menu if set via DSS Super Admin plugin
		global $super_admin;

		// Ensure DSS Super Admin is available - or show error
		if ( NULL == $super_admin ) {
			wp_die( "</header><div id=\"main\"><pre style=\"color:red\">You must enable the DSS Super Admin plugin</pre>" );
		}

		if ( true != $super_admin->get_option( 'hide-main-menu' ) ) : ?>
		<nav id="access" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Main menu', 'dss' ); ?></h3>
			<?php /* Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
			<div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'dss' ); ?>"><?php _e( 'Skip to primary content', 'dss' ); ?></a></div>
			<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'dss' ); ?>"><?php _e( 'Skip to secondary content', 'dss' ); ?></a></div>
			<?php /* Our navigation menu. If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assigned to the primary location is the one used. If one isn't assigned, the menu with the lowest ID is used. */ ?>
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #access --><?php
		endif; ?>

	</header><!-- #branding -->


	<div id="main">
