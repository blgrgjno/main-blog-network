<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function ntpchild_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'ntpchild_hidden_theme', 5, 2 );

function dss_custom_header_options() {
  if ( ! current_user_can('edit_theme_options') )
    return;
  
  if ( "POST" == $_SERVER['REQUEST_METHOD'] ) {
    check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );    
    if ( isset( $_POST['brand-image'] ) ) {
      $brand_image = wp_kses( $_POST['brand-image'], '', '' );
      set_theme_mod( 'header_brand_image', $brand_image );
    }

    if ( isset( $_POST['brand-url'] ) ) {
      $url = esc_url( $_POST['brand-url'] );
      set_theme_mod( 'header_brand_url', $url );
    }
  }
  ?>
<table class="form-table">
<tbody>
<tr valign="top" id="brand-image-row">
<th scope="row"><?php _e('Brand image'); ?></th>
<td>
<p>
<input type="text" name="brand-image" id="brand-image" value="<?php echo esc_attr( get_theme_mod( 'header_brand_image', '' ) ); ?>" />
</p>
</tr>
<tr valign="top" id="brand-url-row">
<th scope="row"><?php _e('Brand URL'); ?></th>
<td>
<p>
<input type="text" name="brand-url" id="brand-url" value="<?php echo esc_attr( get_theme_mod( 'header_brand_url', '' ) ); ?>" />
</p>
</tr>
</tbody>
</table>
<?php 
}

add_action( 'custom_header_options', 'dss_custom_header_options' );

//[dsslatestcomments]
function dss_latest_comments_func ( $atts ) {

  $comments = get_comments('status=approve&number=5');

  if ( count( $comments ) > 0) {
    echo "<div>";
  }
  
  foreach ( $comments as $comment ) {
    $my_id = $comment->comment_post_ID ; $post_id_comms = get_post($my_id); $title = $post_id_comms->post_title;
?>    
    Who: <?php echo($comment->comment_author);?><br />
    About: <a href="<?php echo get_permalink($my_id) ?>#comment-<?php echo $comment->comment_post_ID?>" title="on <?php echo $title ?>"><?php echo $title ?></a><br />
    What they said: <?php echo($comment->comment_content);?><br />
    When they said it: <?php echo($comment->comment_date);?><br />
<?php

  if ( count($comments) > 0) {
    echo "</div>";
  }

  }
}
add_shortcode( 'dsslatestcomments', 'dss_latest_comments_func' );

function dss_remove_header_links() {
  remove_action( 'wp_head', 'feed_links', 2);
  remove_action( 'wp_head', 'feed_links_extra', 3);
  remove_action( 'wp_head', 'wp_generator' );
}

add_action( 'after_setup_theme', 'dss_remove_header_links' );

?>