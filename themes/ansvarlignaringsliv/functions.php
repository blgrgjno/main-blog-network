<?php


/**
* Extend Recent Posts Widget
*
* Adds different formatting to the default WordPress Recent Posts Widget
*/

Class My_Recent_Posts_Widget extends WP_Widget_Recent_Posts {

	function widget($args, $instance) {
		extract( $args );
		$more_posts_url = get_option( 'show_on_front' ) == 'page' ? get_permalink( get_option('page_for_posts' ) ) : get_bloginfo('url');
		$title = apply_filters('widget_title', 
			empty($instance['title']) ? 
			__('Recent Posts') : $instance['title'], 
			$instance, $this->id_base);
		if( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
			$number = 10;
		$r = new WP_Query( apply_filters( 'widget_posts_args', 
			array( 'posts_per_page' => $number, 'no_found_rows' => true, 
				'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if( $r->have_posts() ) :
			echo $before_widget;
		if( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
			<?php while( $r->have_posts() ) : $r->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a><br />[<?php the_time( 'd.m.Y'); ?>]</li>
			<?php endwhile; ?>
		</ul>
		<a href="<?php echo $more_posts_url; ?>"><?php _e('Flere nyheter'); ?></a>
		<?php
		echo $after_widget;
		wp_reset_postdata();
		endif;
	}
}

class Ansvarlig_Widget extends WP_Widget {

	function Ansvarlig_Widget() {
		$widget_ops = array( 'classname' => 'ansvarlig', 'description' => __('Widget for oversettelse og andre spesielt for ansvarlig næringsliv siten', 'example') );  
        $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'ansvarlig-widget' );  
        $this->WP_Widget( 'ansvarlig-widget', __('Ansvarlig Custom Widget', 'example'), $widget_ops, $control_ops );  
	}

	/** need to render wpml manually, because of trouble getting doamin mapping and wpml to work 
	*	together */
	function languages_list_footer() {
	    $languages = function_exists( 'icl_get_languages' ) ? icl_get_languages('skip_missing=0') : "";
	    if(!empty($languages)){
	    	if ($languages["nb"]) {
	    		$languages["nb"]["translated_name"] = "Norsk";
	    	}
	    	// TODO: rewrite to correct domain, ansvarlignaringsliv for norwegian, responsiblebuisness for 
	    	// english
	        echo '<div id="my_language_list"><ul>';
	        foreach($languages as $l){
	            echo '<li>';
	            if(!$l['active']) echo '<a href="'.$l['url'].'">';
	            echo icl_disp_language($l['translated_name'], $l['translated_name']);
	            if(!$l['active']) echo '</a>';
	            echo '</li>';
	        }
	        echo '</ul></div>';
	    }
	}

	function widget($args, $instance) {
		extract( $args );
		echo  $before_widget;
		self::languages_list_footer();
		echo $after_widget;
	}
}  

function ansvarlig_widget_registration() {
	unregister_widget('WP_Widget_Recent_Posts');
	register_widget('My_Recent_Posts_Widget');
	register_widget('Ansvarlig_Widget');
}
add_action( 'widgets_init', 'ansvarlig_widget_registration' );


/**
 * Load l18n
 */
function ansvarlig_theme_setup() {
    // Retrieve the directory for the localization files
    $lang_dir = get_template_directory() . '/lang';
     
    // Set the theme's text domain using the unique identifier from above
    load_theme_textdomain('ansvarlig', $lang_dir);
 
} 
add_action( 'after_setup_theme', 'ansvarlig_theme_setup' );

/**
 * Looks for WPML language and adds a body class for styling
 * @author Gorm
 */
function ansvarlig_body_classes( $classes ) {
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$classes[] = 'lang_' . ICL_LANGUAGE_CODE;
	}

	return $classes;
}
add_filter( 'body_class', 'ansvarlig_body_classes' );

?>

