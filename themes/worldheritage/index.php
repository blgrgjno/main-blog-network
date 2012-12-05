<?php
/**
 * Default template
 * 
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

		<div id="header-thumb">
		<?php echo get_the_post_thumbnail( $post->ID, 'article' ); ?>
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
	$pages_menu = get_pages();
	$count = 0;
	foreach( $pages_menu as $key => $menu_item ) {
		if ( $menu_item->ID != get_the_ID() && $count < 6 ) {
			echo '<li><a href="' . get_permalink( $menu_item->ID ) . '">' . $menu_item->post_title . '</a></li>';
			$count++;
		}
	}

	?></ul>
</nav>

<article>
	<?php the_content(); ?>
</article><?php

endwhile; endif;



/**
 * The footer
 *
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since World Heritage 1.0
 */
get_footer();
