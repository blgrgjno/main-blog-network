<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<ul>
<?php if (have_posts()) { while (have_posts()) { the_post(); if (is_single()) { ?>
	<!-- <li id="archivedentry">
		<h2><?php _e('Archived Entry', 'dss-loaded'); ?></h2>
		<ul>
			<li><strong><?php _e('Post Date :', 'dss-loaded'); ?></strong></li>
			<li><?php the_time('l, M jS, Y'); _e(' at ', 'dss-loaded'); the_time() ?></li>
			<li><strong><?php _e('Category :', 'dss-loaded'); ?></strong></li>
			<li><?php the_category(' and '); ?></li>
			<?php if (function_exists('the_tags')) { ?>
			<li><strong><?php _e('Tags :', 'dss-loaded'); ?></strong></li>
			<li><?php the_tags('',', ' , ''); ?></li>
			<?php } ?>
			<li><strong><?php _e('Do More :', 'dss-loaded'); ?></strong></li>
			<li><?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) 
				{
					// Both Comments and Pings are open 
					_e('You can <a href="#respond">leave a response</a>, or <a href="', 'dss-loaded'); trackback_url(display); _e('">trackback</a> from your own site.', 'dss-loaded'); 
				}
				elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status))
				{
					// Only Pings are Open 
					_e('Responses are currently closed, but you can <a href="', 'dss-loaded'); trackback_url(display); _e('">trackback</a> from your own site.', 'dss-loaded');
				}
				elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status))
				{
					// Comments are open, Pings are not
					_e('You can skip to the end and leave a response. Pinging is currently not allowed.', 'dss-loaded');
				}
				elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status))
				{
					// Neither Comments, nor Pings are open
					_e('Both comments and pings are currently closed.', 'dss-loaded');
				}
				edit_post_link(' Edit this entry.','',''); ?>
			</li>
		</ul>
	</li>-->
<?php }}} ?>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) { ?>
  <li>
	<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the ', 'dss-loaded') ?>
	    <?php single_cat_title(''); ?>
	    <?php _e('category.', 'dss-loaded') ?></p></li></ul>
	<?php /* If this is a tag archive */ } elseif (is_tag()) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the tag ', 'dss-loaded') ?>
	    <?php single_tag_title(''); ?></p></li></ul>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the day ', 'dss-loaded') ?>
	    <?php the_time('l, F jS, Y'); ?></p></li></ul>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the month ', 'dss-loaded') ?>
	    <?php the_time('F, Y'); ?></p></li></ul>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the year ', 'dss-loaded') ?>
	    <?php the_time('Y'); ?></p></li></ul>
	<?php /* If this is paged */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2><?php _e('Currently Browsing', 'dss-loaded') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives of ', 'dss-loaded'); ?><a href="<?php echo get_settings('siteurl'); ?>"><?php echo bloginfo('name'); ?></a></p></li></ul>
	<?php } ?>
	</li>


<!--	<li id="calendar">
		<?php get_calendar(); ?>
	</li>
-->

    <?php if(is_home()) { ?>
<!--	<li id="about">
		<h2><?php _e('About the Site:', 'dss-loaded'); ?></h2>
		<ul>
			<li><?php bloginfo('description'); ?></li>
		</ul>
	</li> -->
	<?php } ?>

	<?php 
	  if (class_exists('Simple_Page_Widget')) {
	    the_widget('Simple_Page_Widget');
	  }
	  
	  the_widget('WP_Widget_Recent_Posts');	  
	  the_widget('WP_Widget_Recent_Comments');

	  if (function_exists('widget_social_links')) {
	    the_widget('widget_social_links');
	  }

 ?>


	
<!--	
	<li id="search">
		<h2><label for="s"><?php _e('Search:', 'dss-loaded'); ?></label></h2>
		<ul>
			<li>
				<form id="searchform" method="get" action="<?php echo esc_url( $_SERVER['PHP_SELF'] ); ?>">
					<div style="text-align:center">
						<p><input type="text" name="s" id="s" size="15" /></p>
						<p><input type="submit" name="submit" value="<?php _e('Search', 'dss-loaded'); ?>" /></p>
					</div>
				</form>
			</li>
		</ul>
	</li>-->

<!--	<li id="meta">
		<h2><?php _e('Meta', 'dss-loaded'); ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="http://validator.w3.org/check/referer" title="Valid W3C XHTML 1.0 Transitional"><img src="<?php bloginfo('template_directory'); ?>/img/button_xhtml.gif" alt="Valid W3C XHTML 1.0 Transitional" width="80" height="15" /></a></li>
			<li><a href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid W3C CSS"> <img src="<?php bloginfo('template_directory'); ?>/img/button_css.gif" alt="Valid W3C CSS" /></a></li>
			<li><a href="http://feedvalidator.org/check.cgi?url=<?php bloginfo('rss2_url'); ?>" title="Valid RSS 2.0"><img src="<?php bloginfo('template_directory'); ?>/img/button_rss.gif" alt="Valid RSS 2.0" width="80" height="15" /></a></li>
			<li><a href="http://feedvalidator.org/check.cgi?url=<?php bloginfo('atom_url'); ?>" title="Valid Atom 0.3"><img src="<?php bloginfo('template_directory'); ?>/img/button_atom.gif" alt="Valid Atom 0.3" width="80" height="15" /></a></li>
			<?php wp_meta(); ?>
		</ul>		
	</li>-->

<?php } ?>
	
	<?php if (function_exists('wp_theme_switcher')) { ?>
	<li id="theme-switcher">
		<h2><?php _e('Themes:', 'dss-loaded'); ?></h2>
		<?php wp_theme_switcher(); ?>
	</li>
	<?php } ?>
</ul>