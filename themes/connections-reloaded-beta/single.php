<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php get_header()?>	
<div id="main">
	<div id="content">
	<!--- middle (posts) column  content begin -->
		<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
			<div class="post">
				<?php require('post.php'); ?>
				<div class="navigation">
					<div class="aligncenter"><?php previous_post_link('&laquo; %link') ?> | <?php next_post_link('%link &raquo;') ?></div>
				</div>
				<div class="post-footer">&nbsp;</div>
				<?php comments_template('', true); // Get wp-comments.php template ?>
			</div>
		<?php } } else { ?>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
		<?php } ?>
	<!--- middle (main content) column content end -->
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>
<?php  get_footer();?>
