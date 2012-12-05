<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php get_header(); ?>
<div id="main">
	<div id="content">
	<!--- middle (posts) column  content begin -->
		<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
			<?php if (function_exists('post_class')) { ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php } else { ?>
			<div class="post">
			<?php } ?>
				<?php require('post.php'); ?>
				<?php comments_template(); // Get wp-comments.php template ?>
			</div>
		<?php } } else { ?>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php } ?>
		<p class="center"><?php posts_nav_link() ?></p>	
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>
