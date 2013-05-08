<?php
/*
Plugin Name: Rich Text Widget
Plugin URI: Plugin URI: http://www.ajcrea.com/plugins/wordpress/plugin-wordpress-des-widgets-riches.html
Description: <strong>(Do Not Update! Modified by TKM/MW)</strong> Create rich text widgets.
Version: 0.2
Author: Ajcrea
Author URI: http://ajcrea.com
*/

/*
 * Disabling plugin update checks
 * This is to avoid malicious use of the WordPress.org plugin repository to force updates on this plugin
 * Based on code from http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 * 
 * @author Ryan Hellyer <ryan@metronet.no>
 * @param unknown $r
 * @param string $url
 */
function dss_richtext_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'dss_richtext_hidden_plugin', 5, 2 );


add_action('admin_head','rtw_adminhead');

function rtw_adminhead(){
	global $pagenow;
	if($pagenow == 'widgets.php'){
		$richedit =  user_can_richedit();
		if ( $richedit ) {
			$version = apply_filters('tiny_mce_version', '');
			$version = 'ver=' . $tinymce_version . $version;
			$baseurl = includes_url('js/tinymce');
			$language = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
			$no_captions = ( apply_filters( 'disable_captions', '' ) ) ? true : false;
			$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');
			?>
			<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce.js"></script>
			<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/thickbox/thickbox.js"></script>
			<script type="text/javascript">
				var spellchecker_languages = "<?php echo $mce_spellchecker_languages; ?>";
				var language = '<?php echo $language; ?>';
				var content_css = <?php get_option('wpurl'); ?>'/wp-includes/js/tinymce/wordpress.css';
			</script>
			<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/rich-text-widget/rich-text-widget.js"></script>
			<?php 
			include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');
			if ( 'en' != $language && isset($lang))
				echo "<script type='text/javascript'>\n".($lang)."\n</script>\n";
			else
				echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
			?>
			<?php
		}
		?>
		<link rel='stylesheet' id='thickbox-css'  href='<?php bloginfo('wpurl'); ?>/wp-includes/js/thickbox/thickbox.css' type='text/css' media='all' />
		<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/rich-text-widget/js_quicktags.js"></script>
		<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-content/plugins/rich-text-widget/media-upload.js"></script>	
		<script type='text/javascript'>
		/* <![CDATA[ */
		var quicktagsL10n = {
			quickLinks: "<?php _e('(Quick Links)'); ?>",
			wordLookup: "<?php _e('Enter a word to look up:'); ?>",
			dictionaryLookup: "<?php echo esc_attr(__('Dictionary lookup')); ?>",
			lookup: "<?php echo esc_attr(__('lookup')); ?>",
			closeAllOpenTags: "<?php echo esc_attr(__('Close all open tags')); ?>",
			closeTags: "<?php echo esc_attr(__('close tags')); ?>",
			enterURL: "<?php echo  __('Enter the URL'); ?>",
			enterImageURL: "<?php echo __('Enter the URL of the image'); ?>",
			enterImageDescription: "<?php echo __('Enter a description of the image'); ?>"
		};
		try{convertEntities(quicktagsL10n);}catch(e){};
		/* ]]> */
		</script>
		<style type="text/css">
			#edButtonPreview,
			#edButtonHTML {
				background-color: #f1f1f1;
				border-color: #dfdfdf;
				color: #999;
				margin-top:0px;
			}
			#editor-toolbar .active {
				border-bottom-color: #e9e9e9;
				background-color: #e9e9e9;
				color: #333;
			}	
		</style>
		<?php
	}
}

function rwt_widget($args, $widget_args = 1) {

	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('rwt_text');
	if ( !isset($options[$number]) )
		return;

	$title = apply_filters('rtw_title', $options[$number]['title']);
	$text = apply_filters( 'rtw_text', $options[$number]['text'] );
?>
		<?php echo $before_widget; ?>
			<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget; ?>
<?php
}

class rtw_widget extends WP_Widget {

