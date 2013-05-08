<?php

class Walker_Front_Page extends Walker {

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	// Extra vars
	var $i = 1;
	var $container_num = 0;
	var $col_num = 1;
	var $break_added = false;
	var $first_child = true;

	function start_lvl(&$output, $depth) {
		$indent = str_repeat("\t", $depth);
		if ($depth == 0) {
			$this->i = 1;
			$this->container_num++;
			$output .= "<div class='menu_container menuContainer".$this->container_num."'><div class='menu_col col1'>\n";
		}
		if ($depth == 1)
			$output .= "<ul class='menulist'>\n";
	}

	function end_lvl(&$output, $depth) {
		if ($depth == 0)
			$output .= "</div></div>\n";
		else if ($depth == 1)
			$output .= "</ul>\n";
	}

	function end_el(&$output, $item, $depth) {
		if ($depth > 1)
			$output .= "</li>\n";
	}

	function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		if ($depth == 2 && $this->first_child) {
			$classes[] = 'first-child';
			$this->first_child = false;
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		if ($depth > 1)
			$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
		$before = "";
		$after = "";
		if ($depth == 0) {
			$before = "<h2 class='entry-title'>";
			$after = "</h2>";
		}
		else if ($depth == 1) {
			$this->i++;
			$before = "<h3>";
			$after = "</h3>";
			if ($this->i > $args->break_point) {
				$this->col_num++;
				$before = "</div><div class='menu_col col".$this->col_num."'>" . $before;
				$this->i = 0;
			}
		}
		else {
			$this->i++;
		
			$before = '<a'. $attributes .'>';
			$after = '</a>';
		}
		
		$hidetitle = get_post_custom_values('topicgroup_hidetitle', $item->object_id);
		
		if (!$hidetitle) {
			$item_output = $args->before;
			$item_output .= $before;
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;// $this->i . " " . 
			$item_output .= $after;
			$item_output .= $args->after;
		}
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

function wp_nav_menu_no_ul( $args = array() ) {
	static $menu_id_slugs = array();

	$defaults = array( 'menu' => '', 'container' => 'div', 'container_class' => '', 'container_id' => '', 'menu_class' => 'menu', 'menu_id' => '',
	'echo' => true, 'fallback_cb' => 'wp_page_menu', 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '',
	'depth' => 0, 'walker' => '', 'theme_location' => '' );

	$args = wp_parse_args( $args, $defaults );
	$args = apply_filters( 'wp_nav_menu_args', $args );
	$args = (object) $args;

	// Get the nav menu based on the requested menu
	$menu = wp_get_nav_menu_object( $args->menu );

	// Get the nav menu based on the theme_location
	if ( ! $menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) )
		$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

	// get the first menu that has items if we still can't find a menu
	if ( ! $menu && !$args->theme_location ) {
		$menus = wp_get_nav_menus();
		foreach ( $menus as $menu_maybe ) {
			if ( $menu_items = wp_get_nav_menu_items($menu_maybe->term_id) ) {
				$menu = $menu_maybe;
				break;
			}
		}
	}

	// If the menu exists, get its items.
	if ( $menu && ! is_wp_error($menu) && !isset($menu_items) )
		$menu_items = wp_get_nav_menu_items( $menu->term_id );

	// If no menu was found or if the menu has no items and no location was requested, call the fallback_cb if it exists
	if ( ( !$menu || is_wp_error($menu) || ( isset($menu_items) && empty($menu_items) && !$args->theme_location ) )
		&& ( function_exists($args->fallback_cb) || is_callable( $args->fallback_cb ) ) )
			return call_user_func( $args->fallback_cb, (array) $args );

	// If no fallback function was specified and the menu doesn't exists, bail.
	if ( !$menu || is_wp_error($menu) )
		return false;

	$nav_menu = $items = '';

	$show_container = false;
	if ( $args->container ) {
		$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
		if ( in_array( $args->container, $allowed_tags ) ) {
			$show_container = true;
			$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
			$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
			$nav_menu .= '<'. $args->container . $id . $class . '>';
		}
	}

	// Set up the $menu_item variables
	_wp_menu_item_classes_by_context( $menu_items );

	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $key => $menu_item )
		$sorted_menu_items[$menu_item->menu_order] = $menu_item;

	unset($menu_items);

	$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
	unset($sorted_menu_items);

	// Attributes
	if ( ! empty( $args->menu_id ) ) {
		$slug = $args->menu_id;
	} else {
		$slug = 'menu-' . $menu->slug;
		while ( in_array( $slug, $menu_id_slugs ) ) {
			if ( preg_match( '#-(\d+)$#', $slug, $matches ) )
				$slug = preg_replace('#-(\d+)$#', '-' . ++$matches[1], $slug);
			else
				$slug = $slug . '-1';
		}
	}
	$menu_id_slugs[] = $slug;
	$attributes = ' id="' . $slug . '"';
	$attributes .= $args->menu_class ? ' class="'. $args->menu_class .'"' : '';

	// Allow plugins to hook into the menu to add their own <li>'s
	$items = apply_filters( 'wp_nav_menu_items', $items, $args );
	$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );
	$nav_menu .= $items;
	unset($items);

	if ( $show_container )
		$nav_menu .= '</' . $args->container . '>';

	$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

	if ( $args->echo )
		echo $nav_menu;
	else
		return $nav_menu;
}
?>