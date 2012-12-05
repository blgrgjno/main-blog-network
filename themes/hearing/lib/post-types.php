<?php

// Add to the admin_init hook of your theme functions.php file
function hearing_add_categories_to_page() {
    // Add category metabox to page 
	add_meta_box('categorydiv', __('Categories'), 'post_categories_meta_box', 'page', 'side', 'default');
	register_taxonomy_for_object_type('category', 'page');
}

add_action( 'admin_init', 'hearing_add_categories_to_page', 1 );

?>