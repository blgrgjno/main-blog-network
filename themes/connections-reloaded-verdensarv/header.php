<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/1">
	<title><?php wp_title( '' ); ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
	</style>	

	<link rel="icon" <?php if ($conrel_favicon!='') { ?>href="<?php echo $conrel_favicon; ?>"<?php } else { ?>href="<?php bloginfo('url')?>/favicon.ico"<?php } ?> type="image/x-icon" />
	<link rel="SHORTCUT ICON" <?php if ($conrel_favicon!='') { ?>href="<?php echo $conrel_favicon; ?>"<?php } else { ?>href="<?php bloginfo('url')?>/favicon.ico"<?php } ?> type="image/x-icon" />

	<?php if ($conrel_feed!='') { ?>
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo $conrel_feed; ?>" />
	<link rel="alternate" type="application/x-opera-widgets" title="<?php bloginfo('name'); ?> Feed" href="http://widgets.opera.com/widgetize/Feed%20Reader/Advanced/?serve&amp;skin=skin7&amp;widgetname=<?php rawurlencode(bloginfo('name')); ?>&amp;url=<?php echo $conrel_feed; ?>&amp;rel=myopera&amp;ref=" />  
	<?php } else { ?>
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="application/x-opera-widgets" title="<?php bloginfo('name'); ?> Feed" href="http://widgets.opera.com/widgetize/Feed%20Reader/Advanced/?serve&amp;skin=skin7&amp;widgetname=<?php rawurlencode(bloginfo('name')); ?>&amp;url=<?php rawurlencode(bloginfo('rss2_url')); ?>&amp;rel=myopera&amp;ref=" />  
	<?php } ?>
	
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>

	<?php echo $conrel_header_stuff; ?>
	
	<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
	<?php wp_head(); ?>
</head>

<body>
<div id="rap">
  <div id="header">
<?php 
function conrel_page_menu()
{
?><ul id="topnav">
	  <li <?php if(is_home()){echo 'class="current_page_item"';}?>><a href="<?php bloginfo('siteurl'); ?>" title="<?php _e('Home', 'dss-loaded'); ?>"><?php _e('Home', 'dss-loaded'); ?></a></li>
	  <?php 
		if($conrel_header_menu) { 
	      wp_list_pages('title_li=&depth=1&include='.$conrel_header_menu); } else { wp_list_pages('title_li=&depth=1&number=5'); }

	  ?>
	</ul>
<?php
}
/*wp_nav_menu( array(
'container' => 'ul',
'menu_id' => 'topnav',
'fallback_cb' => 'conrel_page_menu',
'theme_location' => 'primary'
)); */
conrel_page_menu();
?>

	<div id="headimg">
<!--	  <h1><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>"><?php bloginfo('description'); ?></a></h1>		
	  <div id="desc"><?php bloginfo('name');?></div>
-->
	  </div>
  </div>
