<?php
/*
 Plugin Name: Spectacu.la Page Widget
 Plugin URI: http://spectacu.la
 Description: Show the content of a selected page in a widget. Also gives you control over title behaviour and the page's visibility elsewhere in Wordpress.
 Version: 1.0.7
 Author: James R Whitehead of Spectacu.la
 Author URI: http://www.interconnectit.com

 Release notes: 1.0.0 Initial release
				1.0.1 Added an option to include a clear block at the end of the content. Helpful when you have a page with some floated elements in it and quite short content.
				1.0.2 Tidied up the widget display name in the widget admin page. Also got rid of the unneeded word "widget" from the widget. Added option to show the widget even when viewing the page that's set to show in the widget.
				1.0.3 Edited readme tag and descriptions.
				1.0.4 Fixed issue with wp_list_pages_excludes not respecting other plug-ins wishes.
				1.0.5 Very minor change to bypass a problem I had where a page_id is passed to register_sidebar as part of another plug-in I'm working on and thus interrupts my page_id for this plug-in.
				1.0.6 Found a problem with some of my logic that resulted in the widget not showing up when it would otherwise be expected to. Fixed it.
				1.0.7 Added an option to the widget interface to allow you to add extra CSS classes to the widget.
*/

define ('SPEC_PAGEWIDGET_VER', 2.8);
define ('SPEC_PAGEWIDGET_DOM', 'spec-page-widget');
define ('SPEC_PAGEWIDGET_OPT', 'specpagewidgets');

