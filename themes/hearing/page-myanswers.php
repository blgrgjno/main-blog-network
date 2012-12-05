<?php
/**
 * Template Name: Mine høringssvar
 *
 * NHOP Listing av temaer og kommentarstatus
 */
?>
<?php

	function nhop_page_header() {
?>
		<?php thematic_postheader(); ?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
<?php
		if (has_post_thumbnail()) {
?>
			<div class="page_ill">
<?php
				the_post_thumbnail();
?>
			</div>
<?php
		}
	}
	add_action('thematic_belowheader','nhop_page_header');
	
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

	
?>
	<div id="container" class="fullwidth">
		<div id="content">

            <?php
        
            // calling the widget area 'page-top'
            get_sidebar('page-top');

            the_post();
        
            ?>

			<?php
				if ($_GET['sendt'] == "ok") {
			?>
				<div class="statusBox statusOK">
					<p><?php theme_option('myanswers_ok'); ?></p>
				</div>
			<?php
				}
			?>
            
			<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
            
				<div class="entry-content">

					<div class="myAnswers">
						<div class="myAnswersHeader">
							<div class="row">
								<div class="parentTopic">&nbsp;</div>
								<div class="topic"><strong>Innsatsområde</strong></div>
								<div class="answers"><strong>Høringssvar</strong></div>
							</div>
						</div>
						<div class="myAnswersBody">
<?php
						// Render my answers
						wp_nav_menu_no_ul( array( 'menu' => 'temaer', 'container' => '', 'container_class' => '', 'walker' => new Walker_My_Page ) );
?>
						</div><!-- /myAnswersBody -->
					</div><!-- /myAnswers -->
<?php
                    wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'thematic'), "</div>\n", 'number');
                    
                    edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>

				</div>
                
			</div><!-- .post -->

        <?php
        
        // calling the widget area 'page-bottom'
        get_sidebar('page-bottom');
        
        ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling footer.php
    get_footer();


class Walker_My_Page extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	/**
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	var $first_topic = true;
	
	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl(&$output, $depth) {
		$this->first_topic = true;
		/*
		if ($depth == 0) {
			$output .= "<tr><td><strong>";
		}
		else {
			$output .= "<td>";
		}
		*/
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl(&$output, $depth) {
		/*
		if ($depth == 0) {
			$output .= "</strong></tr></td>";
		}
		else {
			$output .= "</td></tr>";
		}
		*/
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		global $wp_query, $current_user, $wpdb;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
		
		if ($depth == 1) {
			$output .= '<div class="row"><div class="parentTopic">';
		}
		else if ($depth == 2) {
			if (!$this->first_topic) {
				$output .= '<div class="row">';
				
				$output .= '<div class="topic noParentTopic">';
				$this->first_topic = false;
			}
			else {
				$output .= '<div class="topic">';
			}
		}
		$this->first_topic = false;

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$before = "";
		$after = "";
		if ($depth == 1) {
			$before = "<h2>";
			$after = "</h2>";
		}
		else if ($depth == 2) {
			$before = '<h3><a'. $attributes .'>';
			$after = '</a></h3>';
			
			// Answers column:
			
			if ($current_user->ID > 0) {
				$args = array(
					'numberposts' => -1,
					'post_status' => null,
					'meta_key' => get_theme_option('parent_topic_field_name'),
					'meta_value' => $item->object_id,
				);
				$author_topic_posts = get_posts($args);
				
				$answers = "";
				if ($author_topic_posts) {
					foreach ($author_topic_posts as $post) {
						if ($post->post_author == $current_user->ID) {
							$statement_meta = get_statement_meta($post->ID);
							$answers .= '<a class="editAnswer" href="'.$statement_meta->statement_url.'">'.$post->post_title.'</a> ';
						}
					}
				}
			}
			if (get_theme_option('enable_statements')) {
				$answers .= '<a class="addAnswer" href="'. $item->url . get_theme_option('slug_write') . '/">Skriv nytt</a> ';
			}
			$after .= '</div><div class="answers">'.$answers.'';
		}
		
		$hidetitle = get_post_custom_values('topicgroup_hidetitle', $item->object_id);
		
		if ($depth > 0) {
			$item_output = $args->before;
			$item_output .= $before;
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= $after;
			$item_output .= $args->after;
		}
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
		if ($depth == 1) {
			$output .= '</div>';
		}
		else if ($depth == 2) {
			$output .= '</div></div>';
		}
		
	}

	/**
	 * @see Walker::end_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el(&$output, $item, $depth) {
	/*
		if ($depth != 2) {
			$output .= "";
		}
		else if ($depth == 2) {
			$output .= "</tr>";
		}
		*/
	}
}
?>