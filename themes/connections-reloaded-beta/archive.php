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
		<?php if (have_posts()) { ?>

			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* If this is a category archive */ if (is_category()) { ?>
			<h2><?php _e('Archive for the '); echo single_cat_title(); _e(' Category'); ?></h2>
			<div class="post-info"><?php _e('Category Archive'); ?></div>

	 	  	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h2><?php _e('Archive for '); the_time('F jS, Y'); ?></h2>
			<div class="post-info"><?php _e('Daily Archive'); ?></div>

		 	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h2><?php _e('Archive for '); the_time('F, Y'); ?></h2>
			<div class="post-info"><?php _e('Monthly Archive'); ?></div>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h2><?php _e('Archive for '); the_time('Y'); ?></h2>
			<div class="post-info"><?php _e('Yearly Archive'); ?></div>

		  	<?php /* If this is a search */ } elseif (is_search()) { ?>
			<h2><?php _e('Search Results'); ?></h2>

		  	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h2><?php _e('Author Archive'); ?></h2>

			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h2><?php _e('Blog Archives'); ?></h2>

		<?php } ?>
			<div class="left"><?php posts_nav_link('','','&laquo; Previous Entries') ?></div>
			<div class="right"><?php posts_nav_link('','Next Entries &raquo;','') ?></div>
			<div class="clear"></div>
		<?php while (have_posts()) { the_post(); ?>
			<div class="post">
				<?php require('post.php'); ?>
				<?php comments_template(); // Get wp-comments.php template ?>
			</div>
		<?php } } else { ?>
		<h2 class="center">Not Found</h2>
		<p class="center"><?php _e("Sorry, but you are looking for something that isn't here."); ?></p>
	<?php } ?>

	<!--- middle (main content) column content end -->
	</div>
	<div id="sidebar">
			<?php get_sidebar(); ?>
	</div>
<?php  get_footer();?>
