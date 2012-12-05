<?php

function creloadedbeta_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

add_filter( 'http_request_args', 'creloadedbeta_hidden_theme', 5, 2 );

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

function header_style() {
?>
<style type="text/css">
#headimg{
	background:#fff url(<?php header_image() ?>) no-repeat bottom;
}
<?php if ( 'blank' == get_header_textcolor() ) { ?>
#headimg h1, #headimg #desc {
	display: none;
}
<?php } else { ?>
#headimg h1 a, #desc {
	color:#<?php header_textcolor() ?>;
}
<?php } ?>
</style>
<?php
}

function crev_admin_header_style() {
?>
<style type="text/css">
#headimg{
	background:#fff url(<?php header_image() ?>) no-repeat bottom;
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width:<?php echo HEADER_IMAGE_WIDTH; ?>px;
	padding:0;
}

#headimg h1{
	margin: 0;	
	font-size: 1.6em;	
	padding:10px 20px 0 0;
	text-align:right;	
}
#headimg h1 a{
	color:#<?php header_textcolor() ?>;
	text-decoration:none;
}
#headimg #desc{
	color:#<?php header_textcolor() ?>;
	font-weight:normal;
	font-style:italic;
	font-size:1em;
	text-align:right;
	margin:0;
	padding:0 20px 0 0;
}

<?php if ( 'blank' == get_header_textcolor() ) { ?>
#headimg h1, #headimg #desc {
	display: none;
	text-decoration:none;
}
#headimg h1 a, #headimg #desc {
	color:#<?php echo HEADER_TEXTCOLOR ?>;
}
<?php } ?>

</style>
<?php
}
add_custom_image_header('header_style', 'crev_admin_header_style');

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
<p><label for="conrel-tags-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="conrel-tags-title" name="conrel-tags-title" type="text" value="<?php echo $title; ?>" /></label></p>
<input type="hidden" id="conrel-tags-submit" name="conrel-tags-submit" value="1" />
<?php
}

if ( function_exists('register_sidebar_widget') )
{
	register_sidebar_widget(__('Search'), 'widget_conrel_search');
	register_sidebar_widget(__('Calendar'), 'widget_conrel_calendar');
	register_sidebar_widget(__('Tag Cloud'), 'widget_conrel_tags');
	register_widget_control(__('Tag Cloud'), 'widget_conrel_tags_control', null, 175);
}

/* Add Comment support for version prior to WordPress 2.7 */
add_filter('comments_template', 'legacy_comments');
function legacy_comments($file) {
	if(!function_exists('wp_list_comments')) 	$file = TEMPLATEPATH . '/legacy.comments.php';
	return $file;
}

?>