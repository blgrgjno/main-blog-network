<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

$main_page_id = 0;
get_header(); 
?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php 
			if ( is_front_page() ) {		
				while ( have_posts() ) : the_post();  
					/* ugly hack, find main article only, then loop again around child articles */
					$main_page_id = get_the_ID();
				endwhile; 
			} ?>
			<?php 
			$query_args = array( 'showposts' => 6, 'post_parent' => $main_page_id, 
				'post_type' => 'page', 'order' => 'ASC', 'orderby' => 'menu_order' );
			$frontpage_pages = new WP_Query( $query_args );
			?>
			<?php 
				if ( $frontpage_pages->have_posts() ) {
					while ($frontpage_pages->have_posts() ) {
						$frontpage_pages->the_post(); ?>
						<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<header class="entry-header">
								<h2 class="entry-title"><?php the_title(); ?></h2>
							</header><!-- .entry-header -->

							<div class="entry-content">
								<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
									<div class="entry-thumbnail">
										<?php the_post_thumbnail(); ?>
									</div>
								<?php endif; ?>
								<?php if ( current_user_can( "edit_posts" ) ) : ?>
									<footer class="entry-meta">
										<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
									</footer><!-- .entry-meta -->
								<?php endif; ?>
								<?php the_content(); ?>
								<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentythirteen' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
							</div><!-- .entry-content -->
						</section><!-- #post -->

						<?php #comments_template(); ?>
					<?php 
					} 
				}?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>