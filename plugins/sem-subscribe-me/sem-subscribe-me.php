<?php
/*
Plugin Name: Subscribe Me
Plugin URI: http://www.semiologic.com/software/subscribe-me/
Description: Widgets that let you display subscribe links to RSS readers such as Google Reader.
Version: 5.2
Author: Denis de Bernardy & Mike Koepke
Author URI: http://www.getsemiologic.com
Text Domain: sem-subscribe-me
Domain Path: /lang
*/

/*
Terms of use
------------

This software is copyright Mesoconcepts (http://www.mesoconcepts.com), and is distributed under the terms of the GPL license, v.2.

http://www.opensource.org/licenses/gpl-2.0.php

Fam Fam Fam silk icon (feed_add and information) are copyright Mark James (http://www.famfamfam.com/lab/icons/silk/), and CC-By licensed:

http://creativecommons.org/licenses/by/2.5/

Large feed icons are copyright feedicons.com.

Other icons are copyright their respective holders.
**/


load_plugin_textdomain('sem-subscribe-me', false, dirname(plugin_basename(__FILE__)) . '/lang');


/**
 * subscribe_me
 *
 * @property int|string alt_option_name
 * @package Subscribe Me
 **/

class subscribe_me extends WP_Widget {
    /**
   	 * subscribe_me()
   	 *
   	 * @return void
   	 **/

   	function subscribe_me() {
        add_action('widgets_init', array($this, 'widgets_init'));

        if ( !is_admin() ) {
        	add_action('wp_print_scripts', array($this, 'scripts'));
        	add_action('wp_print_styles', array($this, 'styles'));
        }

        foreach ( array(
        		'switch_theme',
        		'update_option_active_plugins',
        		'update_option_sidebars_widgets',
        		'generate_rewrite_rules',

        		'flush_cache',
        		'after_db_upgrade',
        		) as $hook ) {
        	add_action($hook, array($this, 'flush_cache'));
        }

        register_activation_hook(__FILE__, array($this, 'flush_cache'));
        register_deactivation_hook(__FILE__, array($this, 'flush_cache'));

   		$widget_ops = array(
   			'classname' => 'subscribe_me',
   			'description' => __('Subscribe links to RSS readers such as Google Reader', 'sem-subscribe-me'),
   			);
   		$control_ops = array(
   			'width' => 330,
   			);

   		$this->init();
   		$this->WP_Widget('subscribe_me', __('Subscribe Me', 'sem-subscribe-me'), $widget_ops, $control_ops);
   	} # subscribe_me()

	/**
	 * init()
	 *
	 * @return void
	 **/

	function init() {
		if ( get_option('widget_subscribe_me') === false ) {
			foreach ( array(
				'subscribe_me' => 'upgrade',
				'subscribe_me_widgets' => 'upgrade',
				'sem_subscribe_me_params' => 'upgrade_2_x',
				) as $ops => $method ) {
				if ( get_option($ops) !== false ) {
					$this->alt_option_name = $ops;
					add_filter('option_' . $ops, array(get_class($this), $method));
					break;
				}
			}
		}
	} # init()
	
	
	/**
	 * scripts()
	 *
	 * @return void
	 **/

	function scripts() {
		$folder = plugin_dir_url(__FILE__);
		wp_enqueue_script('subscribe_me', $folder . 'js/scripts.js', array('jquery'), '20090906', true);
	} # scripts()
	
	
	/**
	 * styles()
	 *
	 * @return void
	 **/

	function styles() {
		$folder = plugin_dir_url(__FILE__);
		wp_enqueue_style('subscribe_me', $folder . 'css/styles.css', null, '20090903');
	} # styles()
	
	
	/**
	 * widgets_init()
	 *
	 * @return void
	 **/

