<?php 

$statement_meta = get_statement_meta($post->ID);

// Custom callback to list comments in the Thematic style
function nhop_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;

		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		
		// Get user type
		$user_type = get_user_meta($comment->user_id, "user_type", true);
?>
		<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
		<?php endif; ?>
		<div class="comment-author vcard author_<?php echo $user_type; ?>">
		<?php printf('<cite class="fn">%s</cite>', get_comment_author_link()) ?>
		</div>
<?php if ($comment->comment_approved == '0') : ?>
		<em><?php _e('Your comment is awaiting moderation.') ?></em>
		<br />
<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __('%1$s %2$s'), get_comment_date(),  get_comment_time()) ?></a>
				<?php edit_comment_link(__('(Edit)'),'&nbsp;&nbsp;','' );
			?>
		</div>

		<div class="comment-content">
			<?php comment_text() ?>
		</div>

		<div class="reply">
			<?php
			if (get_theme_option('enable_statements')) {
				comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'], 'reply_text' => '<span>Svar på kommentar</span>', 'login_text' => '<span>Logg inn for å svare</span>')));
			}
			?>
		</div>
		<span id="reportcomment_results_div_<?php echo $comment->comment_ID; ?>"><a rel="nofollow" title="Report this comment" onclick="reportComment_AddTextArea(<?php echo $comment->comment_ID; ?>);" href="javascript:void(0);"><span class="buttonRoundLightSlim"><span>Varsle</span></span></a></span>
		
		<div id="reportcomment_comment_div_<?php echo $comment->comment_ID; ?>" class="reportComment_comment"></div>
		
		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
<?php
}
?>




<?php thematic_abovecomments() ?>

			<div id="comments">
<?php
	$req = get_option('require_name_email'); // Checks if fields are required.
	if ( 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die ( 'Please do not load this page directly. Thanks!' );
	if ( ! empty($post->post_password) ) :
		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ) :
?>
				<div class="nopassword"><?php _e('Dette innlegget er passordbeskyttet. Skriv inn passordet ditt for &aring; lese kommentarer.', 'thematic') ?></div>
			</div><!-- .comments -->
<?php
		return;
	endif;
endif;
?>



<?php if ( 'open' == $post->comment_status && get_theme_option('enable_statements') ) : ?>
				<div id="respond">
    				<h3 class="buttonRoundSlim"><span><?php comment_form_title( __('Legg igjen en kommentar', 'thematic'), __('Skriv et svar til %s', 'thematic') ); ?></span></h3>
					<?php 
						if (!is_page()) {
							// Report post
							wprp(true);
						}
					?>
    				
    				<div id="cancel-comment-reply"><?php cancel_comment_reply_link() ?></div>

<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
					<p id="login-req"><?php printf(__('Du m&aring; v&aelig;re <a href="%s" title="Log in">logget inn</a> for &aring; legge igjen en kommentar.', 'thematic'),
					get_option('siteurl') . '/wp-login.php?redirect_to=' . $statement_meta->statement_url ) ?></p>

<?php else : ?>
					<div class="formcontainer">	
					
<?php thematic_abovecommentsform() ?>					

						<form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">

<?php if ( $user_ID ) : ?>
							<p id="login"><?php printf(__('<span class="loggedin">Logget inn som <a href="%1$s" title="Logget inn som %2$s">%2$s</a>.</span> <span class="logout"><a href="%3$s" title="Logg ut">Logg ut?</a></span>', 'thematic'),
								get_option('siteurl') . '/wp-admin/profile.php',
								wp_specialchars($user_identity, true),
								wp_logout_url($statement_meta->statement_url) ) ?></p>

<?php else : ?>

							<p id="comment-notes"><?php _e('Din e-postadresse vil <em>aldri</em> publiseres/deles.', 'thematic') ?> <?php if ($req) _e('P&aring;krevde felter er markert <span class="required">*</span>', 'thematic') ?></p>

                            <div id="form-section-author" class="form-section">
    							<div class="form-label"><label for="author"><?php _e('Navn', 'thematic') ?></label> <?php if ($req) _e('<span class="required">*</span>', 'thematic') ?></div>
    							<div class="form-input"><input id="author" name="author" type="text" value="<?php echo $comment_author ?>" size="30" maxlength="20" tabindex="3" /></div>
                            </div><!-- #form-section-author .form-section -->

                            <div id="form-section-email" class="form-section">
    							<div class="form-label"><label for="email"><?php _e('E-post', 'thematic') ?></label> <?php if ($req) _e('<span class="required">*</span>', 'thematic') ?></div>
    							<div class="form-input"><input id="email" name="email" type="text" value="<?php echo $comment_author_email ?>" size="30" maxlength="50" tabindex="4" /></div>
                            </div><!-- #form-section-email .form-section -->

                            <div id="form-section-url" class="form-section">
    							<div class="form-label"><label for="url"><?php _e('Hjemmeside', 'thematic') ?></label></div>
    							<div class="form-input"><input id="url" name="url" type="text" value="<?php echo $comment_author_url ?>" size="30" maxlength="50" tabindex="5" /></div>
                            </div><!-- #form-section-url .form-section -->

<?php endif /* if ( $user_ID ) */ ?>

                            <div id="form-section-comment" class="form-section">
								<div class="form-label"><label for="comment"><?php _e('Kommentar', 'thematic') ?><span class="required"> *</span></label></div>
    							<div class="form-textarea"><textarea id="comment" name="comment" cols="45" rows="8" tabindex="6"></textarea></div>
                            </div><!-- #form-section-comment .form-section -->
                            
                  <?php do_action('comment_form', $post->ID); ?>
                  
							<div class="form-submit">
								<input type="submit" id="submit" name="submit" value="Send inn kommentar" tabindex="7" />
								<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
							</div>

                            <?php comment_id_fields(); ?>
							<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" />

						</form><!-- #commentform -->
						
<?php thematic_belowcommentsform() ?>											
						
					</div><!-- .formcontainer -->
<?php endif /* if ( get_option('comment_registration') && !$user_ID ) */ ?>

				</div><!-- #respond -->
<?php endif /* if ( 'open' == $post->comment_status ) */ ?>





<?php if ( have_comments() ) : ?>

<?php /* numbers of pings and comments */
$ping_count = 0;
foreach ( $comments as $comment ) {
	if (get_comment_type() != "comment") {
		$ping_count++;
	}
}
	
$comment_count = get_comments_number() - $ping_count;
?>

<?php if ( ! empty($comments_by_type['comment']) ) : ?>

<?php thematic_abovecommentslist() ?>

				<div id="comments-list" class="comments">
					<h3><?php printf($comment_count > 1 ? __('<span>%d</span> kommentarer', 'thematic') : __('<span>1</span> kommentar', 'thematic'), $comment_count) ?></h3>
					<ol>
						<?php wp_list_comments('type=comment&callback=nhop_comments'); ?>
					</ol>

        			<div id="comments-nav-below" class="comment-navigation">
        			     <div class="paginated-comments-links"><?php paginate_comments_links(); ?></div>
                    </div>
					
				</div><!-- #comments-list .comments -->

<?php thematic_belowcommentslist() ?>			

<?php endif; /* if ( $comment_count ) */ ?>


<?php endif /* if ( $comments ) */ ?>



			</div><!-- #comments -->
			
<?php thematic_belowcomments() ?>