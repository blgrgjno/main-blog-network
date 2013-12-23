<?php
/*
Plugin Name: Toggle The Title
Plugin URI: http://wordpress.org/extend/plugins/toggle-the-title/
Version: 1.2
Description: A plugin that will allow to remove page titles per page.
Author: Avner Komarow 
Author URI: mailto:avner.komarow@gmail.com
*/
global $wp_version;

if ( !version_compare($wp_version,"3.0",">=") ) {
    die("You need at least version 3.0 of WordPress to use the Toggle The Title plugin.");	
}

 if ( ! defined( 'TOGGLE_THE_TITLE_PLUGIN_URL' ) )
 	define( 'TOGGLE_THE_TITLE_PLUGIN_URL', WP_PLUGIN_URL . '/toggle-the-title' );
 if ( ! defined( 'TITLETOGGLER_DIRECT_PATH' ) )
 	define( 'TITLETOGGLER_DIRECT_PATH',  plugin_basename(__FILE__));

register_activation_hook(__FILE__, "init_TitleToggler"); //on plugin activation
function init_TitleToggler() {
	if(!get_option('TitleToggler_autoSave')) add_option('TitleToggler_autoSave','');
	TitleToggler_run_on_every_wp_admin_eteration();
}

register_deactivation_hook(__FILE__, "shutdown_TitleToggler"); //on plugin deactivation
function shutdown_TitleToggler() {}

	
if (is_admin()) TitleToggler_run_on_every_wp_admin_eteration();
else TitleToggler_run_on_every_wp_view_eteration();

function TitleToggler_run_on_every_wp_admin_eteration() {
	add_action('admin_menu','TitleToggler_register_title_toogle_sub_menu_page');
	
	add_filter("plugin_action_links_".TITLETOGGLER_DIRECT_PATH, 'TitleToggler_settings_link' );
	add_action( 'add_meta_boxes', 'TitleToggler_custom_field_checkbox' );
	add_action( 'save_post', 'TitleToggler_save_title_status_input');
	add_filter('admin_head', 'TitleToggler_add_jquery_script');
	TitleToggler_Add_Custom_css();
	add_action('wp_ajax_update_title_options','TitleToggler_update_wp_options'); //save the checked statuos (autosave)
	
}

function TitleToggler_run_on_every_wp_view_eteration() {
	add_filter('the_title', 'TitleToggler_hide_title', 10, 2);
}

/* Custom CSS styles */
function TitleToggler_Add_Custom_css() {
    if(isset( $_GET['page'] ) && $_GET['page'] == 'TitleToggle')
		wp_enqueue_style( 'TitleToggler-css',  TOGGLE_THE_TITLE_PLUGIN_URL . '/css/TitleToggler_style.css', array(), '1.0' );
}

/* Custom js*/
function TitleToggler_add_jquery_script() {
    global $parent_file;

	$is_parent_file = preg_match("/edit.php/", $parent_file);
    if(isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['post'] ) && $is_parent_file) {
		$optiontest=get_option('TitleToggler_autoSave');
		if (empty($optiontest)) return;
		$src = TOGGLE_THE_TITLE_PLUGIN_URL . '/js/admin_edit.js';
	    print '<script type="text/javascript" src="' . $src . '"></script>';
    }
    if( isset( $_GET['page'] ) && $_GET['page'] == 'TitleToggle') {
		$src = TOGGLE_THE_TITLE_PLUGIN_URL . '/js/admin_setting.js';
	    print '<script type="text/javascript" src="' . $src . '"></script>';
    }
	
}

// register the meta box
function TitleToggler_custom_field_checkbox() {
	global $post;

    add_meta_box('toggle_page_title_meta_box_id', 'Title Toggler',  'TitleToggler_customfield_box_content', 'page', 'side',  'default');
}

// display the metabox
function TitleToggler_customfield_box_content($post_id) {
	$is_page_title_active = get_post_meta(get_the_ID(), $key = 'toggle_page_title', $single = true);
	$checked = ' checked="checked"';
	if($is_page_title_active != '' && !$is_page_title_active) $checked = '';
    echo '<label><input id="hook_toggle_page_title" type="checkbox" name="toggle_page_title" value="1" ' . $checked . ' /> Show page title?</label><br />';
}

// save data from checkboxes
function TitleToggler_save_title_status_input() {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return; 

    if(isset($_POST['toggle_page_title'])) update_post_meta(get_the_ID(), 'toggle_page_title', 1 );
    else update_post_meta(get_the_ID(), 'toggle_page_title', 0 );
}

function TitleToggler_hide_title($title, $id) {
	if (!is_page() || get_post_type( $id ) <> 'page') return $title;
	
	$is_page_title_active = get_post_meta($id, $key = 'toggle_page_title', $single = true);
	if($is_page_title_active == '' || $is_page_title_active && in_the_loop()) return $title;
	if (!in_the_loop()) return $title;
	return $title='';
}

function TitleToggler_register_title_toogle_sub_menu_page() {
	add_submenu_page('options-general.php', 'Title Toggle Setting ', 'Title Toggle',  'edit_posts', 'TitleToggle', 'title_toggle_page_function');
}

function title_toggle_page_function() {
	if(!current_user_can('publish_posts') || !current_user_can('edit_posts'))   
		wp_die('You do not have sufficient permissions to access this page.');

	print '
		<div class="wrap">' 
			. screen_icon('options-general') . '
			<h2>Title Toggler Settings</h2>' 
			. title_TitleToggler_inner_custom_box() . '
		</div>';
}



function title_TitleToggler_inner_custom_box() {
	$output = '	
	<div>
		<label>
			<br>
			<input id="hook_toggle_btn_title_autosave" type="checkbox" name="hook_toggle_btn_title_autosave" value="1" '.get_option('TitleToggler_autoSave').'/>'.__('Autosave on change title status?', 'title_toogler').'
		</label>
			<br><br><input type="submit" id="toggle_title_submit" class="button-primary" value="Save Changes" />
	<div>';

	return $output;
}

function TitleToggler_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=TitleToggle">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function TitleToggler_update_wp_options() {
	TitleToggler_set_headers();
	update_option('TitleToggler_autoSave', $_POST[checked]);
	die;
}

function TitleToggler_set_headers() {
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
}

?>