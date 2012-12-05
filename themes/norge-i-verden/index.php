<?php
/**
 * The main template file
 * Used to generate the category listings displayed on the home page
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">
				<h1 class="entry-title">Siste innlegg</h1><?php
			
			$category_args = array(
				'orderby'  => 'name',
				'order'    => 'ASC',
				'child_of' => 0
			);
			$category_data =   get_categories( $category_args ); 
			dss_content_nav( 'nav-above' );
			foreach( $category_data as $category ) {
				$categories[] = $category->term_id;

				if ( $category->term_id != 1 ) { // Ignore 1 since that is "uncategorized"
					global $post;
					$args = array(
						'numberposts' => 1,
						'category'    => $category->term_id
					);
					$myposts = get_posts( $args );
					foreach( $myposts as $post ) {
						setup_postdata( $post );
						get_template_part( 'content', get_post_format() );
					}
				}
			}

			?>
			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>