<?php

class Topic {
	private $post_type = "topic";
	private $post_slug;
	
	var $meta_fields = array(
		"topic_summary_sit_header",
		"topic_summary_sit",
		"topic_summary_goal_header",
		"topic_summary_goal",
		"topic_summary_means_header",
		"topic_summary_means",
		"topic_main_sit_header",
		"topic_main_sit",
		"topic_main_goal_header",
		"topic_main_goal",
		"topic_main_means_header",
		"topic_main_means",
		"topic_examples",
		"topic_questions_header",
		"topic_question_0",
		"topic_question_1",
		"topic_question_2",
		"topic_question_3",
		"topic_question_4"
	);
	
	var $meta_values;
	
	function Topic()
	{
		$this->post_slug = get_theme_option('slug_topic');
		
		// Register custom post type
		register_post_type($this->post_type, array(
			'label' => __('Temaer'),
			'labels' => array(
				'singular_name' => __('Tema'),
				'add_new_item' => __('Legg til nytt tema'),
				'edit_item' => __('Rediger tema')
			),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'capability_type' => 'page',
			'hierarchical' => true,
			'rewrite' => array("slug" => $this->post_slug), // Permalinks
			'query_var' => $this->post_type, // This goes to the WP_Query schema
			'supports' => array('title','thumbnail','comments','category','page-attributes'),
			'menu_position' => 4,
			'taxonomies' => array('category')
		));
		
		add_filter("manage_edit-topicgroup_columns", array(&$this, "edit_columns"));
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		
		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));
		add_action('admin_head', array(&$this, 'nhop_admin_head'));
		
		// Insert post hook
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
		
		// URL rewrites
		add_action("template_redirect", array(&$this, 'template_redirect'));
		add_action('query_vars', array(&$this, "add_query_vars"));
		add_filter('generate_rewrite_rules', array($this, 'add_rewrite_rules'));
		
		if (is_admin()) {
			wp_enqueue_script('post');
			if ( user_can_richedit() )
				wp_enqueue_script('editor');
			wp_enqueue_script('word-count');
		}
	}
	
	function nhop_admin_head() {
?>
		<style type="text/css">
			.postbox  .wp_themeSkin .mceStatusbar a.mceResize {
				top:0 !important;
			}

			.postbox .wp_themeSkin table.mceLayout {
				border: 1px solid #DFDFDF;
			}
			.tf_nhop {
				-moz-border-radius:6px 6px 6px 6px;
				border-style:solid;
				border-width:1px;
				font-size:1.7em;
				line-height:100%;
				outline:medium none;
				padding:3px 4px;
				width:100%;
				border-color:#DFDFDF;
				background-color:#FFFFFF;
			}
			.tf_nhop_small {
				-moz-border-radius:6px 6px 6px 6px;
				border-style:solid;
				border-width:1px;
				font-size:1.1em;
				line-height:100%;
				outline:medium none;
				padding:3px 4px;
				width:100%;
				border-color:#DFDFDF;
				background-color:#FFFFFF;
			}
			.my-editor-toolbar {
				height:30px;
				font-size:13px;
			}
			.my-edButtonHTML, .my-edButtonPreview {
				background-color:#F1F1F1;
				border-color:#DFDFDF;
				color:#999999;
				margin-right:15px;
				-moz-border-radius:3px 3px 0 0;
				border-style:solid;
				border-width:1px;
				cursor:pointer;
				float:right;
				height:18px;
				margin:5px 5px 0 0;
				padding:4px 5px 2px;
			}
			.my-editor-toolbar .active {
				background-color:#E9E9E9;
				border-bottom-color:#E9E9E9;
				color:#333333;
			}
		</style>
		<script type="text/javascript">
			function focusTextArea(id) {
			console.log(id);
				jQuery(document).ready(function() {
					if ( typeof tinyMCE != "undefined" ) {
						var elm = tinyMCE.get(id);
					}
					if ( ! elm || elm.isHidden() ) {
						elm = document.getElementById(id);
						isTinyMCE = false;
					}else isTinyMCE = true;
					tmpFocus = elm
					elm.focus();
					if (elm.createTextRange) {
						var range = elm.createTextRange();
						range.move("character", elm.value.length);
						range.select();
					} else if (elm.setSelectionRange) {
						elm.setSelectionRange(elm.value.length, elm.value.length);
					}
				});
			}
			
			function thickbox(link) {
				var t = link.title || link.name || null;
				var a = link.href || link.alt;
				var g = link.rel || false;
				tb_show(t,a,g);
				link.blur();
				return false;
			}
			
			function switchMode(id, to, context, thelink) {
				var ed = tinyMCE.get(id);
				if ( ! ed || ed.isHidden() ) {
					document.getElementById(id).value = switchEditors.wpautop(document.getElementById(id).value);
					if ( ed ) {
						jQuery('#editorcontainer_'+id).prev().hide();
						ed.show();
					}
					else {
						tinyMCE.execCommand("mceAddControl", false, id)
					}
				} else {
					ed.hide();
					jQuery('#editorcontainer_'+id).prev().show();
					document.getElementById(id).style.color="#000000";
				}
				jQuery('#' + context + " a").removeClass('active');
				jQuery(thelink).addClass('active');
				
			}
		</script>
<?php
	}

	// Template selection
	
	function template_redirect()
	{
		global $wp;
		if ($wp->query_vars["post_type"] == "topic")
		{
			locate_template( array('/topic.php'), true );
			die();
		}
	}
	
	// URL rewrites
	
	function add_query_vars( $query_v ) {
		$query_v[] = "show";
		$query_v[] = "statement_id";
		$query_v[] = "paged";
		return $query_v;
	}
	
	public function add_rewrite_rules( $wp_rewrite ) {
		$new_rules = array();
		
		$new_rules[$this->post_slug . '/([^/]+)/'.get_theme_option('slug_statements').'/side/?([0-9]{1,})/?$'] = 'index.php?topic=$matches[1]&show='.get_theme_option('slug_statements').'&paged=$matches[2]';
		$new_rules[$this->post_slug . '/([^/]+)/('.get_theme_option('slug_statements').')/?$'] = 'index.php?topic=$matches[1]&show=$matches[2]';
		$new_rules[$this->post_slug . '/([^/]+)/('.get_theme_option('slug_statement').')/([^/]+)/?$'] = 'index.php?topic=$matches[1]&show=$matches[2]&statement_id=$matches[3]';
		$new_rules[$this->post_slug . '/([^/]+)/('.get_theme_option('slug_full').'|'.get_theme_option('slug_examples').'|'.get_theme_option('slug_write').')/?$'] = 'index.php?topic=$matches[1]&show=$matches[2]';
		
		$old_rules = $wp_rewrite->rules;
		
		$wp_rewrite->rules = $new_rules + $old_rules;
		return $wp_rewrite->rules;
	}
	
	function edit_columns($columns)
	{
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Tema"
		);
		
		return $columns;
	}
	
	function custom_columns($column)
	{
		global $post;
		switch ($column)
		{
		}
	}
	
	// When a post is inserted or updated
	function wp_insert_post($post_id, $post = null)
	{
		if ($post->post_type == "topic")
		{
			// Loop through the POST data
			foreach ($this->meta_fields as $key)
			{
				$value = @$_POST[$key];
				$value = wp_kses( $value, '', '' ); // Using wp_kses as not sure what values will be parsed here (added during quick security audit in November 2012)
				if (empty($value))
				{
					delete_post_meta($post_id, $key);
					continue;
				}

				// If value is a string it should be unique
				if (!is_array($value))
				{
					// Update meta
					if (!update_post_meta($post_id, $key, $value))
					{
						// Or add the meta data
						add_post_meta($post_id, $key, $value);
					}
				}
				else
				{
					// If passed along is an array, we should remove all previous data
					delete_post_meta($post_id, $key);
					
					// Loop through the array adding new values to the post meta as different entries with the same name
					foreach ($value as $entry) {
						echo "$post_id, $key, $entry";
						add_post_meta($post_id, $key, $entry);
					}
				}
			}
		}
	}
	
	
	function admin_init() 
	{
		// Custom meta boxes for the edit topic screen
		add_meta_box("topic-meta-summary", "Sammendrag", array(&$this, "meta_topic_part"), "topic", "normal", "high", array("summary"));
		add_meta_box("topic-meta-main", "Hoveddel", array(&$this, "meta_topic_part"), "topic", "normal", "high", array("main"));
		if (get_theme_option('enable_questions')) {
			add_meta_box("topic-meta-questions", "Spørsmål", array(&$this, "meta_topic_questions"), "topic", "normal", "high");
		}
		add_meta_box("topic-meta-examples", "Gode eksempler", array(&$this, "meta_topic_single"), "topic", "normal", "high", array("examples"));
	}
	
	function meta_topic_part($post, $args)
	{
		global $post;
		if (!$this->meta_values) $this->meta_values = get_post_custom($post->ID);
		
		$partID = $args["args"][0];
		
		$sit = $this->meta_values["topic_".$partID."_sit"][0];
		$goal = $this->meta_values["topic_".$partID."_goal"][0];
		$means = $this->meta_values["topic_".$partID."_means"][0];
		$sit_header = $this->meta_values["topic_".$partID."_sit_header"][0];
		$goal_header = $this->meta_values["topic_".$partID."_goal_header"][0];
		$means_header = $this->meta_values["topic_".$partID."_means_header"][0];
		
		if ($post->post_date_gmt == "0000-00-00 00:00:00") {
			// Unpublished topic
			$sit_header = get_theme_option('topic_header_sit');
			$goal_header = get_theme_option('topic_header_goal');
			$means_header = get_theme_option('topic_header_means');
		}
		
?>
	<p><label for="tf_<?php echo $partID; ?>_sit_header"><strong>Seksjon 1</strong> Tittel</label></p>
	<p><input type="text" autocomplete="off" value="<?php echo $sit_header; ?>" size="30"
		id="tf_<?php echo $partID; ?>_sit_header" name="topic_<?php echo $partID; ?>_sit_header" class="tf_nhop"></p>
		
	<p><label for="tf_<?php echo $partID; ?>"><strong>Seksjon 1</strong> Brødtekst</label></p>
	<?php $this->print_editor($partID . '_sit', $sit); ?>
	
	
	<p><label for="tf_<?php echo $partID; ?>_goal_header"><strong>Seksjon 2</strong> Tittel</label></p>
	<p><input type="text" autocomplete="off" value="<?php echo $goal_header; ?>" size="30"
		id="tf_<?php echo $partID; ?>_goal_header" name="topic_<?php echo $partID; ?>_goal_header" class="tf_nhop"></p>
		
	<p><label for="tf_<?php echo $partID?>_more"><strong>Seksjon 2</strong> Brødtekst</label></p>
	<?php $this->print_editor($partID . '_goal', $goal); ?>
	
	
	<p><label for="tf_<?php echo $partID; ?>_means_header"><strong>Seksjon 3</strong> Tittel</label></p>
	<p><input type="text" autocomplete="off" value="<?php echo $means_header; ?>" size="30"
		id="tf_<?php echo $partID; ?>_means_header" name="topic_<?php echo $partID; ?>_means_header" class="tf_nhop"></p>
		
	<p><label for="tf_<?php echo $partID?>_ex"><strong>Seksjon 3</strong> Brødtekst</label></p>
	<?php $this->print_editor($partID . '_means', $means); ?>

<?php
	}
	
	function meta_topic_single($post, $args)
	{
		global $post;
		if (!$this->meta_values) $this->meta_values = get_post_custom($post->ID);
		
		$partID = $args["args"][0];
		
		$content = $this->meta_values["topic_".$partID][0];
		
?>
	<?php $this->print_editor($partID, $content); ?>

<?php
	}
	
	function meta_topic_questions($post)
	{
		global $post;
		if (!$this->meta_values) $this->meta_values = get_post_custom($post->ID);
		
		$partID = $args["args"][0];
		
		$questions_header = $this->meta_values["topic_questions_header"][0];
		
		if (!$questions_header) $questions_header = get_theme_option('topic_header_questions');
?>
		<p><label for="tf_questions_header"><strong>Tittel</strong></label></p>
		<p><input type="text" autocomplete="off" value="<?php echo $questions_header; ?>" size="30"
			id="tf_questions_header" name="topic_questions_header" class="tf_nhop"></p>
		
		<p>Legg inn opp til fem spørsmål:</p>
<?php
		global $post;
		if (!$this->meta_values) $this->meta_values = get_post_custom($post->ID);
		
		for ($i=0; $i<5; $i++) {
			$the_question = $this->meta_values["topic_question_".$i][0];
?>
			<p><label for="tf_question_<?php echo $i?>"><strong>Spørsmål <?php echo $i+1?></strong></label></p>
			<p><input type="text" autocomplete="off" value="<?php echo $the_question; ?>" size="30"
				id="tf_question_<?php echo $i?>" name="topic_question_<?php echo $i?>" class="tf_nhop tf_nhop_small"></p>
<?php
		}
	}
	
	function print_editor($ident, $content) {
		global $post;
		$tbname = 'editor_toolbar_' . $ident;
		$tfname = 'topic_'.$ident;
?>
		<div id="<?php echo $tbname; ?>" class="my-editor-toolbar">
			<a onclick="switchMode('<?php echo $tfname; ?>', 'html', '<?php echo $tbname; ?>', this);" class="my-edButtonHTML hide-if-no-js">HTML</a>
			<a onclick="switchMode('<?php echo $tfname; ?>', 'tinymce', '<?php echo $tbname; ?>', this);" class="my-edButtonPreview active hide-if-no-js">Visuell</a>
			<div class="hide-if-no-js" id="media-buttons">
Last opp / sett inn
<a title="Legg til et bilde" href="media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=484" onclick="focusTextArea('<?php echo $tfname; ?>');return thickbox(this);"><img alt="Legg til et bilde" src="/wp-admin/images/media-button-image.gif"></a>
<a title="Legg til film" href="media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=video&amp;TB_iframe=1&amp;width=640&amp;height=484" onclick="focusTextArea('<?php echo $tfname; ?>');return thickbox(this);"><img alt="Legg til film" src="/wp-admin/images/media-button-video.gif"></a>
<a title="Legg til lyd" href="media-upload.php?post_id=<?php echo $post->ID ?>&amp;type=audio&amp;TB_iframe=1&amp;width=640&amp;height=484" onclick="focusTextArea('<?php echo $tfname; ?>');return thickbox(this);"><img alt="Legg til lyd" src="/wp-admin/images/media-button-music.gif"></a>
<a title="Legg til media" href="media-upload.php?post_id=<?php echo $post->ID ?>&amp;TB_iframe=1&amp;width=640&amp;height=484" onclick="focusTextArea('<?php echo $tfname; ?>');return thickbox(this);"><img alt="Legg til media" src="/wp-admin/images/media-button-other.gif"></a>
			</div>
		</div>
		<textarea class="theEditor" style="margin:0;width:100%;height:200px;" name="<?php echo $tfname; ?>" id="<?php echo $tfname; ?>"><?php echo wpautop($content, $br = 1 ); ?></textarea>
<?php
	}
}

// Initiate the plugin
add_action("init", "TopicInit");
function TopicInit() { global $hodt; $hodt = new Topic(); }


?>