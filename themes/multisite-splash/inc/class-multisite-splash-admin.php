<?php

/**
 * Convert WordPress posts and comments to PDF
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Multisite_Splash_Admin extends Multisite_Splash_Core {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks or shortcodes
	 * Code adapted from the Page Excerpt plugin (http://wordpress.org/extend/plugins/page-excerpt/)
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {

		add_action( 'widgets_init', array( $this, 'widgets' ) );

		// Bail out now if not in admin panel
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu',   array( $this, 'admin_menu' ) );
		add_action( 'admin_init',   array( $this, 'register_setting' ) );
		add_action( 'init',         array( $this, 'excerpts' ) );
	}
	
	/**
	 * Add excerpts for pages
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function widgets() {
		register_sidebar( array(
			'name'          => __( 'Sidebar', 'dss' ),
			'id'            => 'xsidebar',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => "</aside>",
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
	
	/**
	 * Add excerpts for pages
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function excerpts() {
		add_post_type_support( 'page', 'excerpt' );
	}

	/**
	 * Register settings
	 * 
	 * @since 1.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function register_setting() {
		register_setting(
			'ms_splash',
			'ms_splash',
			array( $this, '_options_validate' )
		);
	}

	/**
	 * Validate inputs
	 * Perform security checks on inputted data
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function _options_validate( $input ) {

		// Sort input into storage array format
		$blog_order_input = $input['blog-order'];
		$blog_order_input = str_replace( '&', '', $blog_order_input );
		$blog_order_input = explode( 'list[]=', $blog_order_input );

		// Santise array by casting as integer
		foreach( $blog_order_input as $key => $site_id ) {
			if ( 9999999999 == $site_id ) {
				$end = true;
			}
			if ( ! isset( $end ) && 0 != $site_id ) {
				$blog_order_output[$key] = (int) $site_id;
			}
		}

		// Remove duplicates
		$blog_order_output = array_unique( $blog_order_output );

		// Output via array
		$output['blog-order'] = $blog_order_output;

		return $output;
	}

	/**
	 * Add the admin menu item
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_menu() {

		$page = add_menu_page(
			__( 'Splash page', 'mss' ),   // Page title
			__( 'Splash page', 'mss' ),   // Menu title
			'manage_options',             // Capability
			'splashpage',                 // Menu slug
			array( $this, 'admin_page' ), // The page content
			MSS_URL . '/admin-menu-icon.png',
			5
		);
		add_action( 'admin_print_styles-' . $page,  array( $this, 'print_styles'     ) );
		add_action( 'admin_print_scripts-' . $page, array( $this, 'print_scripts'    ) );

	}

	/**
	 * Print scripts to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function print_scripts() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function print_styles() {
		wp_enqueue_style( 'multisitesplash_style', MSS_URL . '/admin.css' );
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
		#icon-wp2pdf-icon {
			background: url(<?php echo MSS_URL; ?>/admin-icon.png) no-repeat;
		}
		#page-title {
			line-height: 45px;
		}
		</style>
		<div class="wrap">
			<h2 id="page-title"><?php screen_icon( 'wp2pdf-icon' ); _e( 'Splash page', 'mss' ); ?></h2>

			<script>
			jQuery(document).ready(function() {
				jQuery("#post-list").sortable({
					// Need to adjust the hidden input field each time an item is moved ... 
					update: function(event, ui) {
						var blog_order = jQuery('#post-list').sortable('serialize'); // Grab the order
						jQuery('#blog-order').val(blog_order); // Update the hidden input field
					}
				});
			});
			</script>

			<p><?php _e( 'From here you can control what appears on the splash page of the site', 'mss' ); ?></p>
			<form id="dss-form" action="options.php" method="post">
				<p>
					<input type="submit" class="button" id="save" name="save" value="<?php _e( 'Save &raquo;', 'dss_super') ?>" />
				</p>
	
				<h2><?php _e( 'Displayed', 'mss' ); ?></h2>
	
				<ul id="post-list"><?php
	
				global $wpdb;
				$sql = "SELECT blog_id FROM {$wpdb->blogs} WHERE public = 1";
				$total_sites = $wpdb->get_col( $sql );
	
				$blog_order = $this->get_option( 'blog-order' );
	
				foreach( $blog_order as $key => $site_id ) {
					if ( in_array( $site_id, $total_sites ) ) {
						echo $this->list_item( $site_id );
					}
				}
	
				echo '<li id="list_9999999999"><br /><h2>' . __( 'Hidden', 'mss' ) . '</h2></li>';
	
				$list = '';
				foreach( $total_sites as $site_id ) {
					if ( ! in_array( $site_id, $blog_order ) ) {
						echo $this->list_item( $site_id );
					}
				}
				?>
	
				</ul>

				<?php settings_fields( 'ms_splash' ); ?>
				<input type="hidden" id="blog-order" name="ms_splash[blog-order]" value="<?php
				$blog_order = $this->get_option( 'blog-order' );
				foreach( $blog_order as $key => $value ) {
					echo 'list[]=' . $value . '&';
				}
				?>" />

				<p style="clear:both">
					<br />
					<input type="submit" class="button" id="save" name="save" value="<?php _e( 'Save &raquo;', 'dss_super') ?>" />
				</p>
			</form>
			<h2><?php _e( 'Confused? Need help?', 'mss' ); ?></h2>
			<p><?php _e( 'If these instructions do not make sense, please blame Ryan :) I wrote these very quickly after realising that some of the finer details of how this works may not be entirely obvious to new people. Hopefully it is easy enough to grok though.', 'mss' ); ?></p>
			<p><?php _e( 'Dragging the boxes above will allow you to control which sites appear on the splash page.', 'mss' ); ?></p>
			<p><?php _e( 'You can alter the header image displayed on the splash page by altering the "Custom header image" in the general options page for that site. This is different from the regular custom header which will appear by default. The "custom header image" is accessed by going to the administration page for the blog and clicking on "Settings" > "General". Further down the page you will find the custom header input box where you can enter the URL for a new header image. This only affects the splash page and not the blog itself.', 'mss' ); ?></p>
			<p><?php _e( 'On the same administration page you can also alter the blog description, which will also only appear on the splash page. By default, the blogs tagline will be used, but this can be overwritten with this.', 'mss' ); ?></p>
			<p><?php _e( 'You can add text below the header of the splash page by editing the blog description for the blog you are on right now (ie: the splash page blog).', 'mss' ); ?></p>

		</div><?php

	}

	/*
	 * Display list item HTML
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param int $site_id
	 * @return string
	 */
	public function list_item( $site_id ) {
		if ( 0 == $site_id ) {
			return;
		}

		$url = get_blog_option( $site_id, 'siteurl' );
		$name = get_blog_option( $site_id, 'blogname' );
		return '<li id="list_' . $site_id . '">' . $name . ' ' . $site_id . ' <a href="' . $url . '">*</a></li>';
	}

}

