<?php
register_sidebar(array(
    'name' => 'Sidebar',
    'id' => 'sidebar',
    'before_widget' => '<li class="widgetcontainer nhop_activity dss_activity_%s">',
    'after_widget' => '</li>',
    'before_title' => '<h4>',
    'after_title' => '</h4>',
));

function remove_news_category( $query ) {
   # Remove nyheter in the loop on the homepage
   if ( is_home()) {
    $this_cat = get_category_by_slug('nyheter');
    $query->set( 'cat', '-'.$this_cat->term_id);
  }
}
add_action( 'pre_get_posts', 'remove_news_category' );

/* Global */
add_theme_support('post-thumbnails');
include_once('lib/options.php');
include_once('lib/global_functions.php');
include_once('lib/rewrite_rules.php');
include_once('lib/admin.php');
include_once('lib/menus.php');

/* Structure */
include_once('lib/header.php');
include_once('lib/pagenavi.php');
include_once('lib/footer.php');

/* Page types */
include_once('lib/pages/topic-definition.php');
include_once('lib/pages/topicgroup-definition.php');

/* Post types */
include_once('lib/post-types.php');

/* Function specific */
include_once('lib/menu-topics-front.php');
include_once('lib/menu-article-list.php');
include_once('lib/menu-main.php');
include_once('lib/post.php');
include_once('lib/comments.php');
include_once('lib/email_notification.php');
include_once('lib/social_bookmarks.php');

/* Widgets */
include_once('lib/widgets/nhop_post_filter.php');
include_once('lib/widgets/nhop_entry.php');
include_once('lib/widgets/nhop_activity.php');
include_once('lib/widgets/hearing_fb.php');
//include_once('lib/widgets/nhop_latests_posts.php');
//include_once('lib/widgets/nhop_most_commented.php');
//include_once('lib/widgets/nhop_most_active.php');
//include_once('lib/widgets/nhop_selected_posts.php');

?>