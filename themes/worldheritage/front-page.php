<?php
/**
 * The front page of the theme
 *
 * @package WordPress
 * @subpackage World Heritage
 * @since World Heritage 1.0
 */


get_header();
?>

<div id="header-thumb"><?php
	if ( have_posts() ) : while ( have_posts() ) :
	the_post();

	// Header image caption
	$header_caption = get_post_meta( get_the_ID(), 'caption' );
	if ( $header_caption )
		echo '<div class="header-caption">' . $header_caption[0] . '</div>';

	endwhile;
	endif;

// Check to see if the header image has been removed
// The header image
// Check if this is a post or page, if it has a thumbnail, and if it's a big one
if ( is_singular() &&
		has_post_thumbnail( $post->ID ) &&
		( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) ) ) &&
		$image[1] >= HEADER_IMAGE_WIDTH ) :
	// Houston, we have a new header image!
	echo '<a href="' . home_url( '/' ) . '">' . get_the_post_thumbnail( $post->ID, 'header' ) . '</a>';
else : ?>
<a href="<?php echo home_url( '/' ); ?>"><img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" alt="" /></a>
<?php endif; // end check for featured image or standard header ?>
</div>

<h2 id="bread-crumbs"><a href="<?php echo home_url(); ?>">Home</a></h2>


<hr class="heading-seperator" />

<?php
$count = 0;
query_posts ('posts_per_page=6&post_type=page&orderby=menu_order&order=ASC' );
if ( have_posts() ) : while ( have_posts() ) :
	the_post();
	?>

<aside<?php
	if ( $count % 2 )
		echo ' class="alt"';
?>>
	<div class="link">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php

		// Post thumbnails
		if ( MultiPostThumbnails::has_post_thumbnail( 'page', 'home-thumbs' ) ) {
			echo '<a href="' . get_permalink() . '">';
			MultiPostThumbnails::the_post_thumbnail( 'page', 'home-thumbs' );
			echo '</a>';
		}
	?>
	</div>
	<?php if ( comments_open() && ! post_password_required() ) : ?>
	<?php endif; ?>
	<?php the_excerpt(); ?>
	<div class="comments-link"><?php
		$comments = get_comments_number( get_the_ID() );
		if ( 0 < $comments )
			echo '<a href="' . get_permalink() . '#comments">' . $comments . ' comments</a>';
		?>
	</div>
</aside><?php

	// Seperator between lines
	if ( $count % 2 )
		echo "\n	<div class='box-seperator'></div>\n";

	$count++;
endwhile; endif;



get_footer();
