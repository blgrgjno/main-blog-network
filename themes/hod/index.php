<?php

	function nhop_page_header() {
?>
		<div id="c_page_header" class="page_header_page">
			<div class="page_header">
				<?php exit_logo(); ?>
				<div class="page_intro">
					<h1 class="entry-title">Alle høringssvar</h1>
				</div>
			</div>
		</div>
<?php
	}
	add_action('thematic_belowheader','nhop_page_header');
	
	$sort_base_url = "/".$wp_query->query_vars['pagename']."/";
	
    // calling the header.php
    get_header();

	if ($_GET['sortering'] == 'kommentarer') {
		query_posts('orderby=comment_count&cat='.$getcat.'&paged='.$paged);
	}
	
    // action hook for placing content above #container
    thematic_abovecontainer();

	// Sorting
	$request = remove_query_arg( 'paged' );
	$request = preg_replace( '|page/\d+/?$|', '', $request);

	if ($pos = strpos($request, "?")) {
		$request = substr($request, 0, $pos);
	}
	
?>
	<div id="container">
		<div id="content">
			<h2 class="page-title">Alle innsendere <span class='number'>(<?php echo $wp_query->found_posts; ?> svar)</span></h2>
			<div class="postSorter">
				<a href="<?php echo $request; ?>" <?php if ($_GET['sortering'] != "kommentarer") echo "class='active'"; ?>>Siste</a> |
				<a href="<?php echo $request; ?>?sortering=kommentarer" <?php if ($_GET['sortering'] == "kommentarer") echo "class='active'"; ?>>Mest kommentert</a>
			</div>
<?php
            // calling the widget area 'index-top'
            get_sidebar('index-top');

            // action hook for placing content above the index loop
            thematic_above_indexloop();

            // action hook creating the index loop
            thematic_indexloop();

            // action hook for placing content below the index loop
            thematic_below_indexloop();

            // calling the widget area 'index-bottom'
            get_sidebar('index-bottom');

			// create the navigation below the content
			thematic_navigation_below();
			
            ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar 
    thematic_sidebar();
    
    // calling footer.php
    get_footer();

?>