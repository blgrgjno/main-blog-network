<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
	<?php get_header();?>	
	<div id="main">
	<div id="content">
		<?php if (have_posts()) { ?>
			<h3><?php echo single_cat_title(); ?></h3>
			<div class="post-info"><?php _e('Archived Posts from this Category'); ?></div>
			<br />
			<?php while (have_posts()) { the_post(); ?>				
				<div class="post">
					<?php require('post.php'); ?>
					<?php comments_template(); // Get wp-comments.php template ?>
				</div>
			<?php } ?>
			<p class="center"><?php posts_nav_link() ?></p>		
		<?php } else { ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php } ?>
			
	</div>
	<div id="sidebar">		
		<?php get_sidebar(); ?>
	</div>
<?php get_footer()?>
