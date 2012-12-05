<?php

class NHOP_Category_Filter extends WP_Widget {

	function NHOP_Category_Filter() {
		$widget_ops = array('classname' => 'nhop_category_filter', 'description' => __('Filter on Category'));
		$this->WP_Widget('nhop_category_filter', __('NHOP: Kategorifilter'), $widget_ops, $control_ops);
		$this->alt_option_name = 'nhop_category_filter';
		
		add_action('save_post', array(&$this, 'flush_widget_cache'));
		add_action('deleted_post', array(&$this, 'flush_widget_cache'));
		add_action('switch_theme', array(&$this, 'flush_widget_cache'));
	}

	function form($instance) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : 'Se bare';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['nhop_category_filter']) )
			delete_option('nhop_category_filter');

		return $instance;
	}

	function widget($args, $instance) {
		global $post, $wp_query;
		$temp_query = $wp_query;
		
		// Display only on "alle høringssvar"
		if (!is_home() && !is_author()) return;
		
		$cache = wp_cache_get('nhop_category_filter', 'widget');
		
		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}
		
		ob_start();
		extract( $args );
		
		$title = apply_filters('widget_title', empty($instance['title']) ? "" : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
        if ($title) echo $before_title . $title . $after_title;
?>
		<h4>Sortert på innsender</h4>
		<ul>
			<li><a href="/alle-innsendere/">Se liste over alle innsendere</a></li>
		</ul>
		<h4>Sortert på tema</h4>
		<ul>
<?php
		global $post;
		$args = array(
			'post_type' => 'topic',
			'numberposts' => -1,
			'orderby' => 'menu_order title',
			'order' => 'ASC'
		);
		$topics = get_posts($args);
		//print_r($topics);
		if ($topics) {
			foreach ($topics as $post) {
				setup_postdata($post);
?>
				<li><a href="<?php the_permalink(); theme_option('slug_statements'); echo "/"; ?>"><?php the_title(); ?></a></li>
<?php
			}
		}
?>
		</ul>
<?php
		echo $after_widget;
		
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		$wp_query = $temp_query;
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('nhop_category_filter', $cache, 'widget');
	}
	
	function flush_widget_cache() {
		wp_cache_delete('nhop_category_filter', 'widget');
	}
}
add_action('widgets_init', create_function('', 'return register_widget("NHOP_Category_Filter");'));
?>