<?php


// Kill page for public
if ( ! isset( $_GET['showsplash'] ) && ! is_admin() ) {
	die;
}
if ( 'marthe-is-cool' != $_GET['showsplash'] ) {
	die;
}


get_header();

echo apply_filters( 'the_content', get_option( 'dss_blog_description', '' ) );

// Loop through all of the sites
$sites = $ms_splash->get_option( 'blog-order' );
// shuffle( $sites ); // Randomises the display order of blogs
foreach( $sites as $key => $site_id ) {

	// Switch to the site in question and dump out most recent post data
	switch_to_blog( $site_id );
	?>
	<article id="blog-<?php echo $site_id; ?>" class="post">
		<header class="entry-header">
			<hgroup>
				<h2 class="entry-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Permalenke til <?php echo esc_attr( get_bloginfo( 'blogname' ) ); ?>" rel="bookmark">
						<?php echo esc_html( get_bloginfo( 'blogname' ) ); ?>
					</a>
				</h2><?php

				// Check for custom DSS header image set by Super Admin (not the standard header image)
				if ( '' != get_option( 'dss_blog_headerimage' ) ) {
					$header_image = get_option( 'dss_blog_headerimage' );
					$header_attributes = '';
				}
				// If no custom DSS header set, then use regular WordPress header
				else {
					$header_image = get_header_image();
					$header_attributes = 'width="' . get_custom_header()->width . '" height="' . get_custom_header()->height . '"';
				}

				// If there is a header image, then display it
				if ( $header_image )  {					
				?>
				<a id="header-image" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo $header_image; ?>" <?php echo $header_attributes; ?> alt="" />
				</a><?php
				} ?>
				
				
				
				
			</hgroup>
		</header><!-- .entry-header -->
		<div class="entry-summary">
			<p><?php
				$description = get_option( 'dss_blog_description', '' );
				if ( '' == $description ) {
					$description = bloginfo( 'description' );
				}
				echo $description;
			?></p>
		</div><!-- .entry-summary -->
	</article><!-- #blog-<?php echo $site_id; ?> --><?php

}
switch_to_blog( 1 );

get_footer();

