<?php
/**
 * @package WordPress
 * @subpackage Metronet Admin
 *
 * @since 0.1
 *
 */


/**
 * Metronet Admin Dashboard widgets
 * 
 * @copyright Copyright (c), PixoPoint
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 0.1
 */
class Mn_Admin_Dashboard_Widgets {

	/**
	 * Constructor
	 * Add methods to appropriate hooks
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'admin_add_dashboard_widgets' ) );
	}

	/**
	 * Add dashboard widgets
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function admin_add_dashboard_widgets() {

		// Add custom feed
		wp_add_dashboard_widget(
			'mn_dashboard_custom_feed',
			__( 'Latest Posts from blogg.regjeringen', 'mn_admin' ),
			array( $this, 'dashboard_custom_feed_output' )
		);
	}

	/**
	 * New dashboard widget
	 * Creates the custom dashboard feed RSS box
	 * 
	 * @since 0.1
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function dashboard_custom_feed_output() {

		echo '<div class="rss-widget" id="wppb-rss-widget">';
		wp_widget_rss_output(
			array(
				'url'           => 'http://blogg.regjeringen.no/feed/',
				'title'         => __( 'News from blogg.regjeringen.no', 'mn_admin' ),
				'items'         => 3,
				'show_summary'  => 1,
				'show_author'   => 0,
				'show_date'     => 1
			)
		);
		echo '</div>';
	}
}