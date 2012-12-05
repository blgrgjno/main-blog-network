<?php
/*
Plugin Name: Gravatar Widget
Plugin URI: http://wordpress.org/extend/plugins/gravatar-widget
Description: Display a Gravatar in the sidebar of your blog
Version: 1.0
Author: Automattic Inc.
Author URI: http://automattic.com/
*/

class Gravatar_Widget extends WP_Widget {
	function Gravatar_Widget() {
		$widget_ops  = array( 'classname' => 'widget_gravatar', 'description' => __( 'Insert a Gravatar image', 'gravatar-widget' ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );
		
		$this->WP_Widget( 'gravatar', __( 'Gravatar' ), $widget_ops, $control_ops );
	}

	/**
	 * Display the widget
	 *
	 * @param string $args Widget arguments
	 * @param string $instance Widget instance
	 * @return void
	 **/
	function widget( $args, $instance ) {
		extract($args);
	
		$instance = wp_parse_args( (array)$instance, array( 'title' => '', 'gravatar_size' => 128, 'gravatar_align' => 'none', 'gravatar_text' => '', 'email' => '', 'email_user' => -1, 'gravatar_url' => '' ) );
		$title    = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
	
		if ( $title )
			echo $before_title . stripslashes( $title ) . $after_title;

		// Widget
		$text = '';
		if ( $instance['email'] ) {
			if ( $instance['gravatar_url'] )
				$text .= '<a href="'.esc_url( $instance['gravatar_url'], 'display' ).'">';

			$avatar = get_avatar( $instance['email'], $instance['gravatar_size'], '', true );
			
			if ( $instance['gravatar_text'] ) {
				if ( $instance['gravatar_align'] == 'left' )
					$text .= str_replace( '/>', ' style="margin-top: 3px; padding: 0 0.5em 0 0; float: left"/>', $avatar );
				elseif ( $instance['gravatar_align'] == 'right' )
					$text .= str_replace( '/>', ' style="margin-top: 3px; padding: 0 0 0.5em 0; float: right"/>', $avatar );
				elseif ( $instance['gravatar_align'] == 'center' )
					$text .= str_replace( '/>', ' style="display: block; margin: 0 auto;"/><br/>', $avatar );
				else
					$text .= $avatar.'<br /><br />';
			}
			else
				$text .= $avatar;
				
			if ( $instance['gravatar_url'] )
				$text .= '</a>';
		}

		if ( $instance['gravatar_text'] )
			$text .= stripslashes( $instance['gravatar_text'] );

		echo wpautop( $text );
		
		// After
		echo $after_widget;
	}

	/**
	 * Display config interface
	 *
	 * @param string $instance Widget instance
	 * @return void
	 **/
	function form( $instance ) {
		$instance = wp_parse_args( (array)$instance, array( 'title' => '', 'gravatar_size' => 128, 'gravatar_align' => 'none', 'gravatar_text' => '', 'email' => '', 'email_user' => -1, 'gravatar_url' => '' ) );

		$title          = stripslashes( $instance['title'] );
		$gravatar_size  = $instance['gravatar_size'];
		$gravatar_align = $instance['gravatar_align'];
		$gravatar_text  = stripslashes( $instance['gravatar_text'] );
		$gravatar_url   = $instance['gravatar_url'];
		$email          = $instance['email'];
		$email_user     = $instance['email_user'];
		
		$sizes  = array( 64 => __( 'Small (64 pixels)', 'gravatar-widget' ), 96 => __( 'Medium (96 pixels)', 'gravatar-widget' ), 128 => __( 'Large (128 pixels)', 'gravatar-widget' ), 256 => __( 'Extra Large (256 pixels)', 'gravatar-widget' ) );
		$aligns = array( 'none' => __( 'None', 'gravatar-widget' ), 'left' => __( 'Left', 'gravatar-widget' ), 'right' => __( 'Right', 'gravatar-widget' ), 'center' => __( 'Center', 'gravatar-widget' ) );
		?>
<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'gravatar-widget' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

<p><?php _e( 'Select a user or pick <em>custom</em> and enter a custom email address.', 'gravatar-widget' ); ?></p>
<p><?php wp_dropdown_users( array( 'show_option_none' => __( 'Custom', 'gravatar-widget' ), 'selected' => $email_user, 'name' => $this->get_field_name( 'email_user' ) ) );?></p>

<p id="gravatar_email_user"><label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Custom Email Address:', 'gravatar-widget' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php esc_attr_e( $email ); ?>" /></label></p>

<p>
	<label for="<?php echo $this->get_field_id( 'gravatar_size' ); ?>"><?php _e( 'Size:', 'gravatar-widget' ); ?>
		<select id="<?php echo $this->get_field_id( 'gravatar_size' ); ?>" name="<?php echo $this->get_field_name( 'gravatar_size' ); ?>">
			<?php foreach ( $sizes AS $size => $name) : ?>
				<option value="<?php echo $size; ?>"<?php if ( $gravatar_size == $size ) echo ' selected="selected"' ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>
	</label>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'gravatar_align' ); ?>"><?php _e( 'Gravatar alignment:', 'gravatar-widget' ); ?>
		<select id="<?php echo $this->get_field_id( 'gravatar_align' ); ?>" name="<?php echo $this->get_field_name( 'gravatar_align' ); ?>">
			<?php foreach ( $aligns AS $align => $name) : ?>
				<option value="<?php echo $align; ?>"<?php if ( $gravatar_align == $align ) echo ' selected="selected"' ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>
	</label>
</p>
<p><label for="<?php echo $this->get_field_id( 'gravatar_url' ); ?>"><?php _e( 'Gravatar link. This is an optional URL that will be used when anyone clicks on your Gravatar:', 'gravatar-widget' ); ?> <input  class="widefat" id="<?php echo $this->get_field_id('gravatar_url'); ?>" name="<?php echo $this->get_field_name( 'gravatar_url' ); ?>" type="text" value="<?php esc_attr_e( $gravatar_url ); ?>" /></label></p>
<p><label for="<?php echo $this->get_field_id( 'gravatar_text' ); ?>"><?php _e( 'Text displayed after Gravatar. This is optional and can be used to describe yourself or what your blog is about.', 'gravatar-widget' ); ?><br/> <textarea class="widefat" style="font-size: 0.9em" id="<?php echo $this->get_field_id('gravatar_text'); ?>" name="<?php echo $this->get_field_name( 'gravatar_text' ); ?>" rows="5"><?php echo htmlspecialchars( $gravatar_text ); ?></textarea></label></p>
<p><?php _e( 'You can modify your Gravatar from your <a href="/wp-admin/profile.php">profile page</a>.', 'gravatar-widget' )?></p>
		<?php
	}

