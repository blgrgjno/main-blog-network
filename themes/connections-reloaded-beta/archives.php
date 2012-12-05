<?php
/*
Template Name: Archives
*/
?>

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
		<div class="page">
			<div class="page-info"><h2 class="page-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link: '); the_title(); ?>"><?php the_title(); ?></a></h2>
				<?php /*Posted by <?php the_author(); ?>*/ ?><?php edit_post_link('(edit this)'); ?>
			</div>
			<div class="page-content">
				<h2><?php _e('Archives by Month:'); ?></h2>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
				<h2><?php _e('Archives by Subject:'); ?></h2>
				<ul>
					<?php wp_list_cats(); ?>
				</ul>
			</div>
		</div>	
	<?php } } ?>
	
	<!--- middle (main content) column content end -->
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>

<?php get_footer();?>
