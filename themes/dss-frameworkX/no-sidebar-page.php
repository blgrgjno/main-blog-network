<?php
/**
 * Template Name: No Sidebar
 * Description: A Page Template that never displays a sidebar
 *
 * @package WordPress
 * @subpackage DSS_Framework
 * @since DSS Framework 1.0
 */

get_header(); ?>

		<div id="primary">
			<div id="content" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>