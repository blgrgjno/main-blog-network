<?php 

class Utils{

	function __construct()
	{

			//add stream javascript ajax
			add_action('wp_ajax_instagrabber_load_taxonomies', array($this, 'get_categories') );
			
			add_action('wp_ajax_instagrabber_load_terms', array($this, 'get_categories_terms') );

			add_action('wp_ajax_instagrabber_load_tags', array($this, 'get_tags') );


			//add text and link to plugin page
			add_action('the_content', array($this, 'add_instagrabber_text') );

			//add html comment with instagram disclaimer text
			add_action('wp_footer', array($this, 'html_comment'));

			//add footer admin text
			//add_filter('admin_footer_text', array($this, 'admin_footer_text'), 999, 1);
	}

	function checkboxes_users( $args = '' ) {
		$defaults = array(
			'show_option_all' => '', 'show_option_none' => '', 'hide_if_only_one_author' => '',
			'orderby' => 'display_name', 'order' => 'ASC',
			'include' => '', 'exclude' => '', 'multi' => 0,
			'show' => 'display_name', 'echo' => 1,
			'selected' => 0, 'name' => 'user', 'class' => '', 'id' => '',
			'blog_id' => $GLOBALS['blog_id'], 'who' => '', 'include_selected' => false
		);

		$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$query_args = wp_array_slice_assoc( $r, array( 'blog_id', 'include', 'exclude', 'orderby', 'order', 'who' ) );
		$query_args['fields'] = array( 'ID', $show );
		$users = get_users( $query_args );

		$output = '';
		if ( !empty($users) && ( empty($hide_if_only_one_author) || count($users) > 1 ) ) {
			$name = esc_attr( $name );

			$found_selected = false;
			foreach ( (array) $users as $user ) {
				$user->ID = (int) $user->ID;
				$_selected = in_array($user->ID, $selected) ? 'checked="checked"' : '';
				if ( $_selected )
					$found_selected = true;
				$display = !empty($user->$show) ? $user->$show : '('. $user->user_login . ')';
				$output .= "\t<input type='checkbox' name='{$name}'{$id} class='$class' value='$user->ID' $_selected>\t<strong>". esc_html($display) ."</strong><br />\n";
			}

			// if ( $include_selected && ! $found_selected && ( $selected > 0 ) ) {
			// 	$user = get_userdata( $selected );
			// 	$_selected = in_array($user->ID, $selected);
			// 	$display = !empty($user->$show) ? $user->$show : '('. $user->user_login . ')';
			// 	$output .= "\t<option value='$user->ID'$_selected>" . esc_html($display) . "</option>\n";
			// }
		}

		$output = apply_filters('instagrabber_checkboxes_users', $output);

		if ( $echo )
			echo $output;

		return $output;
	}

	function post_types(){
		$types = get_post_types( array('capability_type' => 'post', 'public' => true), 'names');
		foreach ($types as $key => $value) {
			if($value == 'attachment')
				unset($types[$key]);
		}
		return $types;
	}

	function get_images_sizes_width(){
		global $_wp_additional_image_sizes;
		$additional_image_sizes = $_wp_additional_image_sizes;
		$default_image_sizes = get_intermediate_image_sizes();
		$sizes = array();
		$sizes['full'] = 612;
		foreach ($default_image_sizes as $key => $name) {
			$option = get_option($name.'_size_w');
			if($option && $option < 612)
				$sizes[$name] = $option;
		}

		foreach ($additional_image_sizes as $name => $attr) {
			if ($attr['width'] < 612) {
				$sizes[$name] = $attr['width'];
			}
			
		}



		return $sizes;
	}

