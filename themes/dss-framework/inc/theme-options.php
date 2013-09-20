<?php
/**
 * DSS Framework Theme Options
 *
 * @package WordPress
 * @subpackage DSS_Framework
 * @since DSS Framework 1.0
 */

/**
 * Properly enqueue styles and scripts for our theme options page.
 *
 * This function is attached to the admin_enqueue_scripts action hook.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 *
 */
function dss_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'dss-theme-options', get_template_directory_uri() . '/inc/theme-options.css', false, '2011-04-28' );
	wp_enqueue_script( 'dss-theme-options', get_template_directory_uri() . '/inc/theme-options.js', array( 'farbtastic' ), '2011-06-10' );
	wp_enqueue_style( 'farbtastic' );
}
add_action( 'admin_print_styles-appearance_page_theme_options', 'dss_admin_enqueue_scripts' );

/**
 * Register the form setting for our dss_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, dss_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_theme_options_init() {

	register_setting(
		'dss_options',       // Options group, see settings_fields() call in dss_theme_options_render_page()
		'dss_theme_options', // Database option, see dss_get_theme_option()
		'dss_theme_options_validate' // The sanitization callback, see dss_theme_options_validate()
	);

	// Register our settings field group
	add_settings_section(
		'general', // Unique identifier for the settings section
		'', // Section title (we don't want one)
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see dss_theme_options_add_page()
	);
}
add_action( 'admin_init', 'dss_theme_options_init' );

/**
 * Change the capability required to save the 'dss_options' options group.
 *
 * @see dss_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see dss_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * By default, the options groups for all registered settings require the manage_options capability.
 * This filter is required to change our theme options page to edit_theme_options instead.
 * By default, only administrators have either of these capabilities, but the desire here is
 * to allow for finer-grained control for roles and users.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function dss_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_dss_options', 'dss_option_page_capability' );

/**
 * Returns an array of layout options registered for DSS Framework.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_layouts() {
	$layout_options = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content on left', 'dss' ),
			'thumbnail' => get_template_directory_uri() . '/inc/images/content-sidebar.png',
		),
		'content' => array(
			'value' => 'content',
			'label' => __( 'One-column, no sidebar', 'dss' ),
			'thumbnail' => get_template_directory_uri() . '/inc/images/content.png',
		),
	);

	return apply_filters( 'dss_layouts', $layout_options );
}

/**
 * Add a customize link to the menu
 *
 * @since DSS Framework 1.0
 */
function dss_theme_options_add_page() {

	$theme_page = add_theme_page(
		__( 'Customize', 'dss' ), // Name of page
		__( 'Customize', 'dss' ), // Label in menu
		'edit_customise',         // Capability required
		'redirect_to_customiser', // Menu slug, used to uniquely identify the page
		'dss_empty_function'      // Empty callback since not actually rendering a page
	);
}
add_action( 'admin_menu', 'dss_theme_options_add_page' );

