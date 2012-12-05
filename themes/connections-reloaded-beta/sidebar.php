<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<ul>
<?php if (have_posts()) { while (have_posts()) { the_post(); if (is_single()) { ?>
	<li id="archivedentry">
		<h2><?php _e('Archived Entry'); ?></h2>
		<ul>
			<li><strong><?php _e('Post Date :'); ?></strong></li>
			<li><?php the_time('l, M jS, Y'); _e(' at '); the_time() ?></li>
			<li><strong><?php _e('Category :'); ?></strong></li>
			<li><?php the_category(' and '); ?></li>
			<?php if (function_exists('the_tags')) { ?>
			<li><strong><?php _e('Tags :'); ?></strong></li>
			<li><?php the_tags('',', ' , ''); ?></li>
			<?php } ?>
			<li><strong><?php _e('Do More :'); ?></strong></li>
			<li><?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) 
				{
					// Both Comments and Pings are open 
					_e('You can <a href="#respond">leave a response</a>, or <a href="'); trackback_url(display); _e('">trackback</a> from your own site.'); 
				}
				elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status))
				{
					// Only Pings are Open 
					_e('Responses are currently closed, but you can <a href="'); trackback_url(display); _e('">trackback</a> from your own site.');
				}
				elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status))
				{
					// Comments are open, Pings are not
					_e('You can skip to the end and leave a response. Pinging is currently not allowed.');
				}
				elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status))
				{
					// Neither Comments, nor Pings are open
					_e('Both comments and pings are currently closed.');
				}
				edit_post_link(' Edit this entry.','',''); ?>
			</li>
		</ul>
	</li>
