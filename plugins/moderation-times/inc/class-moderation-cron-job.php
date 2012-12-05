<?php

/**
 * Moderation Cron Job
 *
 * Adds WP Cron for periodic checking and updating of the moderation option field
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Moderation_Cron_Job extends Moderation_Times {

	/**
	 * Class constructor
	 * 
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'scheduled_event', array( $this, 'check_setting' ) );
		add_filter( 'cron_schedules',  array( $this, 'add_schedule_option' ) );
		register_activation_hook(   MODERATION_TIMES_DIR . 'index.php', array( $this, 'activation'   ) );
		register_deactivation_hook( MODERATION_TIMES_DIR . 'index.php', array( $this, 'deactivation' ) ); 
	}

	/**
	 * Add new option to scheduling array
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function add_schedule_option( $schedules ) {
		$schedules['every-five-minutes'] = array(
			'interval' => 300, // Number of seconds in five minutes
			'display'  => __( 'Once every five minutes', 'moderation_times' )
		);
		return $schedules;
	}

	/**
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function activation() {
		wp_schedule_event( time(), 'every-five-minutes', 'scheduled_event' );
	}

	/**
	 * Turning off cron on plugin deactivation
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function deactivation() {
		wp_clear_scheduled_hook( 'scheduled_event' );
	}

}
