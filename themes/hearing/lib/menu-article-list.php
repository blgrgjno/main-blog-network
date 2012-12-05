<?php

class Walker_Article_List extends Walker {

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	// Extra vars
	var $i = 0;
	var $container_num = 0;
	var $col_num = 1;
	var $break_added = false;
	var $first_child = true;

	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu\">\n";
	}

	function end_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query, $arguments;
		
		$this->i++;
		
		$arguments = $args;
	
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $this->i;
		if ($this->first_child) {
			$classes[] = 'first-child';
			$this->first_child = false;
		}
		
		// Get page's category class slugs
		$post_categories = wp_get_post_categories($item->object_id);
		foreach($post_categories as $c){
			$cat = get_category( $c );
			$classes[] = 'category-'.$cat->slug;
		}
		
		$has_post_thumbnail = has_post_thumbnail($item->object_id);
		if ($has_post_thumbnail) {
			$classes[] = "has-post-thumbnail";
		}
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		
		// Get correctly cached content
		$queried_post = get_post($item->object_id);
		$post_content = $queried_post->post_content;
		
		$item_output = $args->before;
		$item_output .= '<h2 class="entry-title">';
		if ($post_content) {
			$item_output .= '<a'. $attributes .'>' . $args->link_before;
		}
		$item_output .= apply_filters( 'the_title', $item->title, $item->object_id );
		if ($post_content) {
			$item_output .= $args->link_after . '</a>';
		}
		$item_output .= '</h2>';
		$item_output .= '<div class="entry-content">';
		
		if (has_post_thumbnail($item->object_id)) {
			$item_output .= '<a'. $attributes .'>';
			$item_output .= get_the_post_thumbnail($item->object_id, array(276, 134));
			$item_output .= '</a>';
		}
		
		// Render excerpt if available
		$post_excerpt = get_the_excerpt_here($item->object_id);
		if ($post_excerpt)
			$item_output .= '<p>' . $post_excerpt . '</p>';
		else
			$item_output .= nhop_trim_excerpt($item->post_content);
		
		if ($post_content) {
			$item_output = str_replace("</p>", ' <span class="readmore"><a'. $attributes .'>Les mer</a></span></p>', $item_output);
		}
		$item_output .= '</div>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function end_el(&$output, $item, $depth) {
		$output .= "</li>\n";
	}
	
}
?>