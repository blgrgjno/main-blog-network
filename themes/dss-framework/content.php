<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage DSS_Framework
 * @since DSS Framework 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php

		// Post thumbnail
		$thumbnail = get_the_post_thumbnail( get_the_ID(), 'single-post-thumbnail' );
		$thumbnail = apply_filters( 'dss_thumbnails', $thumbnail );

		if ( $thumbnail ) {
			?>
			<div class="post-thumbnail">
				<?php echo $thumbnail; ?>
			</div><!-- .post-thumbnail -->
			<div class="post-content"><?php
		}
		?>

			<header class="entry-header">
				<?php if ( is_sticky() ) : ?>
					<hgroup>
						<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'dss' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
						<h3 class="entry-format"><?php _e( 'Featured', 'dss' ); ?></h3>
					</hgroup>
				<?php else : ?>
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'dss' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				<?php endif; ?>
	
				<?php if ( 'post' == get_post_type() ) : ?>
				<div class="entry-meta">
					<?php dss_posted_on(); ?>
				</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->
	
			<?php if ( is_search() || is_archive() || is_home() ) : // Only display Excerpts for Search ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
			<?php else : ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dss' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'dss' ) . '</span>', 'after' => '</div>' ) ); ?>
			</div><!-- .entry-content -->
			<?php endif; ?>
	
			<footer class="entry-meta">
				<?php $show_sep = false; ?>
				<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( __( ', ', 'dss' ) );
	
					// If only one category and is in ignore list, then set the categories list to blank so it doesnt display the list
					$categories_list_stripped = strip_tags( $categories_list );
					foreach( dss_categories_to_ignore() as $cat ) {
						if ( $cat == $categories_list_stripped )
							$categories_list = '';
					}
	
					// Display the categories if they exist
					if ( $categories_list ) :
				?>
				<span class="cat-links">
					<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'dss' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
					$show_sep = true; ?>
				</span>
				<?php endif; // End if categories ?>
				<?php endif; // End if 'post' == get_post_type() ?>
	
				<?php if ( comments_open() ) : ?>
				<?php if ( $show_sep ) : ?>
				<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
				<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'dss' ) . '</span>', __( '<b>1</b> Reply', 'dss' ), __( '<b>%</b> Replies', 'dss' ) ); ?></span>
				<?php endif; // End if comments_open() ?>
	
				<?php edit_post_link( __( 'Edit', 'dss' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- #entry-meta --><?php

		// Close div if has post thumbnail
		if ( $thumbnail ) {
			echo '</div><!-- .post-content -->';
		}
		?>
	</article><!-- #post-<?php the_ID(); ?> -->