if (!class_exists('spec_page_widget')) {
	class spec_page_widget extends WP_Widget {

		var $defaults = array(
							  'title_toggle' => true,
							  'link_toggle' => true,
							  'hide_toggle' => false,
							  'excerpt_toggle' => false,
							  'page_id' => 0,
							  'title' => '',
							  'clear_toggle' => false,
							  'self_show' => false,
							  'class' => ''
							);

		/*
		 constructor.
		*/
		function spec_page_widget() {
			$abs_rel_path = trim(trim(str_replace(trim(ABSPATH, '/'), '', dirname( __FILE__)), '/'), '\\') . '/lang/';
			load_plugin_textdomain(SPEC_PAGEWIDGET_DOM, $abs_rel_path);

			$widget_ops = array('classname' => 'spec_page_widget', 'description' => __( 'Show the content of a selected page in a widget. Gives you control over title behaviour and the page\'s visibility elsewhere in Wordpress.', SPEC_PAGEWIDGET_DOM));
			$this->WP_Widget(SPEC_PAGEWIDGET_OPT, __('Spectacu.la Page', SPEC_PAGEWIDGET_DOM), $widget_ops, array('width' => 450));

			$this->pages = get_pages();
			$this->page_ids = array_map(create_function('$a', 'return $a->ID;'), $this->pages);

			if (!is_admin())
				add_filter('wp_list_pages_excludes', array(&$this, 'excludes_pages'));

		}


		function widget($args, $instance ) {
			global $post;
			extract((array)$instance, EXTR_SKIP);
			extract($args, EXTR_SKIP);

			if ( ! empty( $class ) )
				$before_widget = $this->add_class_attrib( $before_widget, $this->clean_classes( $class ) );

			// Check that the page chosen exists.
			if (in_array($page_id, $this->page_ids) && ( ( ( $post->ID == $page_id ) && $self_show ) ) || ( $post->ID != $page_id ) ) {
				$page  = get_post($page_id);

				if ($title_toggle) {
					$title = $alt_title != '' ? apply_filters('the_title', $alt_title, $page_id) : apply_filters('the_title', $page->post_title, $page_id);
					if ($link_toggle){
						// Use class instead of ID as page could be on display in more than one place.
						$title = '<a href="' . get_permalink($page_id) . '" class="' . sanitize_title($page->post_title) . '-' . $page->ID . '">' . $title . '</a>';
					}
				}

				if ($clear_toggle) {
					$clear = '<div style="clear:both;height:0;overflow:hidden;visibility:hidden"></div>';
				}

				if (!post_password_required($page_id)) {
					if ($excerpt_toggle) {
						$content = $page->post_excerpt ? apply_filters('the_excerpt', $page->post_excerpt) : $this->excerptify($page->post_content);
					} else {
						$content = apply_filters('the_content', $page->post_content);
					}
				} else {
					$content = __('Password protected page', SPEC_PAGEWIDGET_DOM);
				}

				echo $before_widget;

				echo $title		? $before_title . $title . $after_title : '';
				echo $content . $clear;

				echo $after_widget;
			}
		}


		function update($new_instance = array(), $old_instance = array()) {

			$output = array_merge((array)$new_instance, $this->defaults);

			$output['title_toggle'] = intval($new_instance['title_toggle']) == 1 ? true : false;
			$output['link_toggle'] = intval($new_instance['link_toggle']) == 1 ? true : false;
			$output['hide_toggle'] = intval($new_instance['hide_toggle']) == 1 ? true : false;
			$output['self_show'] = intval($new_instance['self_show']) == 1 ? true : false;
			$output['excerpt_toggle'] = intval($new_instance['excerpt_toggle']) == 1 ? true : false;
			$output['page_id'] = in_array(intval($new_instance['page_id']), $this->page_ids) ? intval($new_instance['page_id']) : 0;
			$output['alt_title'] =  $new_instance['alt_title'] != '' ? $new_instance['alt_title'] : '';
			$output['clear_toggle'] = intval($new_instance['clear_toggle']) == 1 ? true : false;
			$output[ 'class' ] = $this->clean_classes( $new_instance[ 'class' ] );

			return $output;
		}


		function form($instance = array()) {
			$instance = array_merge($this->defaults, (array)$instance);
			extract($instance, EXTR_SKIP);
			unset($disabled);

			// Set up the display name for the widget admin page
			$page = $page_id > 0 ? get_post($page_id) : null; ?>
			<input id="display-title" type="hidden" value="<?php echo $alt_title != '' ? apply_filters('the_title', $alt_title, $page_id) : ($page_id > 0 ? apply_filters('the_title', $page->post_title, $page_id) : __('None', SPEC_ADVSEARCH_DOM));?>"/>

			<p>
				<label for="<?php echo $this->get_field_id('page_id'); ?>"><strong><?php _e('Select the page:', SPEC_PAGEWIDGET_DOM); ?></strong></label>
				<select class="widefat" id="<?php echo $this->get_field_id('page_id'); ?>" name="<?php echo $this->get_field_name('page_id'); ?>" >
					<option value="0"<?php echo $page_id == 0 ? ' selected="selected"' : ''; ?>><?php _e('None', SPEC_PAGEWIDGET_DOM);?></option>
			<?php
			foreach($this->pages as $page) {
				echo '<option value="' . $page->ID . '"' . ($page_id == $page->ID ? ' selected="selected"' : '') . '>' . $page->post_title . '</option>';
			}
			?>

				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('title_toggle'); ?>"><?php _e('Show title:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input onchange="specFieldToggle('#<?php echo $this->get_field_id('title_toggle'); ?>', '#<?php echo $this->get_field_id('title_stuff'); ?>')" type="checkbox"<?php echo $title_toggle ? ' checked="checked"' : '' ; ?> id="<?php echo $this->get_field_id('title_toggle'); ?>" name="<?php echo $this->get_field_name('title_toggle'); ?>" value="1"/>
			</p>

			<fieldset id="<?php echo $this->get_field_id('title_stuff'); ?>" style="border:solid 1px #ccc;padding: 10px;margin-bottom:1em;-moz-border-radius: 4px;">
				<p>
					<label for="<?php echo $this->get_field_id('link_toggle'); ?>"><?php _e('Title should link to source page:', SPEC_PAGEWIDGET_DOM); ?></label>
					<input type="checkbox"<?php echo $link_toggle ? ' checked="checked"' : '' ; ?> id="<?php echo $this->get_field_id('link_toggle'); ?>" name="<?php echo $this->get_field_name('link_toggle'); ?>" value="1"/>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id('alt_title'); ?>"><?php _e('Use this text instead of the page title:', SPEC_PAGEWIDGET_DOM); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('alt_title'); ?>" name="<?php echo $this->get_field_name('alt_title'); ?>" type="text" value="<?php echo $alt_title; ?>" />
				</p>
			</fieldset>

			<script type="text/javascript" language="JavaScript">
				//<![CDATA[
				function specFieldToggle(trigger, target) {
					if(typeof jQuery != "undefined"){
						 if ( jQuery(trigger).attr('checked')){
							jQuery(target).css({color:'#000'}).find('input').attr({disabled:''});
						} else {
							jQuery(target).css({color:'#ccc'}).find('input').attr({disabled:'disabled'});
						}
					}
				}

				specFieldToggle('#<?php echo $this->get_field_id('title_toggle'); ?>', '#<?php echo $this->get_field_id('title_stuff'); ?>');
				//]]>
			</script>

			<p>
				<label for="<?php echo $this->get_field_id('excerpt_toggle'); ?>"><?php _e('Use excerpt rather than full content:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input type="checkbox"<?php echo $excerpt_toggle ? ' checked="checked"' : '' ; ?> id="<?php echo $this->get_field_id('excerpt_toggle'); ?>" name="<?php echo $this->get_field_name('excerpt_toggle'); ?>" value="1"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('clear_toggle'); ?>"><?php _e('Add a clear block at the end of the content:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input type="checkbox"<?php echo $clear_toggle ? ' checked="checked"' : '' ; ?> id="<?php echo $this->get_field_id('clear_toggle'); ?>" name="<?php echo $this->get_field_name('clear_toggle'); ?>" value="1"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('self_show'); ?>"><?php _e('Show this widget if on the page that matched the ID set above:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input type="checkbox"<?php echo $self_show ? ' checked="checked"' : '' ; ?> id="<?php echo $this->get_field_id('self_show'); ?>" name="<?php echo $this->get_field_name('self_show'); ?>" value="1"/>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('CSS Classes:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo $class; ?>" />
			</p>

			<hr/>
			<p>
				<?php
				if (in_array($page_id, (array)$this->excludes_pages()) && !$hide_toggle) {
					echo '<em style="color:#a44">' . __('This page has been excluded using another other widget.', SPEC_PAGEWIDGET_DOM) . '</em><br/>';
					$disabled = ' disabled="disabled"';
				}?>
				<label<?php echo $disabled ? ' style="color:#ccc"' : '';?> for="<?php echo $this->get_field_id('hide_toggle'); ?>"><?php _e('Exclude page from wp_list_pages:', SPEC_PAGEWIDGET_DOM); ?></label>
				<input type="checkbox"<?php echo $hide_toggle ? ' checked="checked"' : '' ; echo $disabled;?> id="<?php echo $this->get_field_id('hide_toggle'); ?>" name="<?php echo $this->get_field_name('hide_toggle'); ?>" value="1"/>
			</p>

			<?php
		}


		function excerptify($text = '') {
			if ($text == '')
				return '';

			$text = strip_shortcodes($text);
			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text);

			$excerpt_length = apply_filters('excerpt_length', 55);
			$words = explode(' ', $text, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				array_push($words, '[...]');
				$text = implode(' ', $words);
			}

			return apply_filters('the_excerpt', $text);
		}


		function excludes_pages( $output = array() ) {
			if (isset($this->exclusions))
				return $this->exclusions;

			$options = get_option($this->option_name, array());
			$sidebars = wp_get_sidebars_widgets();
			$inactive = $sidebars['wp_inactive_widgets'];

			$output = (array) $output;

			foreach($options as $key => $option) {
				if (!in_array(SPEC_PAGEWIDGET_OPT . '-' . $key, (array)$inactive)) {
					if ($option['page_id'] > 0 && $option['hide_toggle'])
						$output[] = $option['page_id'];
				}
			}

			$this->exclusions = $output;
			return $output;
		}

		function add_class_attrib( $tag, $class ) {
			$output = preg_replace( '/(^[^<]*?<\w+\s?[^>]*?)(?:class=[\'"])?([^\'"]*?)[\'"]?(>.*)/is', '$1 class="' . strtolower( $class ) . ' $2"$3', $tag );
			$output = preg_replace( '/\s+/is', ' ', $output );
			return $output;
		}

		function clean_classes( $classes = '' ) {
			$tmp = array();
			foreach( ( array ) explode( ' ', $classes ) as $class ) {
				$tmp[] = sanitize_html_class( trim( $class ) );
			}
			$classes = implode( ' ', $tmp );
			return $classes;
		}
	}


	/*
	 Only load the plug-in if we're running a version of WP that'll not break things.
	*/
	if (version_compare($wp_version, SPEC_PAGEWIDGET_VER, 'ge'));
		add_action('widgets_init', create_function('', 'return register_widget("spec_page_widget");'));
}

?>