/**
 * Empty function
 * Used for when callback is required, but not used
 *
 * @todo Figure out if WordPress has a function like this built in and if so, replace this with that
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_empty_function() {
	return;
}

/**
 * Redirect to customise page
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_customise_redirect_page() {

	// Check if query var present and if in admin
	if ( isset( $_GET['page'] ) && is_admin() ) {
		// If query var set correctly, then redirect to customiser
		if ( 'redirect_to_customiser' == $_GET['page'] ) {
			wp_redirect( home_url( '/wp-admin/customize.php' ), 302 );
			exit;
		}
	}

}
add_action( 'admin_init', 'dss_customise_redirect_page' );

/**
 * Returns an array of layout options registered for DSS Framework.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_sidebar_color() {
	$sidebar_color_options = array(
		'gray' => array(
			'value' => 'gray',
			'label' => __( 'Lys grå', 'dss' ),
		),
		'blue' => array(
			'value' => 'blue',
			'label' => __( 'Lys blå', 'dss' ),
		),
		'yellow' => array(
			'value' => 'yellow',
			'label' => __( 'Lys gul', 'dss' ),
		),
		'green' => array(
			'value' => 'green',
			'label' => __( 'Lys grønn', 'dss' ),
		),
		'orange' => array(
			'value' => 'orange',
			'label' => __( 'Lys orange', 'dss' ),
		),
		'white' => array(
			'value' => 'white',
			'label' => __( 'White', 'dss' ),
		),
	);
return $sidebar_color_options;
	return apply_filters( 'dss_sidebar_color', $sidebar_color_options );
}

/**
 * Returns the default options for DSS Framework.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_get_default_theme_options() {
	$default_theme_options = array(
		'sidebar_color'     => 'white',
		'theme_layout'      => 'content-sidebar',
		'footer_text'       => 'Some footer text goes here!',
		'comments_position' => 'below-comments',
		'main_top_widgets'  => '',
	);

	if ( is_rtl() )
 		$default_theme_options['theme_layout'] = 'sidebar-content';

	return apply_filters( 'dss_default_theme_options', $default_theme_options );
}

/**
 * Returns options for the DSS Framework
 * If no option specified, then dumps out the entire array
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_get_theme_option( $option='' ) {
	$options = get_option( 'dss_theme_options', dss_get_default_theme_options() );

	if ( '' != $option )
		return $options[$option];
	else
		return $options;
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see dss_theme_options_init()
 * @todo set up Reset Options action
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_theme_options_validate( $input ) {
	$output = $defaults = dss_get_default_theme_options();

	// Theme layout must be in our array of theme layout options
	if ( isset( $input['theme_layout'] ) && array_key_exists( $input['theme_layout'], dss_layouts() ) )
		$output['theme_layout'] = $input['theme_layout'];

	// Comments position must be in our array of comment position options
	if ( isset( $input['comments_position'] ) && array_key_exists( $input['comments_position'], dss_comments_positions() ) )
		$output['comments_position'] = $input['comments_position'];

	// Comments position must be in our array of comment position options
	if ( isset( $input['main_top_widgets'] ) )
		$output['main_top_widgets'] = (bool) $input['main_top_widgets'];

	// Sidebar color must be 3 or 6 hexadecimal characters
	if ( isset( $input['sidebar_color'] ) && array_key_exists( $input['sidebar_color'], dss_sidebar_color() ) )
		$output['sidebar_color'] = $input['sidebar_color'];

	// Sanitize theme heading
	$output['theme_heading'] = wp_kses( $input['theme_heading'], '', '' );

	// Sanitize footer text
	$output['footer_text'] = wp_kses( $input['footer_text'], dss_allowed_html(), '' );

	// Sanitize header image url
	$output['heading_text'] = esc_url( $input['heading_text'] );

	return apply_filters( 'dss_theme_options_validate', $output, $input, $defaults );
}

/**
 * Add a style block to the theme for the current link color.
 *
 * This function is attached to the wp_head action hook.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_allowed_html() {
	$allowed_html = array(
		'a' => array(
			'href'  => array(),
			'title' => array(),
			'class' => array(),
			'id'    => array(),
		),
		'br'        => array(),
		'em'        => array(),
		'strong'    => array()
	);
	return $allowed_html;
}

/**
 * Add a style block to the theme for the current link color.
 *
 * This function is attached to the wp_head action hook.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_print_style() {
	?>
	<style><?php

		// Use image if one set for the header text, otherwise default to using @font-face
		if ( '' != dss_get_theme_option( 'heading_text' ) ) { ?>
		/* =Site title
		----------------------------------------------- */
		#site-title {
			background: url(<?php echo dss_get_theme_option( 'heading_text' ); ?>) no-repeat;
			text-indent: -999em;
		}
		#site-title a {
			display: block;
		}<?php } else { ?>

		/* =Add fonts
		----------------------------------------------- */
		@font-face {
			font-family: 'ScalaBold';
			src: url(<?php echo get_template_directory_uri(); ?>/fonts/scala-bold.eot);
			src: url(<?php echo get_template_directory_uri(); ?>/fonts/scala-bold.eot?#iefix) format('embedded-opentype'),
				 url(<?php echo get_template_directory_uri(); ?>/fonts/scala-bold.woff) format('woff'),
				 url(<?php echo get_template_directory_uri(); ?>/fonts/scala-bold.ttf) format('truetype'),
				 url(<?php echo get_template_directory_uri(); ?>/fonts/scala-bold.svg#ScalaBold) format('svg');
			font-weight: normal;
			font-style: normal;
		}<?php
		}?>

		/* =Sidebar
		----------------------------------------------- */
		#secondary {
			background-color: #<?php
				if ( 'gray' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'eef0f1';
				elseif ( 'blue' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'e6f4fb';
				elseif ( 'yellow' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'f5f5dd';

				elseif ( 'green' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'eff9df';

				elseif ( 'orange' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'f8ecd3';
				elseif ( 'white' == dss_get_theme_option( 'sidebar_color' ) )
					$color = 'ffffff';
				echo $color;
			?>;
		}
	</style>
<?php
}
add_action( 'wp_head', 'dss_print_style' );

/**
 * Adds DSS Framework layout classes to the array of body classes.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_layout_classes( $existing_classes ) {

	if ( in_array( dss_get_theme_option( 'theme_layout' ), array( 'content-sidebar', 'sidebar-content' ) ) )
		$classes = array( 'two-column' );
	else
		$classes = array( 'one-column' );

	if ( 'content-sidebar' == dss_get_theme_option( 'theme_layout' ) )
		$classes[] = 'right-sidebar';
	elseif ( 'sidebar-content' == dss_get_theme_option( 'theme_layout' ) )
		$classes[] = 'left-sidebar';
	else
		$classes[] = dss_get_theme_option( 'theme_layout' );

	$classes = apply_filters( 'dss_layout_classes', $classes, dss_get_theme_option( 'theme_layout' ) );

	return array_merge( $existing_classes, $classes );
}
add_filter( 'body_class', 'dss_layout_classes' );

/**
 * Implements DSS Framework theme options into Theme Customizer
 *
 * Note: This function is modified from the Twenty Eleven theme
 * 
 * @param $wp_customize Theme Customizer object
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_customize_register( $wp_customize ) {
	$options  = dss_get_theme_option();
	$defaults = dss_get_default_theme_options();

	// Theme Footer
	$wp_customize->add_setting( 'dss_theme_options[footer_text]', array(
		'default'    => '<a href="http://blogg.regjeringen.no/" title="' . __( 'Website by DSS', 'dss' ) . '" rel="generator">' . __( 'Website by DSS', 'dss' ) . '</a>',
		'type'       => 'option',
		'capability' => 'edit_theme_options',
	) );
	$wp_customize->add_section( 'footer_text', array(
		'title'      => __( 'Footer', 'dss' ),
		'priority'   => 120,
	) );
	$wp_customize->add_control( 'dss_theme_options[footer_text]', array(
		'section'    => 'footer_text',
		'label'      => __( 'Footer text', 'dss' ),
		'type'       => 'text',
	) );

	// Default Layout
	$wp_customize->add_section( 'dss_layout', array(
		'title'      => __( 'Layout', 'dss' ),
		'priority'   => 70,
	) );
	$wp_customize->add_setting( 'dss_theme_options[theme_layout]', array(
		'type'              => 'option',
		'default'           => $defaults['theme_layout'],
		'sanitize_callback' => 'sanitize_key',
	) );
	$layouts = dss_layouts();
	$choices = array();
	foreach ( $layouts as $layout ) {
		$choices[$layout['value']] = $layout['label'];
	}
	$wp_customize->add_control( 'dss_theme_options[theme_layout]', array(
		'section'    => 'dss_layout',
		'type'       => 'radio',
		'choices'    => $choices,
	) );

	// Add comments position
	$wp_customize->add_setting( 'dss_theme_options[comments_position]', array(
		'type'              => 'option',
		'default'           => $defaults['comments_position'],
		'sanitize_callback' => 'sanitize_key',
	) );
	$positions = dss_comments_positions();
	$choices = array();
	foreach ( $positions as $position ) {
		$choices[$position['value']] = $position['label'];
	}
	$wp_customize->add_control( 'dss_theme_options[comments_position]', array(
		'section'    => 'dss_layout',
		'label'    => __( 'Position of the comments field', 'dss' ),
		'type'       => 'radio',
		'choices'    => $choices,
	) );

	$wp_customize->add_setting( 'dss_theme_options[main_top_widgets]', array(
		'type'              => 'option',
		'default'           => $defaults['main_top_widgets'],
		'sanitize_callback' => 'sanitize_key',
	) );
	$wp_customize->add_control( 'dss_theme_options[main_top_widgets]', array(
		'section'    => 'dss_layout',
		'label'    => __( 'Display "Main Top" widget area only on front page.', 'dss' ),
		'type'       => 'checkbox',
	) );

	// Sidebar colors
	$wp_customize->add_section(
		'dss_sidebar_color',
		array(
			'title'    => __( 'Sidebar', 'dss' ),
			'priority' => 110,
		)
	);
	$wp_customize->add_setting(
		'dss_theme_options[sidebar_color]',
		array(
			'type'              => 'option',
			'default'           => $defaults['sidebar_color'],
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$colors = dss_sidebar_color();
	$choices = array();
	foreach ( $colors as $color ) {
		$choices[$color['value']] = $color['label'];
	}
	$wp_customize->add_control( 'dss_theme_options[sidebar_color]', array(
		'section'    => 'dss_sidebar_color',
		'label'    => __( 'The background color of the sidebar', 'dss' ),
		'type'       => 'select',
		'choices'    => $choices,
	) );

	// Heading text image
	$wp_customize->add_section(
		'header_image',
		array(
			'title'    => __( 'Header', 'dss' ),
			'priority' => 10,
		)
	);
	$wp_customize->add_setting(
		'dss_theme_options[heading_text]',
		array(
			'default'    => '',
			'type'       => 'option',
			'capability' => 'edit_theme_options',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'dss_theme_options[heading_text]', array(
		'label'   => __( 'Heading Text', 'dss' ),
		'section' => 'header_image',
		'settings'   => 'dss_theme_options[heading_text]',
	) ) );

	// Theme heading
	$wp_customize->add_setting( 'dss_theme_options[theme_heading]', array(
		'default'    => 'Hello',
		'type'       => 'option',
		'capability' => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'dss_theme_options[theme_heading]', array(
		'section'    => 'header_image',
		'label'      => __( 'Header text', 'dss' ),
		'type'       => 'text',
	) );

}
add_action( 'customize_register', 'dss_customize_register' );

/**
 * Comments positions
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_comments_positions() {
	$positions = array(
		'above-comments' => array(
			'value' => 'above-comments',
			'label' => __( 'Above comments', 'dss' ),
		),
		'below-comments' => array(
			'value' => 'below-comments',
			'label' => __( 'Below comments', 'dss' ),
		),
	);

	return apply_filters( 'dss_comment_positions', $positions );
}

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_customize_preview_js() {
	wp_enqueue_script( 'dss-customizer', get_template_directory_uri() . '/inc/theme-customizer.js', array( 'customize-preview' ), '20120523', true );
}
add_action( 'customize_preview_init', 'dss_customize_preview_js' );

/**
 * Adding extra helpful information to the customizer
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_widgets_customizer_information() {
	echo '<li>
		<p>' . __( 'Your website features widgets which can be drag and dropped into either the sidebar, or footer of your site. Head over to the <strong><a href="' . admin_url( '/widgets.php' ) . '">widgets page</a></strong> to use this feature.', 'dss' ) . '</p>
	</li>';
}
add_action( 'customize_render_control_dss_theme_options[sidebar_color]', 'dss_widgets_customizer_information' );

/**
 * Adding extra helpful information to the navigation customiser
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_navigation_information() {
	echo '<li><p>' . __( 'To edit the contents of your menu(s), please visit the', 'dss' ) . ' <a href="' . admin_url( '/nav-menus.php' ) . '">' . __( 'menus administration page', 'dss' ) . '</a>.</p></li>';
}
add_action( 'customize_render_control_nav_menu_locations[primary]', 'dss_navigation_information' );

/**
 * Hides the initial customizer code which displays the theme in use
 * This box is irrelevant if the users of the customizer do not have the ability
 * to change their theme, hence this code is useful for removing it
 *
 * @todo Write new hook for core which allows for dettaching the code entirely rather than hiding it
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_hide_customizer_blurb() {
	echo '<style>#customize-info {display:none;}</style>';
}
add_action( 'customize_controls_print_styles', 'dss_hide_customizer_blurb' );
