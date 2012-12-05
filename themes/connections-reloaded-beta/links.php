<?php
/*
Template Name: Links
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
							// Display the category name
							echo '	<h3 id="linkcat-' . $cat->cat_ID . '">' . $cat->cat_name . "</h3>\n\t<ul>\n";
							// Call get_links() with all the appropriate params
							if (substr(get_bloginfo('version'), 0, 3) < 2.1)
							{
								get_links($cat->cat_id,'<li>',"</li>","<br />", FALSE, 'name', TRUE, FALSE, -1, FALSE);
							}
							else
							{
								get_links($cat->cat_ID,'<li>',"</li>","<br />", FALSE, 'name', TRUE, FALSE, -1, FALSE);
							}
							// Close the last category
							echo "\n\t</ul>\n\n";
						}
					}
				?>
			</div>
		</div>	
	<?php } } ?>
	
	<!--- middle (main content) column content end -->
	</div>
	<div id="sidebar">
		<?php get_sidebar(); ?>
	</div>

<?php get_footer();?>
