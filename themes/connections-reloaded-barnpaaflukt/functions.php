<?php

function bpf_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

add_filter( 'http_request_args', 'bpf_hidden_theme', 5, 2 );


$themecolors = array(
	'bg' => 'F3F6ED',
	'text' => '29303B',
	'link' => '909D73',
	);

// Custom Header
define('HEADER_TEXTCOLOR', 'B5C09D');
define('HEADER_IMAGE', '%s/img/train.jpg'); // %s is theme dir uri
define('HEADER_IMAGE_WIDTH', 741);
define('HEADER_IMAGE_HEIGHT', 142);
//define( 'NO_HEADER_TEXT', true );

// removed custom header and moved to child theme

// Enable WordPress 2.9 Post Thumbnails feature
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150 );
}

// Connections reloaded theme options page
require_once("theme-options.php");

?>
<?php
if ( function_exists('register_sidebar') )
	register_sidebar(array(
		'before_widget' => '<li id="%1$s" class="widget %2$s">', 
		'after_widget' => '</li></ul></li>',
		'before_title' => '<h2>',
		'after_title' => '</h2><ul><li>', 
	));

function widget_conrel_search() {
?>
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
	</li>
<?php
}
function widget_conrel_calendar() {
?>
	<li id="calendar">
		<?php get_calendar(); ?>
	</li>
<?php
}
function widget_conrel_tags() {
  $options = get_option('widget_conrel_tags');
?>
	<li id="tags">
		<h2><?php echo $options['title'] ?></h2>
			<?php wp_tag_cloud('smallest=8&largest=22&format=list');    ?>
	</li>
<?php
}
function widget_conrel_tags_control() {
  $options = $newoptions = get_option('widget_conrel_tags');
  if ( $_POST["conrel-tags-submit"] ) {
	  $newoptions['title'] = strip_tags(stripslashes($_POST["conrel-tags-title"]));
	  if ( empty($newoptions['title']) ) $newoptions['title'] = 'Popular Tags';
  }
  if ( $options != $newoptions ) {
	  $options = $newoptions;
	  update_option('widget_conrel_tags', $options);
  }
  $title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
<p><label for="conrel-tags-title"><?php _e('Title:', 'dss-loaded'); ?> <input style="width: 250px;" id="conrel-tags-title" name="conrel-tags-title" type="text" value="<?php echo $title; ?>" /></label></p>
<input type="hidden" id="conrel-tags-submit" name="conrel-tags-submit" value="1" />
<?php
}

if ( function_exists('register_sidebar_widget') )
{
//	register_sidebar_widget(__('Search', 'dss-loaded'), 'widget_conrel_search');
//	register_sidebar_widget(__('Calendar', 'dss-loaded'), 'widget_conrel_calendar');
//	register_sidebar_widget(__('Tag Cloud', 'dss-loaded'), 'widget_conrel_tags');
//	register_widget_control(__('Tag Cloud', 'dss-loaded'), 'widget_conrel_tags_control', null, 175);
}

/* Add Comment support for version prior to WordPress 2.7 */
add_filter('comments_template', 'legacy_comments');
function legacy_comments($file) {
	if(!function_exists('wp_list_comments')) 	$file = TEMPLATEPATH . '/legacy.comments.php';
	return $file;
}

register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'twentyten' ),
	) );

add_theme_support( 'menus' );

?>