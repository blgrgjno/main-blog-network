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
			<div class="panel-videos">
				<div id="team"></div>
				<div id="loop01"></div>
				<div id="loop02"></div>
                <div id="loop03"></div>
				<ul class="menu-videos">
                	<li class="b1"><a href="http://www.youtube.com/embed/MLkOxy9WFKY?autoplay=1&amp;theme=light&amp;color=white&amp;showinfo=0&amp;rel=0" target="_blank"><span>Enklere å komme i gang</span></a></li>
					<li class="b2"><a href="http://www.youtube.com/embed/Mub1n7kWb10?autoplay=1&amp;theme=light&amp;color=white&amp;showinfo=0&amp;rel=0" target="_blank"><span>Effektivt samspill</span></a></li>
					<li class="b3"><a href="http://www.youtube.com/embed/6WDclwxhqnE?autoplay=1&amp;theme=light&amp;color=white&amp;showinfo=0&amp;rel=0" target="_blank"><span>Enklere skjemavelde</span></a></li>
					<li class="b4"><a href="http://www.youtube.com/embed/hHsOfQxZPqY?autoplay=1&amp;theme=light&amp;color=white&amp;showinfo=0&amp;rel=0" target="_blank"><span>Ulik tolkning av regler</span></a></li>
					<li class="b5"><a href="http://www.youtube.com/embed/ymtWBHRprtE?autoplay=1&amp;theme=light&amp;color=white&amp;showinfo=0&amp;rel=0" target="_blank" class="dontskip"><span>Giske forklarer</span></a></li>
				</ul>
			</div>
			<div class="panel-play">
				<iframe width="640" height="400" src="<?php bloginfo('stylesheet_directory'); ?>/img/blank.png" frameborder="0" allowFullScreen></iframe>
				<a href="#" class="play-back">Tilbake</a>
			</div>
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