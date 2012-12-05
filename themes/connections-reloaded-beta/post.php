<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<p class="post-date"><?php the_time('D j M Y'); ?></p>
<div class="post-info">
	<h2 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
	<p><?php _e('Posted by '); ?><?php the_author(); ?><?php _e(' under '); ?><?php the_category(', '); ?><?php edit_post_link(' (edit this)'); ?>
	<br/>
	<?php comments_popup_link('No Comments', '1 Comment', '[%] Comments'); ?></p>
</div>
<div class="post-content">
<?php
	if ((function_exists('has_post_thumbnail')) && (has_post_thumbnail())&& ($conrel_thumbnails =="true")) {
		the_post_thumbnail();
	} else {
		$postimage = get_post_meta($post->ID, 'post-image', true);
		if ($postimage) {
			echo '<img src="'.$postimage.'" alt="" />';
		}
	}
?>
	<?php the_content(); ?>
	<div class="post-info">
		<?php wp_link_pages(); ?>											
	</div>

	<?php if ($conrel_homepage_tags =="true") { } else { ?>
	<?php if (!is_single()) { ?>
	<div class="postmeta">
		<p><?php _e('Tags: '); ?> <?php the_tags('') ?> </p>
	</div>
	<?php } } ?>
		
	<!--
		<?php trackback_rdf(); ?>
	-->
	<div class="post-footer">&nbsp;</div>
</div>