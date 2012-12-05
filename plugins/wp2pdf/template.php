<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<meta charset="UTF-8" />
	<title><?php bloginfo( 'title' ); ?></title>
	<link rel='stylesheet' href='<?php echo WP2PDF_URL; ?>/style.css' type='text/css' media='all' />
</head>
<body>

	<img src="<?php echo WP2PDF_URL; ?>/dss-logo.png" style="width:20%;margin-left:40%;" alt="" />
	<hr />
	<br />

	<p><strong><?php _e( 'Archive of' ); ?> <?php bloginfo( 'title' ); ?>. <?php _e( 'PDF created on' ); ?> <?php echo date( 'Y/m/d' ); ?></p></strong><?php	


// The Query
if ( 'page' == get_post_type( $post_id ) ) {
	$args = array( 'page_id' => $post_id );
}
elseif ( 'post' == get_post_type( $post_id ) ) {
	$args = array( 'p' => $post_id );
}

$the_query = new WP_Query( $args );
global $more; // Declare global $more (before the loop).

// The Loop
while ( $the_query->have_posts() ) {
	global $wpdb;
	$the_query->the_post();
	$more = 1; // Set (inside the loop) to display all content, including text below more.
	?>
	<div class="post">
		<h2><?php the_title(); ?></h2><?php

		if ( 0 == $comment_offset ) {
			the_content();
			$comment_offset = 10;
		} else {

			// Display the comments
			$args = array(
				'post_id' => $post_id,
				'status'  => 'approve',
				'offset'  => $comment_offset,
				'number'  => 10,
			);
			$args = apply_filters( 'wp2pdf_comment_args', $args ); // This filter was added due to an issue on the DSS blog network which caused Disquis hosted comments on their home page to be listed with NO url
			$comments = get_comments( $args );
			unset( $any_comments );
			foreach( $comments as $comment ) {
				$any_comments = true;
				?>
				<hr />
				<h6><?php echo $comment_offset . ' ';_e( 'Comment by' ); ?> <?php echo $comment->comment_author; ?>(<?php echo $comment->comment_author_email; ?>). <?php _e( 'Comment posted on' ); ?> <?php echo $comment->comment_date; ?>. <?php _e( 'Author IP address' ); ?>: <?php echo $comment->comment_author_IP; ?></h6>
				<p><?php echo $comment->comment_content; ?></p><?php
			}
			if ( isset( $any_comments ) ) {
				$comment_offset = $comment_offset + 10;
				unset( $any_comments );
			} else {
				unset( $comment_offset );
			}
		}
		?>
	</div><?php
}

?>

</body>
</html>