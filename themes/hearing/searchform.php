<?php

$SEARCH_TEXT = ""; //"Skriv inn søkeord og trykk retur";

$search_form  = '<div class="search_form">';
$search_form .= '<form id="searchform" method="get" action="' . home_url() . '">';
$search_submit = '<input id="searchsubmit" type="image" src="' . get_stylesheet_directory_uri() . '/img/buttonSearch.png" value="' . __('Search', 'thematic') . '" tabindex="2" />';
$search_form .= apply_filters('thematic_search_submit', $search_submit);
if (is_search()) {
	$search_form .= '<input id="s" name="s" type="text" value="' . esc_attr($_GET['s']) .'" size="32" tabindex="1" />';
} else {
	$value = $SEARCH_TEXT; //__('Search');
	$value = apply_filters('search_field_value',$value);
	$search_form .= '<input id="s" name="s" type="text" value="' . esc_attr( $value ) . '" onfocus="if (this.value == \'' . esc_attr( $value ) . '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'' . esc_attr( $value ) . '\';}" size="32" tabindex="1" />';
}
$search_form .= '</form>';
$search_form .= '</div>';

echo apply_filters('thematic_search_form', $search_form);

?>