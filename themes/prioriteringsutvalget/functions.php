<?php

/**
 * Adding livereload - if on a .dev domain
 * @author Gorm
 */
function pwcc_live_reload_js() {
?>
<script>document.write('<script src="http://localhost:35729/livereload.js?snipver=1"></' + 'script>')</script>
<?php
}
/** only add if on a .dev domain */
if ( substr( $_SERVER["SERVER_NAME"], -4) == '.dev' ) {
	add_action('wp_footer', 'pwcc_live_reload_js');
}

?>