<?php

class TopicGroup {
	var $meta_fields = array(
		"topicgroup_hidetitle"
	);
	
	function TopicGroup()
	{
		// Register custom post type
		register_post_type('topicgroup', array(
			'label' => __('Temagrupper'),
			'labels' => array(
				'singular_name' => __('Temagruppe'),
				'add_new_item' => __('Legg til ny temagruppe'),
				'edit_item' => __('Rediger temagruppe')
			),
			'public' => true,
			'show_ui' => true, // UI in admin panel
			'capability_type' => 'page',
			'hierarchical' => true,
			'rewrite' => array("slug" => "group"), // Permalinks
			'query_var' => "group", // This goes to the WP_Query schema
			'supports' => array('title'), // ,'thumbnail'
			'menu_position' => 4
		));
		
		add_filter("manage_edit-topicgroup_columns", array(&$this, "edit_columns"));
		add_action("manage_posts_custom_column", array(&$this, "custom_columns"));
		
		// Admin interface init
		add_action("admin_init", array(&$this, "admin_init"));
		add_action("template_redirect", array(&$this, 'template_redirect'));
		
		// Insert post hook
		add_action("wp_insert_post", array(&$this, "wp_insert_post"), 10, 2);
	}
	
	function edit_columns($columns)
	{
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Temagruppe"
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
	
	// Template selection
	function template_redirect()
	{
		global $wp;
		if ($wp->query_vars["post_type"] == "topicgroup")
		{
			locate_template( array('/topicgroup.php'), true );
			die();
		}
	}
	
	// When a post is inserted or updated
	function wp_insert_post($post_id, $post = null)
	{
		if ($post->post_type == "topicgroup")
		{
			// Loop through the POST data
			foreach ($this->meta_fields as $key)
			{
				$value = @$_POST[$key];
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
					foreach ($value as $entry)
						add_post_meta($post_id, $key, $entry);
				}
			}
		}
	}
	
	function admin_init() 
	{
		// Custom meta boxes for the edit topic screen
		add_meta_box("topicgroup-options", "Valg", array(&$this, "meta_topicgroup_options"), "topicgroup", "advanced", "high");
	}
	
	function meta_topicgroup_options($post)
	{
		global $post;
		$this->meta_values = get_post_custom($post->ID);
		
		$hidetitle = $this->meta_values["topicgroup_hidetitle"][0];
		$checked = "";
		if ($hidetitle) {
			$checked = 'checked="checked"';
		}
?>
	<p><label for="cb_topicgroup_hidetitle"><input type="checkbox" name="topicgroup_hidetitle" id="cb_topicgroup_hidetitle" <?php echo $checked ?> /> Skjul tittel på forsiden</label></p>

<?php
	}
	
}

// Initiate the plugin
add_action("init", "TopicGroupInit");
function TopicGroupInit() { global $p30; $p30 = new TopicGroup(); }

?>