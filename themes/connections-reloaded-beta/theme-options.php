<?php
/*Start of Theme Options*/
 
$themename = __("Connections Reloaded");
$shortname = "conrel";
$GLOBALS['template_path'] = get_bloginfo('template_directory');
$options = array (
 
array( "type" => "open-options-div"),

array( "name" => "Header Options",
	"type" => "title"),
array( "type" => "open"),
array( "name" => "Add to header",
	"desc" => "Enter any custom code you would like to add to the header",
	"id" => $shortname."_header_stuff",
	"type" => "textarea",
	"std" => ""),
array( "name" => "Favicon",
	"desc" => "Enter the full path to a 16x16px .gif/.png/.ico file",
	"id" => $shortname."_favicon",
	"type" => "text",
	"std" => ""),
array( "name" => "FeedBurner / Custom Feed",
	"desc" => "Enter the path to your FeedBurner / custom feed. e.g. <a href='http://feeds.feedburner.com/ajaydsouza'>http://feeds.feedburner.com/ajaydsouza</a>",
	"id" => $shortname."_feed",
	"type" => "text",
	"std" => ""),
array( "name" => "Links in header menu",
	"desc" => "Enter a comma separated list of page IDs",
	"id" => $shortname."_header_menu",
	"type" => "text",
	"std" => ""),
array( "type" => "close"),

array( "name" => "Content Options",
	"type" => "title"),
 
array( "type" => "open"),
array( "name" => "Tags on homepage",
	"desc" => "Disable tags after posts' content on non-post sections e.g. homepage, archives etc.",
	"id" => $shortname."_homepage_tags",
	"type" => "checkbox",
	"std" => "false"),	
array( "name" => "Display Post Thumbnails",
	"desc" => "Display post thumbnails in posts. Will be displayed only on single pages/posts (WordPress 2.9 and above)",
	"id" => $shortname."_thumbnails",
	"type" => "checkbox",
	"std" => "false"),	
array( "type" => "close"),

array( "name" => "Footer Options",
	"type" => "title"),
 
array( "type" => "open"),
array( "name" => "Add to footer",
	"desc" => "Enter any custom code you would like to add to the footer",
	"id" => $shortname."_footer_stuff",
	"type" => "textarea",
	"std" => ""),	
array( "type" => "close"),

array( "type" => "submit"),

array( "type" => "clear"),
array( "type" => "close")
 
);
require_once ('theme-options-page.php');
add_action('admin_menu', 'mytheme_add_admin');
?>