	function widgets_init() {
		register_widget('subscribe_me');
	} # widgets_init()
	

	
	/**
	 * widget()
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( is_feed() || isset($_GET['action']) && $_GET['action'] == 'print' )
			return;
		
		extract($args, EXTR_SKIP);
		$instance = wp_parse_args($instance, subscribe_me::defaults());
		extract($instance, EXTR_SKIP);
		
		if ( is_admin() ) {
			echo $before_widget
				. ( $title
					? ( $before_title . $title . $after_title )
					: ''
					)
				. $after_widget;
			return;
		}
		
		if ( $o = wp_cache_get($widget_id, 'widget') ) {
			echo $o;
			return;
		}
		
		# check if the widget has a class
		if ( strpos($before_widget, 'subscribe_me') === false ) {
			if ( preg_match("/^(<[^>]+>)/", $before_widget, $tag) ) {
				if ( preg_match("/\bclass\s*=\s*(\"|')(.*?)\\1/", $tag[0], $class) ) {
					$tag[1] = str_replace($class[2], $class[2] . ' subscribe_me', $tag[1]);
				} else {
					$tag[1] = str_replace('>', ' class="subscribe_me">', $tag[1]);
				}
				$before_widget = preg_replace("/^$tag[0]/", $tag[1], $before_widget);
			} else {
				$before_widget = '<div class="subscribe_me">' . $before_widget;
				$after_widget = $after_widget . '</div>' . "\n";
			}
		}
		
		$site_url = user_trailingslashit(get_option('home'));
		$feed_url = user_trailingslashit(apply_filters('bloginfo', get_feed_link('rss2'), 'rss2_url'));
		$icons_url = plugin_dir_url(__FILE__) . 'icons';
		
		$title = apply_filters('widget_title', $title);
		
		ob_start();
		
		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="subscribe_me_services">' . "\n";
		
		echo '<div class="' . ( $text ? 'float' : 'center' ) . '_feed_button">'
			. '<a href="' . esc_url($feed_url) . '"'
				. ' title="' . esc_attr(__('RSS Feed', 'sem-subscribe-me')) . '" class="no_icon">'
			. '<img src="'
				. esc_url($icons_url . '/feed-' . ( $text ? 'large' : 'giant' ) . '.gif') . '"'
				. ( $text
					? ' height="48" width="48"'
					: ' height="80" width="80"'
					)
				. ' alt="' . esc_attr(__('RSS feed', 'sem-subscribe-me')) . '"'
				. ' />'
			. '</a>'
			. '</div>' . "\n";
		
		if ( $text ) {
			echo '<div class="subscribe_me_text">' . "\n"
				. wpautop(apply_filters('widget_text', $text))
				. '</div>' . "\n";
		}
		
		echo '</div>' . "\n";

		echo '<div class="subscribe_me_ruler"></div>' . "\n";

		echo '<div class="subscribe_me_extra" style="display: none;">' . "\n";

		foreach ( subscribe_me::get_extra_services() as $service_id =>  $service ) {
			echo '<a href="' . esc_url($service['url'])  . '" class="' . $service_id . ' no_icon"'
				. ' title="' . esc_attr($service['name']) . '"'
				. ( $service_id == 'help' && ( strpos(get_option('home'), 'semiologic.com') !== false )
					? ''
					: ' rel="nofollow"'
					)
				. '>'
				. $service['name']
				. '</a>' . "\n";
		}

		echo '<div class="subscribe_me_spacer"></div>' . "\n";

		echo '</div>' . "\n";
	
		echo $after_widget;
		
		$o = ob_get_clean();
		
		$o = str_replace(
			array(
				'%enc_url%', '%enc_feed%',
				'%feed%',
				),
			array(
				urlencode($site_url), urlencode($feed_url),
				esc_url($feed_url),
				),
			$o);
		
		wp_cache_add($widget_id, $o, 'widget');
		
		echo $o;
	} # widget()
	
	
	/**
	 * get_extra_services()
	 *
	 * @return array $services
	 **/

	function get_extra_services() {
		return array(
			'rss_feed' => array(
				'name' => __('Desktop Reader', 'sem-subscribe-me'),
				'url' => 'feed:%feed%',
				),
			'bloglines' => array(
				'name' => __('Bloglines', 'sem-subscribe-me'),
				'url' => 'http://www.bloglines.com/sub/%feed%',
				),
			'google' => array(
				'name' => __('Google', 'sem-subscribe-me'),
				'url' => 'http://www.google.com/ig/add?feedurl=%enc_feed%',
				),
			'live' => array(
				'name' => __('Live', 'sem-subscribe-me'),
				'url' => 'http://www.live.com/?add=%enc_url%',
				),
			'netvibes' => array(
				'name' => __('Netvibes', 'sem-subscribe-me'),
				'url' => 'http://www.netvibes.com/subscribe.php?url=%enc_feed%',
				),
			'yahoo' => array(
				'name' => __('Yahoo!', 'sem-subscribe-me'),
				'url' => 'http://add.my.yahoo.com/rss?url=%enc_feed%',
				),
/*			'help' => array(
				'name' => __('What\'s This?', 'sem-subscribe-me'),
				'url' => 'http://www.semiologic.com/resources/blogging/help-with-feeds/'
				),
*/
			);
	} # get_extra_services()
	
	
	/**
	 * update()
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = $old_instance['text'];
		}
		
		subscribe_me::flush_cache();
		
		return $instance;
	} # update()
	
	
	/**
	 * form()
	 *
	 * @param array $instance
	 * @return void
	 **/

