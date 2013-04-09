<?php 
/*
Plugin Name: Wordpress Instagrabber 
Plugin URI: http://wordpress.org/extend/plugins/instagrabber/
Description: Import your instagrams photos as a post, display them in a widget or just have acces to them in admin.
Version: 2.3.2
Author: Johan Ahlbäck
Author URI: http://johan-ahlback.com
*/
/**
 * Copyright (c) `2013` Johan Ahlbäck. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 * This plugin uses the Instagram(tm) API and is not endorsed or certified by Instagram or Instagram,
 * Inc. All Instagram(tm) logos and trademarks displayed on this plugin are property of Instagram, Inc.
 */

load_plugin_textdomain( 'instagrabber', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

//install tables

register_activation_hook(__FILE__,'instagrabber_install');

function instagrabber_install() {
   global $wpdb;

   $table_name = $wpdb->prefix . "instagrabber_streams";

   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      
	   $sql = "CREATE TABLE $table_name (
	  id int(11) unsigned NOT NULL AUTO_INCREMENT,
	  name text,
	  type text,
	  userid bigint(20) DEFAULT NULL,
	  tag text,
	  access_token text,
	  auto_post tinyint(1) DEFAULT NULL,
	  save_images tinyint(1) DEFAULT NULL,
	  auto_tags tinyint(1) DEFAULT NULL,
	  post_type text,
	  post_status text,
	  last_id text,
	  image_attachment text,
	  image_link text,
	  customlink_url text,
	  created_by int(11) DEFAULT '1',
	  placeholder_title text NOT NULL,
	  backup_placeholder_title text,
	  allow_hashtags tinyint(1) DEFAULT 0,
	  taxonomy text,
	  tax_term int(11) DEFAULT NULL,
	  local_tags text,
	  tags_tax text,
	  post_format text,
	  image_size text,
	  administrators text,
	  get_to_date DATETIME,
	  error int(11),
	  error_message text,
	  PRIMARY KEY (id)
	);";
		

		$table_name_images = $wpdb->prefix . "instagrabber_images";
	      
	   $sql .= "CREATE TABLE $table_name_images (
		  id bigint(20) NOT NULL AUTO_INCREMENT,
		  pic_id varchar(256) NOT NULL DEFAULT '',
		  pic_url varchar(256) DEFAULT NULL,
		  pic_thumbnail text,
		  pic_link varchar(256) DEFAULT NULL,
		  pic_timestamp datetime DEFAULT NULL,
		  time_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  caption text,
		  tags text,
		  comment_count int(11) DEFAULT NULL,
		  like_count int(11) DEFAULT NULL,
		  published tinyint(1) DEFAULT '0',
		  media_id bigint(20) DEFAULT NULL,
		  stream int(11) DEFAULT NULL,
		  user_name text,
		  user_id text,
		  filter text,
		  location text,
		  PRIMARY KEY id (id)
		);";
	   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	   dbDelta($sql);

	   add_option('instagrabber_db_version', 11);
	   update_option('instagrabber_authtype', 'xauth');
	}
}

// include required classes
require_once('includes/database.class.php');
require_once('includes/instagrabber-api.class.php');
require_once('includes/admin_pages.class.php');
require_once('includes/list_stream.class.php');
require_once('includes/list_streams.class.php');
require_once('includes/utils.php');

// plugin base URLs and prefixes
define('INSTAGRABBER_PLUGIN_CALLBACK_ACTION', 'instagrabber_redirect_uri');

// Instagram base URLs
define('INSTAGRABBER_DEVELOPER_URL', 'http://instagram.com/developer/');

