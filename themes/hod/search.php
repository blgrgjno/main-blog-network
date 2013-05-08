<?php

	function nhop_page_header() {
?>
		<div id="c_page_header" class="page_header_topic">
			<div class="page_header">
				<?php exit_logo(); ?>
				<div class="page_intro">
					<h1 class="entry-title">Søkeresultater</h1>
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

?>

	<div id="container">
		<div id="content">

            <?php 
            
            if (have_posts()) {

                // displays the page title
				global $wp_query;
				$content .= '<h2 class="page-title">';
				$content .= __('Search Results for:', 'thematic');
				$content .= ' &ldquo;';
				$content .= wp_specialchars(stripslashes($_GET['s']), true);
				$content .= '&rdquo;';
				$content .= ' <span id="search-terms">';
				$content .= " (".$wp_query->found_posts.")";
				$content .= '</span></h2>';
				echo $content;

                // create the navigation above the content
                thematic_navigation_above();
			
                // action hook for placing content above the search loop
                thematic_above_searchloop();			

                // action hook creating the search loop
                thematic_searchloop();

                // action hook for placing content below the search loop
                thematic_below_searchloop();			

                // create the navigation below the content
                thematic_navigation_below();
				
				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

            } else { 
                
                ?>

			<div id="post-0" class="post noresults">
				<h2 class="entry-title"><?php _e('Nothing Found', 'thematic') ?></h2>
				<div class="entry-content">
					<p>Beklager, vi fant ikke det du lette etter. Forsøk gjerne med andre søkeord:</p>
				</div>
				<form id="noresults-searchform" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="noresults-s" name="s" type="text" value="<?php echo wp_specialchars(stripslashes($_GET['s']), true) ?>" size="40" />
						<input id="noresults-searchsubmit" name="searchsubmit" type="submit" value="<?php _e('Find', 'thematic') ?>" />
					</div>
				</form>
			</div><!-- .post -->

            <?php
            
            }
            
            ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar 
    //thematic_sidebar();
    
    // calling footer.php
    get_footer();

?>