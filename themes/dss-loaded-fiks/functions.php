<?php

add_action( 'wp_print_styles', 'fiks_deregister_styles', 100 );
function fiks_deregister_styles() {
	wp_deregister_style( 'metroshare-css' );
}



// Custom Header
define('HEADER_TEXTCOLOR', '003a80');
define('HEADER_IMAGE_WIDTH', 741);
define('HEADER_IMAGE_HEIGHT', 142);
define('HEADER_IMAGE', get_bloginfo('stylesheet_directory') . '/img/header_default.jpg');
define('NO_HEADER_TEXT', true );

/*function remove_header_support()
{
	remove_action('wp_head', 'header_style');
}

add_action( 'init', 'remove_header_support', 9 );
*/

function dssl_header_style() {
?>
<style type="text/css">
#headimg{
	background:#fff url(<?php header_image() ?>) no-repeat bottom;
}
<?php if ( 'blank' == get_header_textcolor() ) { ?>
#headimg h1, #headimg #desc {
	display: none;
}
<?php } ?>
</style>
<?php
}

function dssl_admin_header_style() {
?>
<style type="text/css">
#headimg{
	background:#fff url(<?php header_image() ?>) no-repeat bottom;
	height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
	width:<?php echo HEADER_IMAGE_WIDTH; ?>px;
	padding:0;
}

#headimg h1{
	margin: 0;	
	font-size: 1.6em;	
	padding:10px 20px 0 0;
	text-align:right;	
}
#headimg h1 a{
	color:#<?php header_textcolor() ?>;
	text-decoration:none;
}
#headimg #desc{
	color:#<?php header_textcolor() ?>;
	font-weight:normal;
	font-style:italic;
	font-size:1em;
	text-align:right;
	margin:0;
	padding:0 20px 0 0;
}

<?php if ( 'blank' == get_header_textcolor() ) { ?>
#headimg h1, #headimg #desc {
	display: none;
	text-decoration:none;
}
#headimg h1 a {
	color:#<?php echo HEADER_TEXTCOLOR ?>;
}
#desc {
	color: #2d2d2c;
}

<?php } ?>

</style>
<?php
}

/*function dssl_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment-author vcard">
		<?php //echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>

   <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
   <?php //printf(__('<cite class="fn">%s</cite> class="says">says:</span>'), get_comment_author_link()) 
   printf(__('<cite class="fn">%s</cite> '), get_comment_author_link()); 
   ?> </a>
   <?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time());
   edit_comment_link(__('(Edit)'),'  ','');

   echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
?>

      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />
      <?php endif; ?>

      <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>

      <?php comment_text() ?>

      <div class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </div>
     </div>
<?php
        }
		*/



add_custom_image_header('dssl_header_style', 'dssl_admin_header_style');
load_textdomain('dss-loaded', dirname(__FILE__).'/languages/' . get_locale() . '.mo');

?>