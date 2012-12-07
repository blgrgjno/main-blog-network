<?php
/**
 * Template Name: Page - nyheter
 *
 * This is a really silly way to display news, but was a requirement in the specifications
 */



    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

?>

		<div id="container">
		
			<?php thematic_abovecontent(); ?>
		
			<div id="content">
	
	            <?php
	        
	            // displays the page title
	            //thematic_page_title();
      echo '<div class="page_header"><h1 class="entry-title">Nyheter</h1></div>';
		echo '<div class="postSorter"></div>';	
	            // create the navigation above the content
	            thematic_navigation_above();
				
	            // action hook for placing content above the category loop
	            thematic_above_categoryloop();			
	



		query_posts( array (
			'category_name' => 'nyheter',
			'posts_per_page' => -1,
			'post_type' => 'page',
		) );
		while (have_posts()) : the_post(); 		
				?>
				<div id="post-<?php the_ID(); ?>"  class="type-post" style="border-bottom: 1px solid #ddd; padding-bottom: 25px; margin-top: 25px;">
					<h3 class="entry-title" style="padding-bottom: 0;padding-top: 0;">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<div style="padding-bottom: 0; padding-top: 0;" class="entry-content">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
					<span class="meta_readmore"><a href="<?php the_permalink(); ?>">Les nyhet</a></span>
				</div><!-- #post -->

			<?php 
		
			thematic_belowpost();
		
		endwhile;





	
	            // action hook for placing content below the category loop
	            thematic_below_categoryloop();			
	
	            // create the navigation below the content
	            thematic_navigation_below();
	            
	            ?>
	
			</div><!-- #content -->
			
			<?php thematic_belowcontent(); ?> 
			
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