	function get_placeholders($img, $stream){
			//default placeholders
			$title_limit = !get_option('instagrabber_title_limit') ? 10 : (int) get_option('instagrabber_title_limit');
			$striped_caption = $img->caption;
			if($stream->allow_hashtags == 0 || $stream->allow_hashtags == null)
				$striped_caption = preg_replace("/#([a-zåäöA-ZÅÄÖ0-9_]+)/", '', $striped_caption);
			//$striped_caption = str_replace('...', '', $striped_caption);
			$striped_caption = wp_trim_words($striped_caption, $title_limit, null);
			$placeholders = array(
					'%user%' => $img->user_name,
					'%tag%' => $stream->tag,
					'%caption%' => $striped_caption,
					'%date%' => $img->pic_timestamp
			);

			return apply_filters('get_placeholders', $placeholders, $img, $stream);
	}

	/**
	 * Get taxonomies for ajax
	 */
	function get_categories($post_type = 'post', $old = 'none'){
		
		if(isset($_GET['type']))
			$post_type = $_GET['type'];

		$taxonomies = get_object_taxonomies($post_type,'objects');
		?>
		<select name="taxonomy" id="taxonomy">
			<option value="none"><?php _e('None', 'instagrabber') ?></option>
		<?php foreach ($taxonomies as $key => $tax): 
			if($tax->hierarchical != 1)
				continue;
		?>
			<option value="<?php echo $key ?>" <?php selected($key, $old) ?>><?php echo $tax->labels->name ?></option>
		<?php endforeach ?>
		</select>
		<?php
		if(isset($_GET['type']))
			die();
		
	}

	/**
	 * Get terms
	 * TODO: Use built in categories dropdown.
	 */
	function get_categories_terms($taxonomy = 'category', $old = 'none'){

		if(isset($_GET['tax']))
			$taxonomy = $_GET['tax'];

		if ($taxonomy != 'none') {				
			$terms = get_terms( $taxonomy, array('hide_empty' => 0) );
			?>
			<select name="terms" id="terms">
				<option value="none"><?php _e('None', 'instagrabber') ?></option>
			<?php foreach ($terms as $key => $term): ?>
				<option value="<?php echo $term->term_id ?>" <?php selected($term->term_id, $old) ?>><?php echo $term->name ?></option>
			<?php endforeach ?>
			</select>
			<?php
		
		}
		if(isset($_GET['tax']))
			die();
	}

	/**
	 * Get taxonomies with tag functionallity
	 */
	function get_tags($post_type = 'post', $old = 'none'){
		if(isset($_GET['type']))
			$post_type = $_GET['type'];

		$taxonomies = get_object_taxonomies($post_type,'objects');
		?>
		<select name="taxonomy_tag" id="taxonomy_tag">
			<option value="none"><?php _e('None', 'instagrabber') ?></option>
		<?php foreach ($taxonomies as $key => $tax):
			
			if($tax->hierarchical != 0 || $key == 'post_format' )
				continue;
		?>
			<option value="<?php echo $key ?>" <?php selected($key, $old) ?>><?php echo $tax->labels->name ?></option>
		<?php endforeach ?>
		</select>
		<?php
		if(isset($_GET['type']))
			die();
	}

	function add_instagrabber_text($content){
		global $post;

		if(get_option('instagrabberlove') == "true" && get_post_meta($post->ID, '_instagrabber_insta_id', true)){
			$content .= '<p>This image is imported from Instagram with <a href="http://wordpress.org/extend/plugins/instagrabber/">Instagrabber</a>, a WordPress plugin.';	
		}

		return $content;
	}

	function admin_footer_text($text){
		$text .= '<br />';
		$footer_text = '<p class="instagrabber_footer_text">This application/website uses the Instagram(tm) API and is not endorsed or certified by Instagram or Instagram, Inc. All Instagram(tm) logos and trademarks displayed on this application/website are property of Instagram, Inc.</p>';
		$text .=  $footer_text;
		return $text;

	}

	function html_comment(){
		echo '<!-- This application/website uses the Instagram(tm) API and is not endorsed or certified by Instagram or Instagram, Inc. All Instagram(tm) logos and trademarks displayed on this application/website are property of Instagram, Inc. -->';
	}
}

$Utils = new Utils;

 ?>