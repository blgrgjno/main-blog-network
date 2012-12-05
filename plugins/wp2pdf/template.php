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
		<h2><?php the_title(); ?></h2>
		<?php the_content(); ?>

		<?php
		// Display the comments
		$comments = $wpdb->get_results("SELECT *,SUBSTRING(comment_content,1,200) AS com_excerpt FROM $wpdb->comments WHERE comment_post_ID = '$post_id' AND comment_approved = '1' ORDER BY comment_date DESC limit 999");
	
		$comments_output = '';
		foreach ( $comments as $comment ) {
			?>
			<hr />
			<h6><?php _e( 'Comment by' ); ?> <?php echo $comment->comment_author; ?>. <?php _e( 'Comment posted on' ); ?> <?php echo $comment->comment_date; ?>.</h6>
			<p><?php echo $comment->comment_content; ?></p><?php
		}
		?>
	</div><?php
}

?>

</body>
</html>
