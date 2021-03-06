<?php
/**
 * Template Name: Forside - Minimal
 *
 * NHD-forside
 */
?>

<?php
	function hearing_page_header() {
?>
		<div id="c_front_header" class="page_header_front">
		</div>
		<script type="text/javascript">
			jQuery('#c_front_header').hearingFront();
		</script>
<?php
	}
	add_action('thematic_belowheader','hearing_page_header');
	/*
	// Enable thickbox
	wp_enqueue_style('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
	*/
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>
	<div id="container">
		<div id="content">
<?php
            // calling the widget area 'page-top'
            get_sidebar('page-top');
			
            the_post();
?>
			<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
<?php
				// Render article list
				if (class_exists('Walker_Article_List')) {
					wp_nav_menu(array(
						'theme_location' => 'article-menu',
						'container' => '',
						'menu_class' => 'article-list',
						'break_point' => get_theme_option('front_menu_break_point'),
						'walker' => new Walker_Article_List
					));
				}
?>
			</div><!-- .post -->
<?php
        // calling the widget area 'page-bottom'
        get_sidebar('page-bottom');
?>
			<a class="delete-cookie" href="#">&nbsp;.&nbsp;</a>
		</div><!-- #content -->
	</div><!-- #container -->
<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar
	echo '<div id="primary" class="aside main-aside"><ul class="xoxo">';
	if ( dynamic_sidebar( 'sidebar' ) ) {}
	echo '</ul></div>';
    
    // calling footer.php
    get_footer();

?>