	/**
	 * Save widget data
	 *
	 * @param string $new_instance
	 * @param string $old_instance
	 * @return void
	 **/
	function update( $new_instance, $old_instance ) {
		$instance     = $old_instance;
		$new_instance = wp_parse_args( (array)$new_instance, array( 'title' => '', 'gravatar_size' => 128, 'gravatar_align' => 'none', 'gravatar_text' => '', 'email' => '', 'email_user' => -1 ) );

		$instance['title']          = wp_filter_nohtml_kses( $new_instance['title'] );
		$instance['gravatar_size']  = intval( $new_instance['gravatar_size'] );
		$instance['gravatar_text']  = wp_filter_post_kses( $new_instance['gravatar_text'] );
		$instance['email']          = wp_filter_nohtml_kses( $new_instance['email'] );
		$instance['email_user']     = intval( $new_instance['email_user'] );
		$instance['gravatar_url']   = esc_url( $new_instance['gravatar_url'], 'url' );
		$instance['gravatar_align'] = $new_instance['gravatar_align'];
		
		if ( $instance['email_user'] > 0 ) {
			$user = get_userdata( $instance['email_user'] );
			
			$instance['email'] = $user->user_email;
		}

		if ( !in_array( $instance['gravatar_size'], array( 64, 96, 128, 256 ) ) )
			$instance['gravatar_size'] = 96;
			
		if ( !in_array( $instance['gravatar_align'], array( 'none', 'left', 'right', 'center' ) ) )
			$instance['gravatar_align'] = 'none';
		
		return $instance;
	}
}

function gravatar_widget_init() {
	register_widget( 'Gravatar_Widget' );
}

add_action( 'widgets_init', 'gravatar_widget_init' );
