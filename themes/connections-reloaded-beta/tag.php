<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php $current_tag = single_tag_title("", false); ?>
<?php get_header() ?>
<div id="main">
	<div id="content">	
	<?php if (have_posts()) { ?>	
		<?php $post = $posts[0]; /* Hack. Set $post so that the_date() works. */ ?>
		<h3><?php _e('Entries tagged with '); echo '&#8220;<strong>'.$current_tag.'</strong>&#8221;.'; ?></h3>			
		<div class="post-info"><?php _e('Did you find what you wanted?') ?></div>		
		<br/>				
		<?php while (have_posts()) { the_post(); ?>				
			<?php require('post.php'); ?>
		<?php } ?>
	<?php } else { ?>
		<h2 class="center"><?php _e('Not Found') ?></h2>
		<p><?php _e('Sorry, no posts were found tagged '); echo '&#8220;<strong>'.$current_tag.'</strong>&#8221;.'; ?></p>
	<?php } ?>		
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>

<?php get_footer(); ?>
