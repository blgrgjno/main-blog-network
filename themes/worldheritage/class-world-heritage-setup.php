<?php

/**
 * World Heritage Setup
 * 
 * @copyright Copyright (c), Metronet
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class WorldHeritageSetup {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks or shortcodes
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * return void
	 */
	public function __construct() {

		// The next four constants set how World Heritage supports custom headers.
		define( 'HEADER_TEXTCOLOR', '' ); // The default header text color
		define( 'HEADER_IMAGE', '' ); // By leaving empty, we allow for random image rotation.

		// The height and width of your custom header.
		// Add a filter to worldheritage_header_image_width and worldheritage_header_image_height to change these values.
		define( 'HEADER_IMAGE_WIDTH', 975 );
		define( 'HEADER_IMAGE_HEIGHT', 288 );

		// Add action hooks
		add_action( 'wp_enqueue_scripts', array( $this,  'css' ) );
		add_action( 'admin_menu',         array( &$this, 'remove_menus' ) );
		add_action( 'init',               array( &$this, 'add_excerpts_to_pages' ) );
		add_action( 'wp_head',            array( $this,  'print_inline_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this,  'external_scripts' ) );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'footer', __( 'Footer Menu', 'worldheritage' ) );

		// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
		add_theme_support( 'post-thumbnails' );

		// We'll be using post thumbnails for custom header images on posts and pages.
		// We want them to be the size of the header image that we just defined
		// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
		set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

		// Add World Heritage's custom image sizes
		add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
		add_image_size( 'article', 975, 9999, true );
		add_image_size( 'home-thumbs', 439, 261, true );

		// Turn on random header image rotation by default.
		add_theme_support( 'custom-header', array( 'random-default' => true ) );

		// Add a way for the custom header to be styled in the admin panel that controls
		// custom headers. See worldheritage_admin_header_style(), below.
		add_custom_image_header( array( $this, 'header_style' ), array( $this, 'blank' ), array( $this, 'blank' ) );

		// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
		register_default_headers( array(
			'default' => array(
				'url'           => '%s/images/thumbs/home-thumb.jpg',
				'thumbnail_url' => '%s/images/thumbs/home-thumb.jpg',
				'description'   => __( 'Willow', 'worldheritage' )
			),
		) );
	}

	/**
     * Blank callback function for use in add_custom_image_header()
	 * This function serves no purpose other than satisfying the pointless requirements of the built in header system.
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
     */
	function blank() {}

	/**
     * Print scripts onto pages
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
     */
	function external_scripts() {
		wp_enqueue_script(
			'colorbox',
			get_template_directory_uri() . '/scripts/jquery.colorbox-min.js',
			array( 'jquery' ),
			1.0,
			true
		);		
	}

	/**
     * Print scripts onto pages
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
     */
	function print_inline_scripts() {

		// HTML5 Shiv for IE
		echo '<!--[if lt IE 9]><script src="' . get_template_directory_uri() . '/scripts/html5.js" type="text/javascript"></script><![endif]-->';

		// Colorbox settings
		echo '<script>jQuery(function($){$("a[href$=\'jpg\'],a[href$=\'jpeg\'],a[href$=\'png\'],a[href$=\'gif\']").colorbox({ maxHeight:\'95%\',opacity:\'0.6\'});});</script>';
	}

    /**
     * Adds excerpts support for pages
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
     */
	function add_excerpts_to_pages() {
		add_post_type_support( 'page', 'excerpt' );
	}

	/**
	* Remove extraneous menus from admin panel
	* 
	* Adapted from PixoPoint Simple CMS plugin
	* 
	* @author Ryan Hellyer <ryan@pixopoint.com>
	* @since 1.0
	*/
	function remove_menus () {

		// If current user can activate plugins (implying they're an admin) then don't execute
		if ( !is_admin() )
			return;

		global $menu;
		/* List of items to remove */
		$restricted = array(
			'Posts',
			'Links',
		);
		end ( $menu );
		while ( prev( $menu ) ) {
			$value = explode( ' ',$menu[key( $menu )][0] );
			if ( in_array( $value[0] != NULL?$value[0] : "", $restricted ) )
				unset( $menu[key( $menu )] ); 
		}
	}

	/*
	 * Adds CSS to front end of site
	 * Must not do anything when on login or admin pages since theme CSS is not needed on thos pages
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function css() {

		// Bail out now if in admin panel or on login page
		if ( is_admin() || strstr( $_SERVER['REQUEST_URI'], 'wp-login.php' ) )
			return;

		wp_enqueue_style( 'colorbox', get_template_directory_uri() . '/colorbox.css', false, '', 'screen' );
		wp_enqueue_style( 'style', get_template_directory_uri() . '/style.css', false, '', 'screen' );
	}

	/**
	 * Callback for custom header 
	 * Normally used for text styling, but since no text used here, then is empty
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since World Heritage 1.0
	 */
	function header_style() {}

	/**
	 * Styles the header image displayed on the Appearance > Header admin panel.
	 *
	 * Referenced via add_custom_image_header() in lassesuper_setup().
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	function admin_header_style() {}

	/**
	 * Custom header image markup displayed on the Appearance > Header admin panel.
	 *
	 * Referenced via add_custom_image_header() in lassesuper_setup().
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since World Heritage 1.0
	 */
	function admin_header_image() { ?>
		<div id="headimg">
			<?php
			if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
				$style = ' style="display:none;"';
			else
				$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
			?>
			<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
			<?php $header_image = get_header_image();
			if ( ! empty( $header_image ) ) : ?>
				<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
			<?php endif; ?>
		</div>
	<?php }

}

