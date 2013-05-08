<?php

$SEARCH_TEXT = ""; //"Skriv inn søkeord og trykk retur";

$search_form  = '<div class="search_form">';
$search_form .= '<form id="searchform" method="get" action="/">';
$search_submit = '<input id="searchsubmit" type="image" src="/wp-content/themes/hod/img/buttonSearch.png" value="' . __('Search', 'thematic') . '" tabindex="2" />';
$search_form .= apply_filters('thematic_search_submit', $search_submit);
if (is_search()) {
	$search_form .= '<input id="s" name="s" type="text" value="' . wp_specialchars(stripslashes($_GET['s']), true) .'" size="32" tabindex="1" />';
} else {
	$value = $SEARCH_TEXT; //__('Search');
	$value = apply_filters('search_field_value',$value);
	$search_form .= '<input id="s" name="s" type="text" value="' . $value . '" onfocus="if (this.value == \'' . $value . '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'' . $value . '\';}" size="32" tabindex="1" />';
}
$search_form .= '</form>';
$search_form .= '</div>';

echo apply_filters('thematic_search_form', $search_form);

?>