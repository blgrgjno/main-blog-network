<?php

class Walker_Main_Menu extends Walker {

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	// Extra vars
	var $first_child = true;
	var $args;
	
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
		
		$arguments = $args;
		
		if (!$this->isHidden($item->object_id)) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			if ($this->first_child) {
				$classes[] = 'first-child';
				$this->first_child = false;
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

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $requires_login . $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	function end_el(&$output, $item, $depth) {
		if (!$this->isHidden($item->object_id)) {
			$output .= "</li>\n";
		}
	}
	
	function isHidden($post_id) {
		global $arguments;
		$requires_login = get_post_custom_values('requires_login', $post_id);
		$hide_in_header = get_post_custom_values('hide_in_header', $post_id);
		
		if ($arguments->location == "header" && $hide_in_header && $hide_in_header[0]) {
			return true;
		}
		else if ($requires_login && $requires_login[0]) {
			if ( !is_user_logged_in() ) {
				return true;
			}
		}
		return false;
	}
}

?>