define('INSTAGRABBER_PLUGIN_URL', plugin_dir_url( __FILE__ ));

	Class Instagrabber
	{
		//db version for updating tables
		public $databaseversion = 11;
		
		//load all hooks
		function __construct(){

			//update database if the user has a old version
			add_action('admin_init', array($this, 'check_update'), 1);

			add_filter('set-screen-option', array( $this, 'set_option'), 10, 3);
			
			// Instagrabber auth
			$api = new InstagrabberApi;
			
			//Auth actions
			add_action('wp_ajax_instagrabber_redirect_uri', array($api, 'instagrabber_deal_with_instagram_auth_redirect_uri') );

			//save options
			// TODO: Move to pages class if you save information
			add_action('admin_init', array( $this, 'save_client_info' ));
			add_action('admin_init', array($this, 'save_stream'));
			add_action('admin_init', array($this, 'delete_stream'));

			add_action('admin_init', array($this, 'post_images'), 25);
			add_action('admin_init', array($this, 'post_images_overide_post_status'), 25);
			add_action('admin_init', array($this, 'handle_save_to_library'));

			
			// wp_cron
			add_action('instagrabber_scheduled_post_creation_event', array( $this, 'instagrabber_automatic_post_creation'));

			// Add wp_cron intervals
			add_filter('cron_schedules',array( $this, 'instagrabber_cron_definer'));

			//add scripts
			add_action('admin_enqueue_scripts', array($this, 'my_scripts_method'));

			//import images
			add_action('admin_init', array($this, 'manual_import') );

			//handle uninstall and reset
			add_action('admin_init', array($this, 'plugin_extras') );

			//remove madia_id when image is removed from library
			add_action('delete_attachment', array($this, 'remove_media_id'),10, 1);

			//toolbar menu 
			add_action( 'admin_bar_menu', array($this, 'toolbar'), 999 );

			//dashboard widget
			add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets') );
		}

		/**
		 * Checks if the tables needs to be updated.
		 */

		function check_update(){
			global $wpdb;

			$userversion = get_option('instagrabber_db_version');
			
			if($userversion == $this->databaseversion)
				return;

			if(!$userversion){
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD post_format varchar(11) AFTER post_status;");
				update_option('instagrabber_db_version', 2);
				$userversion = 2;
			}

			if($userversion == 2){
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD backup_placeholder_title text AFTER placeholder_title;");
				update_option('instagrabber_db_version', 3);
				$userversion = 3;
			}
			if($userversion == 3){
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD error int(11) AFTER post_format;");
				$wpdb->query("ALTER TABLE $table_name ADD error_message text AFTER error;");
				update_option('instagrabber_db_version', 4);
				$userversion = 4;
			}

			if ($userversion == 4) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD save_images tinyint(1) AFTER auto_post;");
				update_option('instagrabber_db_version', 5);
				$userversion = 5;
			}

			if ($userversion == 5) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD image_size text AFTER post_format;");
				$wpdb->query("ALTER TABLE $table_name ADD allow_hashtags tinyint(1) AFTER backup_placeholder_title;");
				$wpdb->query("ALTER TABLE $table_name ADD administrators text AFTER image_size;");
				update_option('instagrabber_db_version', 6);
				$userversion = 6;
			}
			
			if ($userversion == 6) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD image_link text AFTER image_attachment;");
				update_option('instagrabber_db_version', 7);
				$userversion = 7;
			}

			if ($userversion == 7) {
				$table_name = $wpdb->prefix . "instagrabber_images";
				$wpdb->query("ALTER TABLE $table_name ADD filter text AFTER user_id;");
				$wpdb->query("ALTER TABLE $table_name ADD location text AFTER filter;");
				update_option('instagrabber_db_version', 8);
				$userversion = 8;
			}

			if ($userversion == 8) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD get_to_date DATETIME AFTER administrators;");
				update_option('instagrabber_db_version', 9);
				$userversion = 9;
			}

			if ($userversion == 9) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD local_tags text AFTER tax_term;");
				update_option('instagrabber_db_version', 10);
				$userversion = 10;
			}

			if ($userversion == 10) {
				$table_name = $wpdb->prefix . "instagrabber_streams";
				$wpdb->query("ALTER TABLE $table_name ADD customlink_url text AFTER image_link;");
				update_option('instagrabber_db_version', 11);
				$userversion = 11;
			}

			return;
		}


		/**
		 * Add scripts and css on admin pages
		 */	
		function my_scripts_method() {
		    wp_deregister_script( 'intagrabber_stream' );
		    wp_register_script( 'intagrabber_stream', INSTAGRABBER_PLUGIN_URL.'js/instagrabber_stream.js', array('jquery'));

		    wp_register_style( 'instagrabbercss', INSTAGRABBER_PLUGIN_URL . 'css/instagrabber.css' );
		    wp_enqueue_style( 'instagrabbercss' );
		    
		}    


		/**
		 * Set page options. In this case how many items per page
		 */
		function set_option($status, $option, $value) {
 
			return $value;
			 
		}

		
		/**
		 * Some plugin extras. reset and unisntall. 
		 * @todo uninstall transients and user inputs.
		 */
		function plugin_extras(){
			if(!current_user_can('manage_options'))
				return;

			if (isset($_REQUEST['remove_locks']) && $_REQUEST['remove_locks'] == 'true') {
				$this->remove_locks();
				wp_redirect(admin_url( 'admin.php' ).'?page=instagram-settings');
				die();
			}

			if (isset($_REQUEST['reset_instagrabber']) && $_REQUEST['reset_instagrabber'] == 'true') {
				$this->reset_auto();
				wp_redirect(admin_url( 'admin.php' ).'?page=instagram-settings');
				die();
			}

			if (isset($_REQUEST['uninstall_instagrabber']) && $_REQUEST['uninstall_instagrabber'] == 'true') {
				$this->reset_auto();
				$this->uninstall_plugin();
				
				deactivate_plugins("instagrabber/instagrabber.php");
				wp_redirect(admin_url( '/' ));
				die();

			}


		}

		/**
		 * Removes locks from transients
		 */

		function remove_locks(){
			$streams = Database::get_streams();

			foreach ($streams as $key => $value) {
				
				delete_transient( 'instablock_'. $value->id);
			}
		}

		/**
		 * Resets wp_cron for instagrabber. 
		 */
		function reset_auto(){
			$this->remove_locks();
			$this->instagrabber_remove_scheduled_event();
			update_option('instagrabber_instagram_app_scheduled', 'never');
			delete_option( 'instagrabber_auto_post_creation' );
		}

		/**
		 * Uninstalls instagrabber.
		 * @todo add all transients and user preferenses
		 */
		function uninstall_plugin(){
			$this->remove_locks();
			$this->reset_auto();

			delete_option('instagrabber_instagram_app_scheduled');
			delete_option('instagrabber_db_version');
			delete_option('instagrabber_authtype');
			delete_option('instagrabber_instagram_app_id');
			delete_option('instagrabber_instagram_app_secret');
			delete_option('instagrabberlove');
			delete_option('instagrabber_allow_save_images');
			delete_option('instagrabber_title_limit');
		
			Database::drop_tables();
		}

		
		/**
		 * Save settings for the plugin.
		 * @todo move to page class
		 */
		function save_client_info(){
			if(!isset($_POST['clientauthinfo']))
				return;

			update_option('instagrabber_authtype', $_POST['authorizationtype']);

			update_option('instagrabber_instagram_app_id', esc_attr($_POST['clientid']));
			update_option('instagrabber_instagram_app_secret', esc_attr($_POST['clientsecret']));

			update_option('instagrabberlove', esc_attr($_POST['instagrabberlove']));
			update_option('instagrabber_allow_save_images', esc_attr($_POST['allowsave_images']));
			update_option('instagrabber_title_limit', esc_attr($_POST['titlelimit']));

			if($_POST['scheduled'] != get_option('instagrabber_instagram_app_scheduled')){
				$this->instagrabber_remove_scheduled_event();
				
				if($_POST['scheduled'] != 'never')
					$this->instagrabber_schedule_event($_POST['scheduled']);

				update_option('instagrabber_instagram_app_scheduled', $_POST['scheduled']);
			}
			wp_redirect(admin_url( 'admin.php' ).'?page=instagram-settings');
			die();
		}

		/**
		 * Saves a stream.
		 * @todo Move this function
		 */
		function save_stream(){
			if(!isset($_POST['instagram-add-streams']))
				return;
			

			$admins = !isset($_REQUEST['instagrabber_admins']) ? array(-1) : $_REQUEST['instagrabber_admins'];
			
			$autopost = esc_attr($_POST['createpost']) == 'true' ? 1 : 0;
			$autotags = esc_attr($_POST['autotag']) == 'true' ? 1 : 0;
			$allow_hashtags = esc_attr($_POST['allow_hashtags']) == 'true' ? 1 : 0;

			$save_images = isset($_POST['saveimages']) && $_POST['saveimages'] == 'true' ? 1 : 0;
			$image_size = $_REQUEST['image_size_custom'] == "0" || $_REQUEST['image_size_custom'] == "" ?  $_REQUEST['image_size'] : $_REQUEST['image_size_custom'];
			$image_size = empty($image_size) ? 'full' : $image_size;
			$taxonomy = esc_attr($_POST['taxonomy']);
			$term = $taxonomy == 'none' ? 'none' : esc_attr($_POST['terms']);
			$tag_tax = esc_attr($_POST['taxonomy_tag']);
			
			$args = array(
				'name'                     => esc_attr($_POST['name']),
				'type'                     => esc_attr($_POST['type']),
				'tag'                      => esc_attr($_POST['tag']),
				'auto_post'                => $autopost,
				'save_images'              => $save_images,
				'auto_tags'                => $autotags,
				'post_type'                => esc_attr($_POST['post_type']),
				'post_status'              => esc_attr($_POST['status']),
				'placeholder_title'        => esc_attr($_POST['placeholder']),
				'backup_placeholder_title' => esc_attr($_POST['backup_placeholder']),
				'allow_hashtags'           => $allow_hashtags,
				'created_by'               => esc_attr($_POST['instagrabber_user']),
				'image_attachment'         => esc_attr($_POST['image_attachment']),
				'image_link'               => esc_attr($_POST['image_link']),
				'customlink_url'           => esc_attr($_POST['customlink_url']),
				'taxonomy'                 => $taxonomy,
				'tax_term'                 => $term,
				'tags_tax'                 => $tag_tax,
				'local_tags'               => $_POST['local_tags'],
				'post_format'              => esc_attr($_POST['format']),
				'image_size'               => esc_attr($image_size),
				'administrators'           => $admins,
				'get_to_date'				=> esc_attr($_POST['getfromdate'])
			);

			
			if(isset($_POST['instagram-update-stream'])){
				$stream = Database::get_stream($_POST['instagram-update-stream']);
				$args['access_token'] = Database::get_access_token($_POST['instagram-update-stream']);
				
				if ($stream->type != $args['type'] || $stream->tag != $args['tag']) {
					$args['access_token'] = "";
				}
				
				Database::update_stream($args, $_POST['instagram-update-stream']);
				$id = $_POST['instagram-update-stream'];
			}else{

				if($args['type'] == 'user')
					$args['access_token'] = '';
				
				$id = Database::insert_stream($args);
			}
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber&stream='.$id);
			die();
		}


		function delete_stream(){
			if (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'instagrabber-delete-stream')
				return false;

			Database::delete_stream($_REQUEST['streamid']);
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber');
			die();
		}
		

		// task scheduling - custom time periods
		function instagrabber_cron_definer($schedules)
		{
			// 5 minutes
			$schedules['instagrabber_fiveminutes'] = array(
				'interval'=> 300,
				'display'=> __('Once Every 5 Minutes', 'instagrabber')
		  	);

			// 10 minutes
			$schedules['instagrabber_tenminutes'] = array(
				'interval'=> 600,
				'display'=> __('Once Every 10 Minutes', 'instagrabber')
		  	);

		  	// 20 minutes
			$schedules['instagrabber_twentynminutes'] = array(
				'interval'=> 1200,
				'display'=> __('Once Every 20 Minutes', 'instagrabber')
		  	);

			// 30 minutes
			$schedules['instagrabber_twicehourly'] = array(
				'interval'=> 1800,
				'display'=> __('Once Every 30 Minutes', 'instagrabber')
		  	);

		  	// 'hourly', 'twicedaily', 'daily' already defined in WordPress

			// weekly
			$schedules['instagrabber_weekly'] = array(
				'interval'=> 604800,
				'display'=> __('Once Every 7 Days', 'instagrabber')
		  	);

			// monthly
			$schedules['instagrabber_monthly'] = array(
				'interval'=> 2592000,
				'display'=> __('Once Every 30 Days', 'instagrabber')
		  	);	

			return $schedules;
		}



		function instagrabber_schedule_event($period)
		{
			if ($period == 'instagrabber_fiveminutes' ||
				$period == 'instagrabber_tenminutes' ||
				$period == 'instagrabber_twentynminutes' ||
				$period == 'instagrabber_twicehourly' ||
				$period == 'hourly' ||
				$period == 'twicedaily' ||
				$period == 'daily' ||
				$period == 'instagrabber_weekly' ||
				$period == 'instagrabber_monthly')

				wp_schedule_event(current_time('timestamp'), $period, 'instagrabber_scheduled_post_creation_event' );
		}
		function instagrabber_remove_scheduled_event()
		{
			wp_clear_scheduled_hook( 'instagrabber_scheduled_post_creation_event' );
		}


		function manual_import(){
			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'updatestream')
				return;

			$streams = $_REQUEST['streamid'];
		
			if(!is_array($streams))
				$streams = array($streams);

			foreach ($streams as $key => $stream) {
				
				$this->import_images($stream);
			}
		}

		function import_images($stream){
			
			if(!isset($stream->id))
				$stream = Database::get_stream($stream);
			
			$trany = get_transient( 'instablock_'.$stream->id );
			if ( $trany != "blocked" ){
			   // we have a trany return/assign the results
				$trany = 'blocked';
			   set_transient('instablock_'.$stream->id , $trany, 300 );
			}else{
				//the tranny is not empty and thus this function will not run.
				return false;
			}

			
			if ($stream->type == 'user') {
				$data = InstagrabberApi::instagrabber_getInstagramUserStream($stream);
			}elseif($stream->type == 'tag'){
				$data = InstagrabberApi::instagrabber_TagStream($stream);
			}elseif($stream->type == 'like'){
				$data = InstagrabberApi::instagrabber_getInstagramUserLikeStream($stream);
			}else{
				delete_transient('instablock_'.$stream->id);
				return false;
			}

			

			if(isset($data->meta->error_type)){
				$stream_array = (array) $stream;
				$stream_array['error'] = 1;
				$stream_array['error_message'] = $data->meta->error_message;
				Database::update_stream($stream_array, $stream->id);
				delete_transient('instablock_'.$stream->id);
				return;
			}


			if ($data) {
				Database::save_images_in_database($stream, $data->data);

			}else{
				delete_transient('instablock_'.$stream->id);
				return false;
			}

			if($stream->save_images == 1 && $stream->auto_post != 1){
				$images = Database::get_not_saved_images($stream->id);
				foreach ($images as $key => $image) {
					$this->save_image_to_media_library($image);
				}
			}

			if($stream->auto_post == 1){
				
				$images = Database::get_unpublished_images($stream->id);
				if(!empty($images)){
					$this->create_post($images);
				}
			}

			delete_transient('instablock_'.$stream->id);

		}

		function post_images_overide_post_status(){

			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'post-instagrammer-override')
				return;
			$image = $_REQUEST['image'];
			
			if (!is_array($image))
				$image = array($image);
			
			$this->create_post($image, true);
			
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber&stream='.$_REQUEST['stream']);
			die();
		}

		function post_images(){

			if(!isset($_REQUEST['action']) || $_REQUEST['action'] != 'post-instagrammer')
				return;
			$image = $_REQUEST['image'];
			
			if (!is_array($image))
			$image = array($image);

			$this->create_post($image);
			
			wp_redirect(admin_url( 'admin.php' ).'?page=instagrabber&stream='.$_REQUEST['stream']);
			die();
		}

		function create_post($images, $override = false){
			global $current_user;
			
			

			if(!isset($images[0]->pic_id))
				$images = Database::get_images_by_id($images);

			$stream = '';
			$last_stream = NULL;
			
			foreach ($images as $key => $img) {
				
				
				if ($img->stream != $last_stream) {
					
					$stream = Database::get_stream($img->stream);
					$last_stream = $img->stream;
					
				}

			$title_placeholder = $stream->placeholder_title;

			$category_for_post = $stream->tax_term;

			if (empty($category_for_post))
			{
					
					$category_for_post = 'none';
			}

			if ($override) {
				if ($stream->post_status == 'draft') {
					$created_post_status = 'published';
				}else{
					$created_post_status = 'draft';
				}	
			}else{
				$created_post_status = $stream->post_status;	
			}
			
			if ($created_post_status != 'published')
				$created_post_status = 'draft';
			

			$insert_photo_mode = $stream->image_attachment;
			
			$placeholders = Utils::get_placeholders($img, $stream);
			$placeholder_keys = array_keys($placeholders);
			$placeholder_values = array_values($placeholders);
			$title_placeholder = str_replace(
									$placeholder_keys,
									$placeholder_values,
									$title_placeholder);

			$backup_title_placeholder = str_replace(
									$placeholder_keys,
									$placeholder_values,
									$stream->backup_placeholder_title);

			if ($title_placeholder == '' && $backup_title_placeholder != '') {
				$title_placeholder = $backup_title_placeholder;
			}elseif($title_placeholder == '' && $backup_title_placeholder == ''){
				$title_placeholder = $stream->name;
			}

			$new_post_author = $stream->created_by;
			if ($override) {
				$current_user = wp_get_current_user();
				$new_post_author = $current_user->ID;
			}
			$post_args = array(
				'post_author' 	=> $new_post_author,		// with 0, current post author id is used
				//'post_category'	=> array($cat_id),
				'post_content' 	=> $img->caption,
				'post_status'	=> 'draft', 
				'post_title'	=> $title_placeholder,
				'post_type'		=> $stream->post_type 
			);

			
			
			// INSERT checks about correct format...
			
			$created_post_ID = wp_insert_post($post_args);
			
			//for those who updates the plugin. post formats was added in plugin version 1.4
			$post_format = $stream->post_format == '' ? 'standard' : $stream->post_format;

			//set post format for post
			set_post_format( $created_post_ID , $stream->post_format);
			
			if($stream->taxonomy != 'none' || $stream->tax_term != 'none')
				wp_set_post_terms($created_post_ID, array($stream->tax_term), $stream->taxonomy, false);
			
			// add comma separated tags to post, if specified
			$tag_to_add_to_post = $stream->auto_tags;
			

			$tags = $img->tags != "" && !empty($img->tags) ? unserialize($img->tags) : "";
			
			$tag_string = '';

			if ((!empty($tags) || $tags == false) && $tag_to_add_to_post != false) {
				$tag_string .= implode(', ', $tags);
				$tag_string .= ', ' . $stream->local_tags;
				
			}

			if (($tag_to_add_to_post == false || empty($tags)) && $stream->local_tags != '') {
				
				$tag_string .= $stream->local_tags;
			}


			if (!empty($tag_string) || $tag_string != ''){
				wp_set_post_terms($created_post_ID, $tag_string, $stream->tags_tax, true);
			}


			// c. add Instagram pic metadata to the just created post
			update_post_meta($created_post_ID, '_instagrabber_image_id', $img->id);
			update_post_meta($created_post_ID, '_instagrabber_insta_id', $img->pic_id);
			update_post_meta($created_post_ID, '_instagrabber_insta_link', $img->pic_link);
			
			if(isset($img->user_name))
				update_post_meta($created_post_ID, '_instagrabber_insta_authorusername', $img->user_name);

			if(isset($img->user_id))
				update_post_meta($created_post_ID, '_instagrabber_insta_authorid', $img->user_id);	
			
			
			// d. download image from Instagram and associate to po

			// if we the image is already inside the media library, we get it from there, without actually downloading it from Instagram
			$image_info = null;
			$image_try_int = intval($stream->image_size);

			if ($image_try_int != 0 && $stream->image_size != null) {
				$defaultsize = array($image_try_int, $image_try_int);
			}elseif ($stream->image_size != null) {
				$defaultsize = $stream->image_size;
			}else{
				$defaultsize = 'full';
			}

			
			$imagesize = apply_filters('instagrabber_content_image_size', $defaultsize, $img, $stream);
			
			if ($img && $img->media_id)
			{
				$image_info = wp_get_attachment_image_src($img->media_id, $imagesize);
			}

			if (!$image_info)
			{

				$attach_id = $this->save_image_to_media_library($img, $created_post_ID);



				if (is_wp_error($attach_id)) {
					continue;
				}
			}
			else
				$attach_id = $img->media_id;


			// update Instagram photo local data (better doing it as soon as we are sure we have the image, so
			// if next scheduled event occurs and the same image is present, it is less likely to be added again)
			Database::update_image_to_published($img->id, $attach_id);

			
			if ($insert_photo_mode === 'featured' || $insert_photo_mode === 'both')
			{
				// attach to image as featured image (post thumbnail)
				add_post_meta($created_post_ID, '_thumbnail_id', $attach_id, true);
			}
			if ($insert_photo_mode === 'content' || $insert_photo_mode === 'both'){
				
				if (!$image_info)
					$image_info = wp_get_attachment_image_src($attach_id, $imagesize);

				$fullImage = wp_get_attachment_image_src($attach_id, 'full');

				
				// insert the image inside the post, followed by post caption
				$update_post_data = array();
		  		$update_post_data['ID'] = $created_post_ID;
		  		$image_link = '';

		  		if ($stream->image_link == 'instagram') {
		  			$image_link = $img->pic_link;
		  		}elseif ($stream->image_link == 'post') {
		  			$image_link = get_permalink( $created_post_ID );
		  		}elseif($stream->image_link == 'image'){
		  			$image_link = $fullImage[0];
		  		}elseif($stream->image_link == 'user'){
		  			$image_link = 'http://instagram.com/'. $img->user_name;
		  		}elseif($stream->image_link == 'customlink'){
		  			$image_link = $stream->customlink_url;
		  		}

		  		$post_html = '';
		  		
		  		if($image_link != '')
		  			$post_html .= '<a href="'.$image_link.'">';
		  		
		  		$post_html .= '<img src="'.$image_info[0].'" alt="'.esc_attr(strip_tags($img->caption)).'" width="'.$image_info[1].'" height="'.$image_info[2].'"/>';
		  		
				if($image_link != '')
		  			$post_html .= '</a>';
		  		$post_html .= '<br/>';
		  		$post_html .= $img->caption;
		  	
		  		$update_post_data['post_content'] = $post_html;

		  		wp_update_post($update_post_data);
			}

			// the post is always created as draft and, if after post creation the image could actually be added and settings say the
			// post must be directly published, it is moved from 'draft' to 'published'
			if ($created_post_status == 'published')
			{
				$update_post_data = array();
		  		$update_post_data['ID'] = $created_post_ID;
		  		$update_post_data['post_status'] = 'publish';
		  		wp_update_post($update_post_data);
			}

				
			}
			
		}

		function save_image_to_media_library($img, $created_post_ID = 0){
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
			$tmp = download_url($img->pic_url);
			if (is_wp_error($tmp))
			{
				if($created_post_ID != 0){
					wp_delete_post($created_post_ID, true);
				}
		    	Database::delete_image($img->id);
				return new WP_Error('Broken', __("That fail'd"));
		    }

		    $file_array = array(
		        'name' => basename($img->pic_url),
		        'tmp_name' => $tmp
		    );


		    $attach_id = media_handle_sideload($file_array, $created_post_ID);

		    if (is_wp_error($attach_id))
			{
				if($created_post_ID != 0){
					@unlink($file_array['tmp_name']);
					wp_delete_post($created_post_ID, true);
				}
				return new WP_Error('Broken', __("That fail'd"));
		    }
			
			if($created_post_ID == 0){
				Database::update_media_id($img->id, $attach_id);
			}
			@unlink($file_array['tmp_name']);
			return $attach_id;
		}

		function handle_save_to_library(){
			if (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'instagrabber_save_image') {
				return;
			}

			$images = $_REQUEST['image'];

			if(!is_array($images))
				$images = array($images);

			foreach ($images as $key => $image) {
				if(isset($image->media_id) && $image->media_id != 0)
					continue;

				if(!isset($image->id))
					$image = Database::get_images_by_id($image);

				$this->save_image_to_media_library($image);
			}
		}

		function remove_media_id($media_id){
			Database::remove_media_id($media_id);
		}

		function instagrabber_automatic_post_creation(){
			//check if this function is already running

			$trany = get_transient( 'instagrabber_auto_post_creation' );
			if ( empty( $trany ) ){
			   // we have a trany return/assign the results
				$trany = 'instagrabber_auto_post_creation';
				set_transient('instagrabber_auto_post_creation', $trany, 200 );
			}else{
				return false;
			}

			//get streams
			$streams = Database::get_streams();
			
			foreach ( $streams as $key => $stream ) {
				
				$this->import_images( $stream );
			}


			//Mark as done
			delete_transient('instagrabber_auto_post_creation');
			
		}

		function toolbar($wp_admin_bar){
			global $wp_admin_bar;
			$group = array(
			  'id' => 'instagrabber',
			  'title' => 'Instagrabber',
			  'meta' => array('class' => '')
			);
			
			$trany = get_transient( 'instagrabber_auto_post_creation' );
			if(!empty($trany))
				$group['meta']['class'] = 'instagrabber-getting-images';

			$wp_admin_bar->add_node($group);

			$streams = array(
			  'id' => 'instagrabber-streams',
			  'title' => 'Streams',
			  'parent' => 'instagrabber',
			  'href' => admin_url( 'admin.php' ).'?page=instagrabber'
			);



			$wp_admin_bar->add_node($streams);

			$newstream = array(
			  'id' => 'instagrabber-new',
			  'title' => 'New stream',
			  'parent' => 'instagrabber',
			  'href' => admin_url( 'admin.php' ).'?page=instagram-add'
			);

			$wp_admin_bar->add_node($newstream);
		}

		function add_dashboard_widgets(){
			wp_add_dashboard_widget('instagrabber_streams', 'Instagrabber '.__('Streams', 'instagrabber'), array($this, 'instagrabber_stream_widget') );
		}

		function instagrabber_stream_widget(){
				$streams = $this->stream_widget_function();
			?>
			
			<div>
				<table>
					<thead>
						<th><?php _e('Stream', 'instagrabber') ?></th>
						<th><?php _e('Images', 'instagrabber') ?></th>
						<th><?php _e('Published', 'instagrabber') ?></th>
						
					</thead>
					<tbody>
						<?php foreach ($streams as $key => $stream): ?>
							<tr>
								<td>
									<a href="<?php echo admin_url('admin.php?page=instagrabber&stream=' . $stream->id) ?>"><?php echo $stream->name ?></a>
								</td>
								<td>
									<?php echo $stream->image_count ?>
								</td>
								<td>
									<?php echo $stream->published_images_count ?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
			<?php
		}

		function stream_widget_function(){
			$streams = Database::get_streams(array(), true);

			foreach ($streams as $key => $stream) {
				$stream->image_count = Database::count_images($stream);
				$stream->published_images_count = Database::count_published_images($stream);
				//$stream->new_images = Database::count_new_images($stream);
			}

			return $streams;
		}
		
	}// class Instagrabber

	$instagrabber = new Instagrabber();



	require_once("includes/widgets/published_images.php");
		function instagrabber_register_widgets() {
		register_widget( 'Instagrabber_Published_Images' );
	}

	add_action( 'widgets_init', 'instagrabber_register_widgets' );

	require_once("includes/functions.php");
 ?>