	function form($instance) {
		$instance = wp_parse_args($instance, subscribe_me::defaults());
		extract($instance, EXTR_SKIP);
		
		echo '<p>'
			. '<label>'
			. __('Title:', 'sem-subscribe-me')
			. '<br />'
			. '<input type="text" class="widefat"'
				. ' id="' . $this->get_field_id('title') . '"'
				. ' name="' . $this->get_field_name('title') . '"'
				. ' value="' . esc_attr($title) . '" />'
			. '</label>'
			. '</p>' . "\n";
		
		echo '<p>'
			. '<label for="' . $this->get_field_id('text') . '">'
			. __('Text:', 'sem-subscribe-me')
			. '</label>'
			. '<br />'
			. '<textarea class="widefat" cols="20" rows="6"'
				. ' name="' . $this->get_field_name('text') . '"'
				. ' id="' . $this->get_field_id('text') . '"'
				. ' >'
				. $text
			. '</textarea>'
			. '</p>' . "\n";
	} # form()
	
	
	/**
	 * defaults()
	 *
	 * @return array $instance
	 **/

	function defaults() {
		return array(
			'title' => __('Syndicate', 'sem-subscribe-me'),
			'text' => __('Subscribe to this site\'s RSS feed.', 'sem-subscribe-me'),
			);
	} # defaults()


    /**
     * flush_cache()
     *
     * @param mixed $in
     * @return mixed
     */

	function flush_cache($in = null) {
		$o = get_option('widget_subscribe_me');
		unset($o['_multiwidget']);
		
		if ( !$o )
			return $in;
		
		foreach ( array_keys($o) as $id ) {
			wp_cache_delete("subscribe_me-$id", 'widget');
		}
		
		return $in;
	} # flush_cache()
	
	
	/**
	 * upgrade()
	 *
	 * @param array $ops
	 * @return array $ops
	 **/

	function upgrade($ops) {
		$widget_contexts = class_exists('widget_contexts')
			? get_option('widget_contexts')
			: false;

		foreach ( $ops as $k => $o ) {
			$ops[$k] = array(
				'title' => $o['title'],
				);
			if ( isset($widget_contexts['subscribe_me-' . $k]) ) {
				$ops[$k]['widget_contexts'] = $widget_contexts['subscribe_me-' . $k];
			}
		}
		
		return $ops;
	} # upgrade()
	
	
	/**
	 * upgrade_2_x()
	 *
	 * @param array $ops
	 * @return array
	 **/

	function upgrade_2_x($ops) {
		$ops = !empty($ops['title']) ? array('title' => $ops['title']) : array();

        $changed = false;
		if ( is_admin() ) {
			$sidebars_widgets = get_option('sidebars_widgets', array('array_version' => 3));
		} else {
			if ( !$GLOBALS['_wp_sidebars_widgets'] )
				$GLOBALS['_wp_sidebars_widgets'] = get_option('sidebars_widgets', array('array_version' => 3));
			$sidebars_widgets =& $GLOBALS['_wp_sidebars_widgets'];
		}
		
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( !is_array($widgets) )
				continue;
			$widgets = array_map('sanitize_title', $widgets);
			$key = array_search('subscribe-me', $widgets);
			if ( $key !== false ) {
				$sidebars_widgets[$sidebar][$key] = 'subscribe_me';
				$changed = true;
				break;
			}
		}
		
		if ( $changed && is_admin() )
			update_option('sidebars_widgets', $sidebars_widgets);
		
		return $ops;
	} # upgrade_2_x()
} # subscribe_me


/**
 * the_subscribe_links()
 *
 * @param null $instance
 * @param string $args
 * @return void
 */

function the_subscribe_links($instance = null, $args = '') {
	if ( is_string($instance) )
		$instance = array('title' => $instance);
	
	$args = wp_parse_args($args, array(
		'before_widget' => '<div class="subscribe_me">' . "\n",
		'after_widget' => '</div>' . "\n",
		'before_title' => '<h2>',
		'after_title' => '</h2>' . "\n",
		));
	
	the_widget('subscribe_me', $instance, $args);
} # the_subscribe_links()


$subscribe_me = new subscribe_me();

?>