	function rtw_widget() {
		$widget_ops = array('classname' => 'rtw_widget', 'description' => __('Rich Text Widget'));
		$control_ops = array('width' => 700, 'height' => 550);
		$this->WP_Widget('richText', "NHOP: Rik tekst", $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		//MOD: Don't display on list pages and topic pages:
		global $wp_query;
		$display_me = false;
		if ($instance['show_on_topic'] && $wp_query->query_vars['post_type'] == "topic") {
			$display_me = true;
		}
		if ($instance['show_on_front'] && is_front_page()) {
			$display_me = true;
		}
		if (!$display_me) return;
		
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
		$text = apply_filters( 'widget_text', $instance['text'] );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = wp_filter_post_kses( $new_instance['text'] );
		$instance['show_on_front'] = isset($new_instance['show_on_front']);
		$instance['show_on_topic'] = isset($new_instance['show_on_topic']);
		return $instance;
	}

	function form( $instance ) {
		//$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = format_to_edit($instance['text']);
		$richedit = user_can_richedit();
		$show_on_front = $instance['show_on_front'];
		$show_on_topic = $instance['show_on_topic'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<div id="editor-toolbar">
			<?php if ( $richedit ) { ?>
					<?php $wp_default_editor = wp_default_editor(); ?>
					<div class="zerosize"><input accesskey="e" type="button" onclick="switchEditors('<?php echo $this->get_field_id('text'); ?>')" /></div>
					<?php	if ( 'html' == $wp_default_editor ) {	add_filter('the_editor_content', 'wp_htmledit_pre'); ?>
						<a id="edButtonHTML" class="active hide-if-no-js" onclick="switchEditors('<?php echo $this->get_field_id('text'); ?>', 'html');"><?php _e('HTML'); ?></a>
						<a id="edButtonPreview" class="hide-if-no-js" onclick="switchEditors('<?php echo $this->get_field_id('text'); ?>', 'tinymce');"><?php _e('Visual'); ?></a>
					<?php	} else {
						add_filter('the_editor_content', 'wp_richedit_pre'); ?>
						<a id="edButtonHTML" class="hide-if-no-js" onclick="switchEditors('<?php echo $this->get_field_id('text'); ?>', 'html');"><?php _e('HTML'); ?></a>
						<a id="edButtonPreview" class="active hide-if-no-js" onclick="switchEditors('<?php echo $this->get_field_id('text'); ?>', 'tinymce');"><?php _e('Visual'); ?></a>
					<?php	} ?>
			<?php } ?>
			<div id="media-buttons">
				<?php rtw_media_buttons($this->get_field_id('text')); ?>
			</div>
		</div>
		<div id="quicktags">
		</div>
		<div id="editorcontainer">
			<textarea class="widefat rtw" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		</div>
		
		<p><input id="<?php echo $this->get_field_id('show_on_front'); ?>" name="<?php echo $this->get_field_name('show_on_front'); ?>" type="checkbox" <?php checked(isset($instance['show_on_front']) ? $instance['show_on_front'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_on_front'); ?>">Vis på forsiden</label></p>
		
		<p><input id="<?php echo $this->get_field_id('show_on_topic'); ?>" name="<?php echo $this->get_field_name('show_on_topic'); ?>" type="checkbox" <?php checked(isset($instance['show_on_topic']) ? $instance['show_on_topic'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_on_topic'); ?>">Vis på emnesiden</label></p>

<?php
	}
}

function rtw_media_buttons($editor) {
	$uploading_iframe_ID = time();
	$context = apply_filters('media_buttons_context', __('Upload/Insert %s'));
	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID&amp;editor=$editor";
	$media_title = __('Add Media');
	$image_upload_iframe_src = apply_filters('image_upload_iframe_src', "$media_upload_iframe_src&amp;type=image");
	$image_title = __('Add an Image');
	$video_upload_iframe_src = apply_filters('video_upload_iframe_src', "$media_upload_iframe_src&amp;type=video");
	$video_title = __('Add Video');
	$audio_upload_iframe_src = apply_filters('audio_upload_iframe_src', "$media_upload_iframe_src&amp;type=audio");
	$audio_title = __('Add Audio');
	$out = <<<EOF

	<a href="{$image_upload_iframe_src}&amp;TB_iframe=true" id="add_image" class="thickbox" title='$image_title' onclick="return false;"><img src='images/media-button-image.gif' alt='$image_title' /></a>
	<a href="{$video_upload_iframe_src}&amp;TB_iframe=true" id="add_video" class="thickbox" title='$video_title' onclick="return false;"><img src='images/media-button-video.gif' alt='$video_title' /></a>
	<a href="{$audio_upload_iframe_src}&amp;TB_iframe=true" id="add_audio" class="thickbox" title='$audio_title' onclick="return false;"><img src='images/media-button-music.gif' alt='$audio_title' /></a>
	<a href="{$media_upload_iframe_src}&amp;TB_iframe=true" id="add_media" class="thickbox" title='$media_title' onclick="return false;"><img src='images/media-button-other.gif' alt='$media_title' /></a>

EOF;
	printf($context, $out);
}

function rtw_widget_register(){
	register_widget('rtw_widget');
	do_action('widgets_init');
}
add_action('init', 'rtw_widget_register');

?>