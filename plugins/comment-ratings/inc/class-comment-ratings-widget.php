<?php

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Comments_Rating_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	public function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'description' => __( 'Displays the most important comments', 'cr') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'cr-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'cr-widget', __( 'Comments rating widget', 'cr'), $widget_ops, $control_ops );
	}
	
	/*
	 * The widget itself
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		
		// Get any existing copy of our transient data
		if ( false === ( $comment_ratings = get_transient( 'comment_ratings' ) ) ) {
			
			// Grab array of comment ratings
			$posts_args = array(
				'posts_per_page' => 999,
				'post_type'      => array( 'post', 'page' ),
			);
			$posts = get_posts( $posts_args );
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				
				// Comments
				$comments_args = array(
					'number' => 999,
					'post_id' => $post->ID, // use post_id, not post_ID
				);
				$comments = get_comments( $comments_args );
				foreach ( $comments as $comment ) {
					
					// Calculate a rating for the comment
					$up_vote   = get_comment_meta( $comment->comment_ID, 'up_vote',   true );
					$down_vote = get_comment_meta( $comment->comment_ID, 'down_vote', true );
					$total_vote = $up_vote + $down_vote;
					
					if ( 0 != $down_vote ) {
						$rating = $total_vote * ( $up_vote / $down_vote );
					} else {
						$rating = 0;
					}
					
					// Comment ratings
					$comment_ratings[$rating] = array(
						'post_id'      => $post->ID,
						'comment_id'   => $comment->comment_ID,
					);
				}
			}
//				set_transient( 'comment_ratings', $comment_ratings, 1 * HOUR_IN_SECONDS );
			set_transient( 'comment_ratings', $comment_ratings, 5 );
		}
		
		krsort( $comment_ratings );
		
		echo '<ul>';
		$count = 0;
		foreach( $comment_ratings as $key => $value ) {
			$count++;
			if ( $count > 20 ) {
				continue;
			}
			echo '<li><a href="' . get_permalink( $value['post_id'] ) . '#comment-' . $value['comment_id'] . '">';
			$comment_text = get_comment_text( $value['comment_id'] );
			$comment_text = esc_html( $comment_text );
			$comment_text = $this->word_limiter( $comment_text, 10, '&#8230;' );
			echo $comment_text;
			echo ' (' . $key . ')</a></li>';
		}
		echo '</ul>';
			
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	/*
	 * Word limiter
	 */
	public function word_limiter( $string, $limit, $end_char ) {
		if ( trim( $string ) == '' ) {
			return $string;
		}
		
		preg_match( '/^\s*+(?:\S++\s*+){1,' . (int) $limit . '}/', $string, $matches );
		
		if ( strlen( $string ) == strlen( $matches[0] ) ) {
			$end_char = '';
		}
		
		return rtrim( $matches[0] ) . $end_char;
	}
	
	/**
	 * Update the widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['title'] = esc_html( $new_instance['title'] );
		$instance['name'] = esc_html( $new_instance['name'] );
		
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	public function form( $instance ) {
		
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'title', 'Most popular comments') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cr' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p><?php
	}
}
