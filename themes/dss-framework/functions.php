<?php
/**
 * DSS Framework functions and definitions
 *
 * @package WordPress
 * @subpackage DSS_Framework
 * @since DSS Framework 1.0
 */


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Definitions
 */
define( 'DSS_READ_MORE_LENGTH', 50 );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override dss_setup() in a child theme, add your own dss_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, custom headers
 * 	and backgrounds, and post formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_setup() {

	/* Make DSS Framework available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on DSS Framework, use a find and replace
	 * to change 'dss' to the name of your theme in all the template files.
	 */
	$locale = apply_filters( 'theme_locale', get_locale(), 'dss' );
	load_textdomain( 'dss', WP_LANG_DIR.'/themes/twentyeleven-'.$locale.'.mo' );

	load_theme_textdomain( 'dss', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );

	// Grab DSS Framework's Ephemera widget.
	require( get_template_directory() . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'dss' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// Add support for custom headers.
	$custom_header_support = array(
		// The default header text color.
		'default-text-color' => '000',
		// The height and width of our custom header.
		'width' => apply_filters( 'dss_header_image_width', 976 ),
		'height' => apply_filters( 'dss_header_image_height', 288 ),
		'header-text' => false, 
		// Support flexible heights.
		'flex-height' => true,
		// Random image rotation by default.
		'random-default' => false,
		// Callback for styling the header preview in the admin.
		'admin-head-callback' => 'dss_admin_header_style',
		// Callback used to display the header preview in the admin.
		'admin-preview-callback' => 'dss_admin_header_image',
	);

	add_theme_support( 'custom-header', $custom_header_support );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( $custom_header_support['width'], $custom_header_support['height'], true );

	// Add DSS Framework's custom image sizes.
	// Used for large feature (header) images.
	add_image_size( 'large-feature', $custom_header_support['width'], $custom_header_support['height'], true );
	// Used for featured posts if a large-feature doesn't exist.
	add_image_size( 'small-feature', 500, 300 );
	// Used for post thumbnails
	add_image_size( 'single-post-thumbnail', 135, 135, true );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'chessboard' => array(
			'url'           => '%s/images/headers/chessboard.jpg',
			'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
			'description'   => __( 'Chessboard', 'dss' )
		),
		'health' => array(
			'url'           => '%s/images/headers/health.jpg',
			'thumbnail_url' => '%s/images/headers/health-thumbnail.jpg',
			'description'   => __( 'Health', 'dss' )
		),
	) );
}
add_action( 'after_setup_theme', 'dss_setup' );

if ( ! function_exists( 'dss_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in dss_setup().
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 32px;
		line-height: 36px;
		text-decoration: none;
	}
	#desc {
		font-size: 14px;
		line-height: 23px;
		padding: 0 0 3em;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 1000px;
		height: auto;
		width: 100%;
	}
	</style>
<?php
}
endif; // dss_admin_header_style

