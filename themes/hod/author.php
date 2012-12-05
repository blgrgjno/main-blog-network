<?php
    // calling the theme options
    global $options;
	
    foreach ($options as $value) {
        if (get_option( $value['id'] ) === FALSE) { 
            $$value['id'] = $value['std']; 
        } else {
            $$value['id'] = get_option( $value['id'] );
        }
    }

	function nhop_page_header() {
		global $authordata;
?>
		<div id="c_page_header" class="page_header_page">
			<div class="page_header">
				<?php exit_logo(); ?>
				<div class="page_intro">
					<h1 class="entry-title">Høringssvar</h1> 
				</div>
			</div>
		</div>
<?php
	}
	add_action('thematic_belowheader','nhop_page_header');
	
    // calling the header.php
    get_header();

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

            <?php

            the_post();
			
            // displays the page title
?>
             <h2 class="page-title"><?php echo $authordata->display_name; ?> <span class='number'>(<?php echo $wp_query->found_posts; ?>)</span></h2> 
<?php
            if (!is_paged()) { 
				echo getAuthorMeta();
            }
	?>
			<div class="postSorter">
				<a href="<?php echo $request; ?>" <?php if ($_GET['sortering'] != "kommentarer") echo "class='active'"; ?>>Siste</a> |
				<a href="<?php echo $request; ?>?sortering=kommentarer" <?php if ($_GET['sortering'] == "kommentarer") echo "class='active'"; ?>>Mest kommentert</a>
			</div>
	<?php
			if ($_GET['sortering'] == 'kommentarer') {
				query_posts('author='.$authordata->ID.'&orderby=comment_count&paged='.$paged);
			}
			
            // action hook creating the author loop
            thematic_authorloop();

            // create the navigation below the content
			thematic_navigation_below(); ?>
	
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