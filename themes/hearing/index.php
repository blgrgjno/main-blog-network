<?php
	$sort_base_url = "/".$wp_query->query_vars['pagename']."/";
    // calling the header.php
    get_header();

	if ($_GET['sortering'] == 'kommentarer') {
        $query = 'orderby=comment_count';
        if ($getcat) {
          $query .= '&cat='.$getcat;
        }
        if ($paged) {
          $query .= '&paged='.$paged;
        }
		query_posts($query);
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
			<div class="page_header">
				<div class="page_intro">
					<h1 class="entry-title">Alle <?php echo strtolower(get_theme_option('entity_plural')); ?></h1>
				</div>
				
			</div>
			<h2 class="page-title">Alle innsendere <span class='number'>(<?php echo $wp_query->found_posts; ?> <?php echo strtolower(get_theme_option('entity_plural')); ?>)</span></h2>
			<div class="postSorter">
				<a href="<?php echo esc_url( $request ); ?>" <?php if ($_GET['sortering'] != "kommentarer") echo "class='active'"; ?>>Siste</a> |
				<a href="<?php echo esc_url( $request ); ?>?sortering=kommentarer" <?php if ($_GET['sortering'] == "kommentarer") echo "class='active'"; ?>>Mest kommentert</a>
			</div>
<?php
            // calling the widget area 'index-top'
            get_sidebar('index-top');

            // action hook for placing content above the index loop
            thematic_above_indexloop();

















		// Copy and pasted from Thematic theme to allow for direct modification to enable correct author names to be used
		global $options, $blog_id;
		
		foreach ($options as $value) {
		    if (get_option( $value['id'] ) === FALSE) { 
		        $$value['id'] = $value['std']; 
		    } else {
		    	if (THEMATIC_MB) 
		    	{
		        	$$value['id'] = get_option($blog_id,  $value['id'] );
		    	}
		    	else
		    	{
		        	$$value['id'] = get_option( $value['id'] );
		    	}
		    }
		}
		
		/* Count the number of posts so we can insert a widgetized area */ $count = 1;
		while ( have_posts() ) : the_post();
		
				thematic_abovepost(); ?>

				<div id="post-<?php the_ID();
					echo '" ';
					if (!(THEMATIC_COMPATIBLE_POST_CLASS)) {
						post_class();
						echo '>';
					} else {
						echo 'class="';
						thematic_post_class();
						echo '">';
					}

					?>
					<div class="author-meta">
						<span class="author_name author_">
							<span class="a_alt">
							<?php echo get_post_meta( $post->ID, 'Author Name', true ); ?>
							</span>
						</span>
					</div>
		
					<h3 class="entry-title">
						<a href="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>" title="<?php esc_attr( get_the_title() ); ?>" rel="bookmark">
							<?php the_title(); ?>
						</a>
					</h3>

					<div class="entry-content">
			<?php thematic_content(); ?>

					<?php wp_link_pages('before=<div class="page-link">' .__('Pages:', 'thematic') . '&after=</div>') ?>
					</div><!-- .entry-content -->
					<?php thematic_postfooter(); ?>
				</div><!-- #post -->

			<?php 
				
				thematic_belowpost();
				
				comments_template();

				if ($count==$thm_insert_position) {
						get_sidebar('index-insert');
				}
				$count = $count + 1;
		endwhile;
















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
	echo '<div id="primary" class="aside main-aside"><ul class="xoxo">';
	if ( dynamic_sidebar( 'sidebar' ) ) {}
	echo '</ul></div>';
    
    // calling footer.php
    get_footer();

?>