<?php

class NHOP_Entry extends WP_Widget {

	function NHOP_Entry() {
		$widget_ops = array('classname' => 'nhop_entry', 'description' => __('Din mening teller-boks'));
		$this->WP_Widget('nhop_entry', __('NHOP: Inngang'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		$link_url = $instance['link_url'];
		$link_text = $instance['link_text'];
		$box_enclose = $instance['box_enclose'];
		$write_only = $instance['write_only'];
		
		// Don't display on same page as link url:
		if ($_SERVER['REQUEST_URI'] == $link_url) return;
		
		// Don't display on list pages and topic pages:
		global $wp_query;
		$global_hide = ($wp_query->query_vars['post_type'] == "topic" || is_home() || is_author() || $write_only);
		// Check if show on write only
		$write_show = ($write_only && $wp_query->query_vars['show'] == get_theme_option('slug_write'));
		
		if ( !((!$global_hide && !$write_show) || ($global_hide && $write_show)) ) {
			return;
		}
		
		echo $before_widget;

		if ($box_enclose) echo "<div class='sidebox'>";
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
?>
		<div class="entry-content">
			<?php echo $instance['filter'] ? wpautop($text) : $text; ?>
<?php
			if ($link_url) {
?>
				<p class="buttons"><a href="<?php echo $link_url; ?>" class="buttonRound"><span><?php echo $link_text; ?></span></a></p>
<?php
			}
?>
		</div>
<?php
		if ($box_enclose) echo "</div><!-- /sidebox -->";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['link_url'] =  $new_instance['link_url'];
		$instance['link_text'] =  $new_instance['link_text'];
		$instance['filter'] = isset($new_instance['filter']);
		$instance['box_enclose'] = isset($new_instance['box_enclose']);
		$instance['write_only'] = isset($new_instance['write_only']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
		$link_url = $instance['link_url'];
		$link_text = $instance['link_text'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('link_url'); ?>"><?php _e('Lenke-URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="text" value="<?php echo esc_attr($link_url); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Lenketekst:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>" /></p>
		
		<p><input id="<?php echo $this->get_field_id('box_enclose'); ?>" name="<?php echo $this->get_field_name('box_enclose'); ?>" type="checkbox" <?php checked(isset($instance['box_enclose']) ? $instance['box_enclose'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('box_enclose'); ?>">Vis som boks</label></p>
		
		<p><input id="<?php echo $this->get_field_id('write_only'); ?>" name="<?php echo $this->get_field_name('write_only'); ?>" type="checkbox" <?php checked(isset($instance['write_only']) ? $instance['write_only'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('write_only'); ?>">Vis bare på skriv-siden</label></p>

<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("NHOP_Entry");'));
?>