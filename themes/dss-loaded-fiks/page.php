<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php get_header();?>	
<div id="main">
	<div id="content">
		<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
		<div class="page">
		<div class="page-info"><h2 class="page-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<?php /*Posted by <?php the_author(); ?>*/ ?><?php edit_post_link('(edit this)'); ?></div>
			<div class="page-content">
				<?php the_content(); ?>
				<?php link_pages('<p class="center"><strong>Pages:</strong> ', '</p>', 'number'); ?>
				<?php comments_template(); // Get wp-comments.php template ?>
			</div>
		</div>
	  <?php } } ?>
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>

<?php get_footer();?>
