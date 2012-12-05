<?php
/**
 * The static article pages of the theme
 *
 * @package WordPress
 * @subpackage World Heritage
 * @since World Heritage 1.0
 */


/**
 * The header
 *
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since World Heritage 1.0
 */
get_header();


/**
 * The WordPress loop
 * Displays the main page content
 *
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since World Heritage 1.0
 */
if ( have_posts() ) : while ( have_posts() ) :
	the_post();

	// Display post thumbnail at top of content
	if ( has_post_thumbnail( $post->ID ) ) :
		?>

		<div id="header-thumb"><?php

		// Header image caption
		$header_caption = get_post_meta( get_the_ID(), 'caption' );
		if ( $header_caption )
			echo '<div class="header-caption">' . $header_caption[0] . '</div>';

		?>
		<a href="<?php echo home_url( '/' ); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'article' ); ?></a>
		</div><?php
	endif;
?>

<h2 id="bread-crumbs"><a href="<?php echo home_url(); ?>">Home</a></h2>

<h2 id="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php

// Output a subheading if it exists
$subheading = get_post_meta( get_the_ID(), 'subheading' );
if ( $subheading )
	echo '<p class="subheading">' . $subheading[0] . '</p>';
?>

<hr class="heading-seperator" />

<nav id="pages-menu">
	<ul><?php
		if ( $post->post_parent ) {
			$ancestors = get_post_ancestors( $post->ID );
			$root = count( $ancestors ) - 1;
			$parent = $ancestors[$root];
		} else {
			$parent = $post->ID;
		}

		$args = array(
			'child_of' => $parent,
			'title_li' => '',
			'exclude'  => get_the_ID(),
		);
		wp_list_pages( $args );
	?></ul>
</nav>

<article>
	<?php

	// The page content
	the_content(); 

	// Load comments
	comments_template();
	?>
</article><?php

endwhile; endif;



/**
 * The footer
 *
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since World Heritage 1.0
 */
get_footer();
