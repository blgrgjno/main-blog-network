<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php get_header()?>	
<div id="main">
	<div id="content">
		<h2 class="center"><?php _e('Error 404 - Not Found') ?></h2>
		<p><?php _e('The page you are looking for cannot be found on this site. You can use the search box to the left to find the page or go to the ') ?><a href="<?php bloginfo('url'); ?>"><?php _e('home page') ?></a>.</p>
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>
<?php  get_footer();?>
