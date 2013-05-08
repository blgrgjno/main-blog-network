<?php

/* Add our own scripts */

wp_enqueue_script('tiny_mce', get_option('siteurl') . '/wp-includes/js/tinymce/tiny_mce.js', false, '20081129');
wp_register_script('hodjs', dirname( get_bloginfo('stylesheet_url') )."/js/general.js", array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'));
wp_enqueue_script('hodjs');

/* Remove the blog description from the header */
function remove_thematic_actions() {
	remove_action('thematic_header','thematic_blogdescription',5);
	remove_action('thematic_header','thematic_blogtitle',3);
}
add_action('init','remove_thematic_actions');

// Create the blog title in the header div
function nhop_blogtitle() { ?>
			<div id="blog-title"><span><a href="<?php bloginfo('url') ?>/" title="Gå til forsiden" rel="home"><?php bloginfo('name') ?></a></span></div>
<?php }
add_action('thematic_header','nhop_blogtitle',3);

/* MAIN MENU */

// This theme uses wp_nav_menu()
add_theme_support( 'nav-menus' );

// Remove standard menu
function remove_menu() {
    remove_action('thematic_header','thematic_access',9);
}
add_action('init', 'remove_menu');

// Create wp_nav_menu
function new_access() {
?>
    <div id="access">
        <div class="skip-link"><a href="#content" title="<?php _e('Skip navigation to the content', 'thematic'); ?>"><?php _e('Skip to content', 'thematic'); ?></a></div>
        <?php
			// Render main menu
			wp_nav_menu(array(
				'menu' => get_theme_option('mainmenu_slug'),
				'container_class' => 'menu',
				'menu_class' => '',
				'fallback_cb' => '',
				'location' => 'header',
				'walker' => new Walker_Main_Menu
			));
			
			// Render seach form
			get_search_form();
	 ?>
		<ul id="user-menu">
<?php
	// Include login links
	global $user_ID, $user_identity;
	if ( $user_ID ) {
		echo "<li>";
		printf(__('<span class="loggedin">Logged in as <a href="%1$s" title="Logged in as %2$s">%2$s</a>.</span> <span class="logout"><a href="%3$s" title="Log out of this account">Log out?</a></span>', 'thematic'),
			get_option('siteurl') . '/wp-admin/profile.php',
			wp_specialchars($user_identity, true),
			wp_logout_url(get_permalink()) );
		echo "</li>";
	}
	else if (get_theme_option('enable_statements')) {
?>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
<?php
	}
?>
		</ul>
    </div>
<?php
}
add_action('thematic_header','new_access',9);

function wp_nav_menu_add_menuclass($content) {
	if (strrpos($content, get_theme_option('mainmenu_slug')) !== false)
		return preg_replace('/<ul/', '<ul class="sf-menu"', $content, 1);
	else
		return $content;
}
add_filter('wp_nav_menu','wp_nav_menu_add_menuclass');

?>