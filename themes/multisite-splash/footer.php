<?php
$ms_splash = new Multisite_Splash_Core();
?>

			</div><!-- #content -->
		</div><!-- #primary -->

		<div id="secondary" class="widget-area" role="complementary">
			<aside class="widget">
				<h3 class="widget-title">Om Bloggene</h3>
				<?php
					$om_page = get_page_by_path( 'om' );
					$om_page = get_post( $om_page->ID );
					echo apply_filters( 'the_content', $om_page->post_excerpt );
				?>
			</aside>
			<aside class="widget">
				<h3 class="widget-title">Nyeste innlegg</h3>
				<ul><?php

				// Loop through all of the sites
				$sites = $ms_splash->get_option( 'blog-order' );
				shuffle( $sites ); // Alters order of sites at random to ensure that all blogs are represented
				foreach( $sites as $key => $site_id ) {
					if ( $key < 6 ) { // Limit the number displayed
						$url  = get_blog_option( $site_id, 'siteurl' );
						$name = get_blog_option( $site_id, 'blogname' );

						// Switch to the site in question and dump out most recent post data
						switch_to_blog( $site_id );
						$args = array(
							'numberposts' => 1,
						);
						$recent_posts = get_posts( $args );
						foreach( $recent_posts as $recent ) {
							echo '<li>
								<a href="' . $url . '">' . $name . '</a>
								<!--<a href="' . get_permalink( $recent->ID ) . '" title="Look '.esc_attr( $recent->post_title ).'" >' .
									$recent->post_title .
								'</a>-->
							</li>';
						}

					}
				}
				switch_to_blog( 1 );
				?>
				</ul>
			</aside>
			<aside class="widget">
				<h3 class="widget-title">Nyeste kommentar</h3>
				<ul>
				<?php
				// Loop through all the selected sites
				$site_comment = array();
				$sites = $ms_splash->get_option( 'blog-order' );
				shuffle( $sites ); // Alters order of sites at random to ensure that all blogs are represented
				foreach( $sites as $key => $site_id ) {
					if ( $key < 6 ) { // Limit the number displayed
						$url  = get_blog_option( $site_id, 'siteurl' );
						$name = get_blog_option( $site_id, 'blogname' );
			
						// Switch to the site in question and dump out most recent post data
						switch_to_blog( $site_id );
						$comments = get_comments();
						unset( $end );
						foreach( $comments as $comment ) {
							$comment_time = strtotime( $comment->comment_date );
							if ( $comment_time < ( time() - MSS_COMMENT_TIME_BUFFER ) ) {
								if ( ! isset( $end ) ) {

									$comment_content = esc_html( $comment->comment_content );
									$comment_excerpt = $ms_splash->limit_string( $comment_content, 100, ' ... ' );

									echo '
									<li>
										<a href="' . get_permalink( $comment->comment_post_ID ) . '">' .
										$comment_excerpt .
										'</a>' . '<br />' . 
										esc_html( $comment->comment_author ) . ' om ' .
										esc_html( get_the_title( $comment->comment_post_ID ) ) . '
									</li>';
								}
								$end = true;
							}
						}
					}
				}
				switch_to_blog( 1 );
				?>
				</ul>
			</aside>

			<?php
			// Display the sidebar widgets
			dynamic_sidebar( 'sidebar' );
			?>

		</div><!-- #secondary .widget-area -->
		<div style="clear:both;"></div>
	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">
		<div id="site-generator">
			<a href="<?php echo home_url( '/' ); ?>" title="Website by DSS">Website by DSS</a>
			<br />
			<?php
			wp_nav_menu(
				array(
					'container_class' => 'menu-footer',
					'menu'  => 'footer',
				)
			); ?>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>