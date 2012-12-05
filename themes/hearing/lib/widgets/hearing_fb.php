<?php

class Hearing_Facebook extends WP_Widget {

	function Hearing_Facebook() {
		$widget_ops = array('classname' => 'widget-hearing-fb', 'description' => __('Setter inn Facebook like og share-knapper'));
		$this->WP_Widget('hearing_fb', __('Høring: Facebook Like & Share'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$link_text = $instance['link_text'];
		
		if (!is_front_page() && $instance['front_only']) {
			return;
		}
		
		echo $before_widget;
?>
		<a id="fb_share_link" rel="nofollow" href="http://www.facebook.com/share.php?u=<?php bloginfo('url'); ?>&amp;t=<?php bloginfo('name'); ?>" target="_blank" class="fb_share_link"><?php echo $link_text; ?></a>

		<iframe class="fb_like_iframe" src="https://www.facebook.com/plugins/like.php?locale=nb_NO&amp;app_id=205125446216064&amp;href=<?php bloginfo('url'); ?>&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['front_only'] = isset($new_instance['front_only']);
		$instance['link_text'] =  $new_instance['link_text'];
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$link_text = $instance['link_text'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('link_text'); ?>"><?php _e('Lenketekst "Share on Facebook":'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo esc_attr($link_text); ?>" /></p>
		
		<p><input id="<?php echo $this->get_field_id('front_only'); ?>" name="<?php echo $this->get_field_name('front_only'); ?>" type="checkbox" <?php checked(isset($instance['front_only']) ? $instance['front_only'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('front_only'); ?>">Vis bare på forsiden</label></p>

<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("Hearing_Facebook");'));
?>