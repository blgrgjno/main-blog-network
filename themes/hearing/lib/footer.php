<?php

	// Remove default Thematic action
	function remove_thematic_action() {
		remove_action('thematic_footer', 'thematic_siteinfo', 30);
	}
	add_action('init','remove_thematic_action');

    function hod_siteinfo() {
        global $options;
        foreach ($options as $value) {
            if (get_option( $value['id'] ) === FALSE) { 
                $$value['id'] = $value['std'];
            } else { 
                $$value['id'] = get_option( $value['id'] );
            }
        }
?>
		<div class="footer_menu">
<?php
			// Render footer menu
			wp_nav_menu(array(
				'theme_location' => 'footer-menu',
				'container_class' => 'menu',
				'menu_class' => '',
				'fallback_cb' => '',
				'location' => 'footer',
				'walker' => new Walker_Main_Menu
			));
?>
			<div class="rss_links">
				<a href="<?php bloginfo('rss2_url'); ?>" class="rss_link" title="RSS-strøm for <?php echo strtolower(get_theme_option('entity_plural')); ?>"><?php echo theme_option('entity_plural'); ?></a>
				<a href="<?php bloginfo('comments_rss2_url'); ?>" class="rss_link" title="RSS-strøm for kommentarer">Kommentarer</a>
			</div>
		</div>
		<div class="info">
			<div class="exit_logo_bottom">
				<a href="<?php theme_option('exit_url'); ?>" target="_blank"><img src="<?php bloginfo('stylesheet_directory'); ?>/img/logo_exit_bottom.png" alt="<?php theme_option('exit_title'); ?>" title="Gå til <?php theme_option('exit_title'); ?>" /></a>
			</div>
<?php
        /* footer text set in theme options */
		echo do_shortcode(__(stripslashes(thematic_footertext($thm_footertext)), 'thematic'));
?>
		</div>
<?php
	}
    add_action('thematic_footer', 'hod_siteinfo', 30);
?>