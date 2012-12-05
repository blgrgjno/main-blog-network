<?php

/**
 * Moderation timer
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Moderation_Times {

	/**
	 * @var $default_times
	 * @desc Default times set in DB
	 * @access private
	 */
	private $default_times = array(
		'weekdays_start'   => '8:00',
		'weekdays_finish'  => '22:00',
		'saturdays_start'  => '10:00',
		'saturdays_finish' => '17:00',
		'sundays_start'    => '',
		'sundays_finish'   => '',
		'holidays_start'   => '',
		'holidays_finish'  => '',
	);

	/**
	 * @var $holidays
	 * @desc List of holidays
	 * @access private
	 */
	private $holidays = array(
		'11/6/2022',
		'15/6/2022',
		'17/6/2022',
		'19/6/2022',
		'21/6/2022',
	);

	/**
	 * Class constructor
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		// Add option
		add_option( 'moderation_times', $this->default_times );

		// Add actions
		add_action( 'admin_init',         array( $this, 'register_settings'  ) );
		add_action( 'admin_menu',         array( $this, 'add_page'  ) );

		// If we want to manually fire the cron, then ping a specific URL
		if ( isset( $_GET['manually_trigger_moderation_timer_cron'] ) ) {
			add_action( 'init',               array( $this, 'check_setting'  ) );
		}
	}

	/**
	 * Check comment moderation setting and change if needed
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function check_setting() {

		// Calculate time/dates
		$current_time_value = current_time( 'timestamp' );
		$current_time_string = date( 'H:i', $current_time_value );
		$current_date_string = date( 'j/n/Y', $current_time_value );

		echo 'Current time string: ' . $current_time_string . '<br>';
		echo 'Current date string: ' . $current_date_string . '<br>';

		
		// Set which type of day we're on
		if ( date( 'w', $current_time_value ) == 6 ) {
			$day = 'saturdays';
		} elseif ( date( 'w', $current_time_value ) == 7 ) {
			$day = 'sundays';
		} else {
			// Loop through each holiday and check if we're on one of them
			foreach( $this->holidays as $key => $value ) {
				if ( $current_date_string == $value )
					$day = 'holidays';
			}
			// If nothing else left, then default to being a weekday
			if ( !isset( $day ) )
				$day = 'weekdays';
		}

		// Check if we're in an appropriate time zone
		$start  = strtotime( $this->get_option( $day . '_start' ) );
		$finish = strtotime( $this->get_option( $day . '_finish' ) );

		if ( '' == $start || '' == $finish ) {
			update_option( 'comment_moderation', true );
			echo 'Start or finish time not set, so comments will need to be approved.';
		}
		elseif ( $current_time_value > $start && $current_time_value < $finish ) {
			update_option( 'comment_moderation', false );
			echo 'Comments will automatically appear.';
		}
		elseif ( ( $current_time_value > $start || $current_time_value < $finish ) ) {
			update_option( 'comment_moderation', true );
			echo 'Posts will need to be approved.';
		}

		echo '<br /><br />';
		die( __( 'Moderation mode reset!', 'moderation_times' ) );
	}

	/**
	 * Init plugin options to white list our options
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function register_settings(){
		register_setting(
			'moderation_times',
			'moderation_times',
			array( $this, 'validate' )
		);
	}

	/**
	 * Load up the menu page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function add_page() {
		add_options_page(
			__( 'Moderation times', 'moderation_times' ),
			__( 'Moderation times', 'moderation_times' ),
			'edit_moderation_times',
			'moderation_times',
			array( $this, 'do_page' )
		);

	}

	/**
	 * Create the options page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 */
	public function do_page() {
	
		if ( ! isset( $_REQUEST['settings-updated'] ) )
			$_REQUEST['settings-updated'] = false;

		?>
		<div class="wrap">
			<?php screen_icon(); echo '<h2>' . __( 'Moderation times', 'moderation_times' ) . '</h2>'; ?>

			<form method="post" action="options.php"><?php

				// Add hidden input fields (nonces etc.)
				settings_fields( 'moderation_times' ); ?>

				<p><?php _e( 'This plugin adds the ability to turn comment moderation on and off at specific times of day.', 'moderation_times' ); ?></p><?php

				/**
				 * Time slots
				 */
				?>
				<h2><?php _e( 'Time slots', 'moderation_times' ); ?></h2>
				<p><?php _e( 'Enter the various time slots for comment moderation below.', 'moderation_times' ); ?></p>
				<table class="form-table"><?php
					$options = array(
						'saturdays' => __( 'Saturday', 'moderation_times' ),
						'sundays'   => __( 'Sunday', 'moderation_times' ),
						'weekdays'  => __( 'Weekday', 'moderation_times' ),
						'holidays'  => __( 'Holiday', 'moderation_times' ),
					);
					foreach( $options as $key => $value ) { ?>
					<tr valign="top">
						<th scope="row"><?php echo $value; ?></th>
						<td>
							<p>
								<input id="moderation_times_<?php echo $key; ?>_start" class="regular-text" type="text" name="moderation_times[<?php echo $key; ?>_start]" value="<?php echo $this->get_option( $key . '_start' ); ?>" />
								<label class="description" for="moderation_times_<?php echo $key; ?>_start"><?php _e( 'Start', 'moderation_times' ); ?></label>
								<br />
								<input id="moderation_times_<?php echo $key; ?>_finish" class="regular-text" type="text" name="moderation_times[<?php echo $key; ?>_finish]" value="<?php echo $this->get_option( $key . '_finish' ); ?>" />
								<label class="description" for="moderation_times_<?php echo $key; ?>_finish"><?php _e( 'Finish', 'moderation_times' ); ?></label>
							</p>
						</td>
					</tr><?php
					}
					?>
				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'moderation_times' ); ?>" />
				</p>
				<p>
					<small>
						<?php _e( 'The plugin automatically checks the settings and updates the moderation toggle once every five minutes. If you need to force it to check at a particular time, then visit the "<a href="' . home_url( '/?manually_trigger_moderation_timer_cron=yup' ) . '">Manually set moderation mode</a>" link.', 'moderation_times' ); ?>
					</small>
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Get option from array
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function get_option( $key ) {
		$options = get_option( 'moderation_times' );
		if ( isset( $options[$key] ) )
			return $options[$key];
	}

	/**
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since 1.0
	 * @access public
	 * @todo Remove hard coded junk
	 */
	public function validate( $input ) {
		$output['weekdays_start']   = wp_kses( $input['weekdays_start'], '', '' );
		$output['weekdays_finish']  = wp_kses( $input['weekdays_finish'], '', '' );
		$output['saturdays_start']  = wp_kses( $input['saturdays_start'], '', '' );
		$output['saturdays_finish'] = wp_kses( $input['saturdays_finish'], '', '' );
		$output['sundays_start']    = wp_kses( $input['sundays_start'], '', '' );
		$output['sundays_finish']   = wp_kses( $input['sundays_finish'], '', '' );
		$output['weekdays_start']   = wp_kses( $input['weekdays_start'], '', '' );
		$output['weekdays_finish']  = wp_kses( $input['weekdays_finish'], '', '' );
		$output['holidays_start']   = wp_kses( $input['holidays_start'], '', '' );
		$output['holidays_finish']  = wp_kses( $input['holidays_finish'], '', '' );
		return $output;
	}

}