<?php }}} ?>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) { ?>
	<li>
	<?php /* If this is a category archive */ if (is_category()) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the ') ?>
	    <?php single_cat_title(''); ?>
	    <?php _e('category.') ?></p></li></ul>
	<?php /* If this is a tag archive */ } elseif (is_tag()) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the tag ') ?>
	    <?php single_tag_title(''); ?></p></li></ul>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the day ') ?>
	    <?php the_time('l, F jS, Y'); ?></p></li></ul>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the month ') ?>
	    <?php the_time('F, Y'); ?></p></li></ul>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives for the year ') ?>
	    <?php the_time('Y'); ?></p></li></ul>
	<?php /* If this is paged */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2><?php _e('Currently Browsing') ?></h2>
		<ul><li><p><?php _e('You are currently browsing the archives of '); ?><a href="<?php echo get_settings('siteurl'); ?>"><?php echo bloginfo('name'); ?></a></p></li></ul>
	<?php } ?>
	</li>
	
	<li id="calendar">
		<?php get_calendar(); ?>
	</li>

	<?php if(is_home()) { ?>
	<li id="about">
		<h2><?php _e('About the Site:'); ?></h2>
		<ul>
			<li><?php bloginfo('description'); ?></li>
		</ul>
	</li>
	<?php } ?>
	
	<li id="pages">
		<h2><?php _e('Pages:'); ?></h2>
		<ul>
			<?php wp_list_pages('sort_column=menu_order&depth=1&title_li=');    ?>
		</ul>
	</li>
	
	<li id="categories">
		<h2><?php _e('Categories:'); ?></h2>
		<ul>
			<?php wp_list_cats('sort_column=name&optioncount=1');    ?>
		</ul>
	</li>
	
	<?php if ( function_exists('wp_tag_cloud') ) { ?>
	<li id="tags">
		<h2><?php _e('Popular Tags:'); ?></h2>
			<?php wp_tag_cloud('smallest=8&largest=22&format=list');    ?>
	</li>
	<?php } ?>
	
	<li id="search">
		<h2><label for="s"><?php _e('Search:'); ?></label></h2>
		<ul>
			<li>
				<form id="searchform" method="get" action="<?php echo esc_url( $_SERVER['PHP_SELF'] ); ?>">
					<div style="text-align:center">
						<p><input type="text" name="s" id="s" size="15" /></p>
						<p><input type="submit" name="submit" value="<?php _e('Search'); ?>" /></p>
					</div>
				</form>
			</li>
		</ul>
	</li>
	
	<?php if(is_home()) { ?>
	<li id="links">
		<h2><?php _e('Links:'); ?></h2>
		<ul>
		<?php
			if (substr(get_bloginfo('version'), 0, 3) < 2.1)
			{
				$cats = $wpdb->get_results("SELECT cat_id, cat_name FROM $wpdb->linkcategories");
			}
			else
			{
				$cats = get_categories("type=link&orderby=name&order=ASC&hierarchical=0");
			}
			
			if ($cats) {
				foreach ($cats as $cat) {
					// Handle each category.
					if (substr(get_bloginfo('version'), 0, 3) < 2.1)
					{
						// Display the category name
						echo '	<li id="linkcat-' . $cat->cat_id . '"><h3>' . $cat->cat_name . "</h3>\n\t<ul>\n";
						// Call get_links() with all the appropriate params
						get_links($cat->cat_id,'<li>',"</li>","\n", TRUE, 'name', FALSE, FALSE, -1, FALSE);
					}
					else
					{
						// Display the category name
						echo '	<li id="linkcat-' . $cat->cat_ID . '"><h3>' . $cat->cat_name . "</h3>\n\t<ul>\n";
						// Call get_links() with all the appropriate params
						get_links($cat->cat_ID,'<li>',"</li>","\n", TRUE, 'name', FALSE, FALSE, -1, FALSE);
					}
					// Close the last category
					echo "\n\t</ul>\n</li>\n";
				}
			}
		?>
		</ul>
	</li>
	
	<li id="subscriptions">
		<h2><?php _e('Subscriptions:'); ?></h2>
		<ul>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>"><img src="<?php bloginfo('template_directory'); ?>/img/add-rss2.0.png" alt="<?php _e('Syndicate this site using RSS'); ?>" width="80" height="15" /></a></li>
		    <li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><img src="<?php bloginfo('template_directory'); ?>/img/comments-rss.png" alt="<?php _e('The latest comments to all posts in RSS'); ?>" width="80" height="15" /></a></li>
		    <li><a href="http://add.my.yahoo.com/rss?url=<?php bloginfo('rss2_url'); ?>" title="Add to My Yahoo!"><img src="http://us.i1.yimg.com/us.yimg.com/i/us/my/addtomyyahoo4.gif" alt="Add to My Yahoo!" /></a></li>
		    <li><a href="http://my.msn.com/addtomymsn.armx?id=rss&amp;ut=<?php bloginfo('rss2_url'); ?>&amp;tt=CENTRALDIRECTORY&amp;ru=http://rss.msn.com'"><img src="http://sc.msn.com/c/rss/rss_mymsn.gif" alt="Add to My MSN" /></a></li>
		    <li><a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url=<?php bloginfo('rss2_url'); ?>" title="Subscribe in NewsGator Online"><img src="http://www.newsgator.com/images/ngsub1.gif" alt="Subscribe in NewsGator Online" width="91" /></a></li>
		    <li><a href="http://www.newsburst.com/Source/?add=<?php bloginfo('rss2_url'); ?>"><img src="http://i.i.com.com/cnwk.1d/i/newsbursts/btn/newsburst3.gif" alt="Add your feed to Newsburst from CNET News.com" width="96" height="20" /></a></li>
		    <li><a href="http://www.rojo.com/add-subscription?resource=<?php bloginfo('rss2_url'); ?>"><img src="http://www.rojo.com/skins/static/images/add-to-rojo.gif" alt="Subscribe in Rojo" width="52" height="17" /></a></li>
			<li><a href="http://fusion.google.com/add?feedurl=<?php bloginfo('rss2_url'); ?>" title="Subscribe in Google Reader"><img src="<?php bloginfo('template_directory'); ?>/img/add-to-google-plus.gif" alt="Subscribe in Google Reader" width="104" height="17" /></a></li>
		    <li><a href="http://client.pluck.com/pluckit/prompt.aspx?GCID=C12286x053&amp;a=<?php bloginfo('rss2_url'); ?>&amp;t=<?php bloginfo('name'); ?>"><img src="http://www.pluck.com/images/pluck/pluspluck.png" alt="Subscribe with Pluck RSS reader" width="91" height="17" /></a></li>
		    <li><a href="http://www.bloglines.com/sub/<?php bloginfo('rss2_url'); ?>"><img src="http://www.bloglines.com/images/sub_modern1.gif" alt="Subscribe with Bloglines" width="94" height="20" /></a></li>
			<li><a href="http://www.feedster.com/myfeedster.php?action=addrss&amp;rssurl=<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('template_directory'); ?>/img/addtomyfeedster.gif" alt="Subscribe with Bloglines" width="68" height="13" /></a></li>
			<li><a href="http://www.furl.net/storeIt.jsp?t=<?php bloginfo('name'); ?>&amp;u=<?php bloginfo('url'); ?>" title="Furl It!"><img src="<?php bloginfo('template_directory'); ?>/img/FurlIt.png" alt="Furl It!" width="80" height="15" /></a></li>
		</ul>
	</li>
	<?php } ?>
	
	<li id="meta">
		<h2><?php _e('Meta'); ?></h2>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="http://validator.w3.org/check/referer" title="Valid W3C XHTML 1.0 Transitional"><img src="<?php bloginfo('template_directory'); ?>/img/button_xhtml.gif" alt="Valid W3C XHTML 1.0 Transitional" width="80" height="15" /></a></li>
			<li><a href="http://jigsaw.w3.org/css-validator/check/referer" title="Valid W3C CSS"> <img src="<?php bloginfo('template_directory'); ?>/img/button_css.gif" alt="Valid W3C CSS" /></a></li>
			<li><a href="http://feedvalidator.org/check.cgi?url=<?php bloginfo('rss2_url'); ?>" title="Valid RSS 2.0"><img src="<?php bloginfo('template_directory'); ?>/img/button_rss.gif" alt="Valid RSS 2.0" width="80" height="15" /></a></li>
			<li><a href="http://feedvalidator.org/check.cgi?url=<?php bloginfo('atom_url'); ?>" title="Valid Atom 0.3"><img src="<?php bloginfo('template_directory'); ?>/img/button_atom.gif" alt="Valid Atom 0.3" width="80" height="15" /></a></li>
			<?php wp_meta(); ?>
		</ul>		
	</li>

<?php } ?>
	
	<?php if (function_exists('wp_theme_switcher')) { ?>
	<li id="theme-switcher">
		<h2><?php _e('Themes:'); ?></h2>
		<?php wp_theme_switcher(); ?>
	</li>
	<?php } ?>
	
</ul>
