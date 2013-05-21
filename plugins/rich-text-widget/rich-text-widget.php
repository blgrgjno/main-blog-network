<?php
/*
Plugin Name: Rich Text Widget
Plugin URI: http://julienappert.com/realisations/plugin-rich-text-widget
Description: Create rich text widgets.
Version: 1.1.1
Author: Julien Appert
Author URI: http://julienappert.com
*/
class richText extends WP_Widget {

	function richText() {

		$locale = get_locale ();
		if ( empty($locale) )
			$locale = 'en_US';

		$mofile = dirname (__FILE__)."/locale/$locale.mo";
		load_textdomain ('rtw', $mofile);		
	
		$widget_ops = array('classname' => 'widget_richtext', 'description' => __( 'WYSIWYG widget','rtw' ) );
		$control_ops = array('width' => 700, 'height' => 550);
		$this->WP_Widget('richetext', __('Rich Text','rtw'), $widget_ops,$control_ops);
		$this->alt_option_name = 'widget_richtext';
	}

	function widget( $args, $instance ) {
		extract($args, EXTR_SKIP);
		$title = strlen($instance['title'])>0 ? esc_attr($instance['title']) : '';		
		$text = strlen($instance['text'])>0 ? esc_attr($instance['text']) : '';	

		$text = apply_filters('widget_richtext' , $text );	
		
		echo $before_widget;
			if(strlen($title)>0) echo $before_title.$title.$after_title;	
			echo '<div class="widget_richtext_content">'.html_entity_decode($text).'</div>';
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = $new_instance['text'];

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_richtext']) )
			delete_option('widget_richtext');

		return $instance;
	}	

	function form( $instance ) {

		$title = isset($instance['title']) ? esc_attr($instance['title']) : ''; 
		$text = isset($instance['text']) ? esc_attr($instance['text']) : ''; 
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<div class="media-buttons" style="display:none">
			<?php do_action('media_buttons', 'richtext'); ?>
		</div>			
		<p>
			<textarea class="widefat rtw" style="width:100%;" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		</p>
		<?php
	}
	
 }

 
class richTextWidget{

	function richTextWidget(){$this->__construct();}
		
	function __construct(){
		add_action( 'widgets_init', array(&$this,'widgets_init'));
		add_action('admin_head',array(&$this,'admin_head'));
		add_action('init',array(&$this,'init'));
		add_filter('get_media_item_args',array(&$this,'media_args'),10,1);
		add_action( 'wp_ajax_rtw_save', array(&$this,'wp_ajax_save'));
	}
	
	function wp_ajax_save(){
		$title = $_POST['title'];
		$content = $_POST['content'];
		$number = $_POST['number'];
		
		$option = get_option('widget_richetext');
		if(!$option){
			$option = array(array(),'_multiwidget'=>1);
		}
		$option[$number] = array();
		$option[$number]['title'] = stripslashes($title);
		$option[$number]['text'] = stripslashes($content);
		update_option('widget_richetext',$option);		
	}	
	
	function widgets_init(){
		register_widget('richText');
	}
	
	
	function media_args($args){		
		if($_GET['post_id'] == -1) $args['send'] = 1;
		return $args;
	}
	
	function init(){
		$locale = get_locale ();
		if ( empty($locale) )
			$locale = 'en_US';

		$mofile = dirname (__FILE__)."/locale/$locale.mo";
		load_textdomain ('rtw', $mofile);	
		wp_enqueue_script('thickbox');    
	}
	function admin_head(){
		global $pagenow;
		if($pagenow == 'widgets.php'){
			//$richedit =  user_can_richedit();
			$richedit =  true;
			if ( $richedit ) {
				$version = apply_filters('tiny_mce_version', '');
				$version = 'ver=' . $tinymce_version . $version;
				$language = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
				$no_captions = ( apply_filters( 'disable_captions', '' ) ) ? true : false;
				$mce_spellchecker_languages = apply_filters('mce_spellchecker_languages', '+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv');
				?>		
				<script type="text/javascript">
					var deactivate = "<?php echo esc_js(__('Please deactivate the editor before saving the widget','rtw')); ?>";
					var spellchecker_languages = "<?php echo $mce_spellchecker_languages; ?>";
					var language = '<?php echo $language; ?>';
					var content_css = <?php get_option('wpurl'); ?>'/wp-includes/js/tinymce/wordpress.css';
				</script>	
				<script type="text/javascript" src="<?php echo plugins_url('tiny_mce/tiny_mce.js', __FILE__); ?>"></script>		
				<script type="text/javascript" src="<?php echo plugins_url('rich-text-widget.js', __FILE__); ?>"></script>	
			
				<link rel='stylesheet' id='thickbox-css'  href='<?php bloginfo('wpurl'); ?>/wp-includes/js/thickbox/thickbox.css' type='text/css' media='all' />
				<script type="text/javascript" src="<?php echo plugins_url('media-upload.js', __FILE__); ?>"></script>			
			<?php  }		
		}
	}
}
new richTextWidget();