if ( ! function_exists( 'dss_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in dss_setup().
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_admin_header_image() { ?>
	<div id="headimg">
		<?php
		$color = get_header_textcolor();
		$image = get_header_image();
		if ( $color && $color != 'blank' )
			$style = ' style="color:#' . $color . '"';
		else
			$style = ' style="display:none"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // dss_admin_header_image

/**
 * Limit the number of characters shown in the excerpt
 * Adds read more link to excerpt
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_limit_excerpt( $excerpt ) {
	$excerpt = preg_replace( " (\[.*?\])", '', $excerpt );
	$excerpt = strip_shortcodes( $excerpt );
	$excerpt = strip_tags( $excerpt );
	$excerpt = substr( $excerpt, 0, DSS_READ_MORE_LENGTH );
	$excerpt = substr( $excerpt, 0, strripos( $excerpt, ' ' ) );
	$excerpt = trim( preg_replace( '/\s+/', ' ', $excerpt ) );
	$excerpt .= ' &hellip; <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'dss' ) . '</a>';

	return $excerpt;
}
add_filter( 'wp_trim_excerpt', 'dss_limit_excerpt' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function dss_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'dss_page_menu_args' );

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_widgets_init() {

	register_widget( 'DSS_Framework_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Top', 'dss' ),
		'id' => 'main-top',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'dss' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'dss' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'dss' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'dss' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'dss' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'dss' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'dss' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'dss_widgets_init' );

if ( ! function_exists( 'dss_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 * 
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'dss' ); ?></h3>
			<ul id="numeric_pagination"><?php
				// Load numeric pagination
				dss_pagination();
			?>
			</ul><!-- #numeric_pagination -->
		</nav><!-- #nav-above -->
	<?php endif;
}
endif; // dss_content_nav

/*
 * Pagination code
 * @since 1.0
 * Code developed from the excellent Genesis theme by StudioPress (http://studiopress.com/)
 * 
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_pagination( $pages = '', $range = 2 ) {

	// Beginning of numeric pagination
	if( !is_singular() ) : // do nothing

	global $wp_query;

	// Stop execution if there\'s only 1 page
	if( $wp_query->max_num_pages <= 1 ) return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged') ) : 1;
	$max = intval( $wp_query->max_num_pages );

	//	add current page to the array
	if ( $paged >= 1 )
		$links[] = $paged;

	//	add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1; $links[] = $paged - 2;
	}
	if ( ($paged + 2) <= $max ) { 
		$links[] = $paged + 2; $links[] = $paged + 1;
	}

	//	Previous Post Link
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( __( '&laquo; Previous', 'dss') ) );

	//	Link to first Page, plus ellipeses, if necessary
	if ( !in_array( 1, $links ) ) {
		if ( $paged == 1 )
			$current = ' class="active"';
		else
			$current = null;
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link(1),
			'1'
		);

		if ( !in_array( 2, $links ) )
			echo '<li>&hellip;</li>';
	}

	//	Link to Current page, plus 2 pages in either direction (if necessary).
	sort( $links );
	foreach( (array)$links as $link ) {
		$current = ( $paged == $link ) ? 'class="active"' : '';
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link( $link ),
			$link
		);
	}

	//	Link to last Page, plus ellipses, if necessary
	if ( !in_array( $max, $links ) ) {
		if ( !in_array( $max - 1, $links ) )
			echo '<li>&hellip;</li>' . "\n";
		
		$current = ( $paged == $max ) ? 'class="active"' : '';
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link( $max ),
			$max
		);
	}

	//	Next Post Link
	if ( get_next_posts_link() )
		printf(
			'<li>%s</li>' . "\n",
			get_next_posts_link( __( 'Next &raquo;', 'dss' ) ) );
	endif;

}

/**
 * Return the URL for the first link found in the post content.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @return string|bool URL or false when no link is present.
 */
function dss_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 * 
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'dss_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own dss_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'dss' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'dss' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'dss' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'dss' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'dss' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'dss' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'dss' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for dss_comment()

if ( ! function_exists( 'dss_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own dss_posted_on to override in a child theme
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_posted_on() {
	printf( __( '<span class="sep">Posted</span><span class="by-author"><span class="sep"> by </span><span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span> &nbsp; <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>', 'dss' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'dss' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * Note: This function is modified from the Twenty Eleven theme
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_body_classes( $classes ) {

	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	if ('stat' == dss_get_theme_option( 'theme_sender' ) ) {
		$classes[] = 'riksvapen';
	}

	return $classes;
}
add_filter( 'body_class', 'dss_body_classes' );

/**
 * Categories to ignore in templates
 * If one of these categories is singly present, then the category list is not displayed
 * Useful for ensuring that categories aren't listed when someone doesn't bother to set a category for the post
 *
 * @since DSS Framework 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_categories_to_ignore() {
	$cats = array(
		'Uncategorised',
		'Uncategorized',
		'Ukategorisert',
	);
	return $cats;
}

/**
 * Adds order class to widgets
 * Useful for targetting individual widgets
 * 
 * Works by modifying the global array containing the sidebar class names
 * Code adapted from http://konstruktors.com/blog/wordpress/3615-add-widget-order-css-class-sidebar/
 * 
 * @since 1.0
 * @global array $wp_registered_sidebars
 * @global array $wp_registered_widgets
 * @author Ryan Hellyer <ryan@pixopoint.com> and Kaspars Dambis <kaspars@metronet.no>
 */
function dss_widget_order_class() {
	global $wp_registered_sidebars, $wp_registered_widgets;

	// Grab the widgets
	$sidebars = wp_get_sidebars_widgets();

	if ( empty( $sidebars ) )
		return;

	// Loop through each widget and change the class names
	foreach ( $sidebars as $sidebar_id => $widgets ) {
		if ( empty( $widgets ) )
			continue;
		$number_of_widgets = count( $widgets );
		foreach ( $widgets as $i => $widget_id ) {
			$wp_registered_widgets[$widget_id]['classname'] .= ' widget-order-' . $i;

			// Add first widget class
			if ( 0 == $i ) {
				$wp_registered_widgets[$widget_id]['classname'] .= ' first-widget'; 
			}

			// Add last widget class
			if ( $number_of_widgets == ( $i + 1 ) ) {
				$wp_registered_widgets[$widget_id]['classname'] .= ' last-widget'; 
			}
		}
	}
}
add_action( 'init', 'dss_widget_order_class' );

/**
 * Disables the admin panel notice in "Disable Search" plugin
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@pixopoint.com>
 */
function dss_disable_search_plugin_notice() {
	add_filter( 'c2c_disable_search_hide_admin_nag', '__return_true' );
}
add_action( 'init', 'dss_disable_search_plugin_notice' );

/**
 * Set whether bio information should be displayed or not
 * Used on singular pages
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 * @return bool If true, then author bio information should be displayed
 */
function dss_show_author_bio() {
	$show_bio = true;
	$show_bio = apply_filters( 'dss_show_author_bio', $show_bio );
	return $show_bio;
}

/**
 * Add thumbnail information to post class
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_add_thumbnail_post_class( $classes ) {
	global $post;
	if ( has_post_thumbnail() ) {
		$classes[] = 'has-thumbnail';
	} else {
		$classes[] = 'no-thumbnail';
	}
	
	return $classes;
}
add_filter( 'post_class', 'dss_add_thumbnail_post_class' );

/*
 * Dequeue comment ratings plugin CSS
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function dss_dequeue_comment_ratings_css() {
	wp_dequeue_style( 'comment_ratings' );
}
add_action( 'wp_enqueue_scripts', 'dss_dequeue_comment_ratings_css' );
