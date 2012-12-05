<?php


// Kill page for public
if ( ! isset( $_GET['showsplash'] ) && ! is_admin() ) {
	die;
}
if ( 'marthe-is-cool' != $_GET['showsplash'] ) {
	die;
}



get_header();

if ( have_posts() ) while ( have_posts() ) {
?>
<article id="static-page" class="post">
	<header class="entry-header">
		<hgroup>
			<h2 class="entry-title">
				<?php the_title(); ?>
			</h2>
		</hgroup>
	</header><!-- .entry-header -->
	<div class="entry-summary"><?php

		the_post();
		?>
		<h1 class="entry-title"></h1><?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="page-link">' . __( 'Pages:', 'multisite-splash' ),
				'after' => '</div>'
			)
		);
		edit_post_link(
			__( 'Edit', 'multisite-splash' ),
			'<span class="edit-link">',
			'</span>'
		);
		?>
	</div><!-- .entry-summary -->
</article><!-- #static-page -->
<?php
}

get_footer();
