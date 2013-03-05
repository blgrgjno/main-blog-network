<?php

/**
 * Plugin Name: Profile widget
 * Plugin URI: http://geek.ryanhellyer.net/products/profile-widget/
 * Description: A widget for displaying user profile information on single post pages
 * Version: 1.0.2
 * Author: Ryan Hellyer / Metronet
 * Author URI: http://geek.ryanhellyer.net/
 *
 * The implementation of widget code is based on work by Justin Tadlock
 * http://justintadlock.com/archives/2009/05/26/the-complete-guide-to-creating-widgets-in-wordpress-28
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */


/**
 * Register the widget
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function profwid_load_widgets() {
	register_widget( 'Profile_Widget' );
}
add_action( 'widgets_init', 'profwid_load_widgets' );

/**
 * Profile Widget class.
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
class Profile_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'profwid', 'description' => __( 'Widget for display user bio information', 'profwid' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'profwid-widget' );

		/* Create the widget. */
		$this->WP_Widget(
			'profwid-widget',
			__( 'Profile Widget', 'profwid' ),
			$widget_ops, $control_ops
		);
	}

	/**
	 * How to display the widget on the screen.
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @global int $post
	 * @param array $args     Contains the arguments, but is disused here
	 * @param array $instance Contains the widget settings
	 */
	public function widget( $args, $instance ) {
		global $post;
		extract( $args );

		// Check if widget should be displayed here (contains additional logic to allow for overriding via themes and other plugins)
		if ( ! is_single() )
			$return = true;
		$return = apply_filters( 'profwid_confirm_display' ); // Used to modify result via plugins
		if ( true == $return )
			return;

		// Set the title
		$title = apply_filters( 'widget_title', $instance['title'] );

		// Display before widget code
		echo $before_widget;

		// Display the widget title
		if ( $title )
			echo $before_title . $title . $after_title;

		echo get_avatar( $post->post_author, $instance['size'] );
		$description = get_the_author_meta( 'description', $post->post_author );
		$description = wptexturize( $description ); // Apply texturising filter - not using the_content() due to it causing things like share icons to be displayed in the sidebar
		$description = wpautop( $description ); // Apply auto paragraph filter - not using the_content() due to it causing things like share icons to be displayed in the sidebar
		echo $description;

		// Display after widget code
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 * Sanitise data inputs
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function update( $input, $old ) {

		// Sanitise data input
		$output['title'] = wp_kses( $input['title'], '', '' );
		$output['size'] = (int) $input['size'];

		return $output;
	}

	/**
	 * Displays the form on the widget page
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function form( $instance ) {

		// Set up some default widget settings
		$defaults = array(
			'title' => __( 'Profile', 'profwid'),
			'size'  => '120',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'profwid' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size:', 'profwid' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo $instance['size']; ?>" style="width:100%;" />
		</p><?php
	}
}
