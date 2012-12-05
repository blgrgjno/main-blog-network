<?php
// Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'dss-loaded'); ?></p>
	<?php
		return;
	}
?>

<!-- You can start editing here. -->
<?php if (have_comments() || "open" == $post->comment_status) { ?>
<div class="boxcomments">
	<h2 id="comments">
	  <?php comments_number(); ?>
		<?php _e('to', 'dss-loaded'); ?> &#8220; <?php the_title(); ?> &#8221;
	</h2>
<?php
if (have_comments()) { 	?>
		<ol class="commentlist">
		  <?php wp_list_comments(array('avatar_size'=>32, 'type'=>'comment')); 
						//		wp_list_comments('type=comment&callback=dssl_comment');
?>
		</ol>
<!-- removed pings for now		<h3 id="pings"><?php _e('Trackbacks &amp; Pingbacks:', 'dss-loaded') ?></h3>
		<ol class="tblist">
			<?php wp_list_comments('type=pings'); ?>
		</ol>
-->

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
 <?php } else { // this is displayed if there are no comments so far 

	if ('open' == $post->comment_status) { ?>
		<!-- If comments are open, but there are no comments. -->
		<!-- I don't see the point of this bit but have left it here just incase! -->

	 <?php } else { // comments are closed ?>
		<!-- If comments are closed. -->
		<p id="comments-closed"><?php _e('Sorry, comments for this entry are closed at this time.', 'dss-loaded') ?></p> 

	<?php }
}
?>

<?php if ('open' == $post->comment_status) { ?>

<div id="respond">
	
	<?php if (get_option('comment_registration') && !$user_ID ) { ?>
		<p id="comments-blocked"><?php _e('You must be', 'dss-loaded') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=
		<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in', 'dss-loaded') ?></a> <?php _e('to post a comment.', 'dss-loaded') ?></p>
	<?php } else { ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	<!-- <fieldset><legend><?php comment_form_title( 'Leave a Comment', 'Leave a Reply to %s' ); ?></legend> -->
	
	<p><?php cancel_comment_reply_link() ?></p>

	<?php if ($user_ID) { ?>
	
	<p><?php _e('You are logged in as', 'dss-loaded') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php">
		<?php echo $user_identity; ?></a>. <?php _e('To logout', 'dss-loaded') ?>, <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'dss-loaded') ?>"><?php _e('click here', 'dss-loaded') ?></a>.
	</p>

	<?php } else { ?>	

	 <table id="commentformtable">
						<tr><td><label for="author"><?php _e('Name', 'dss-loaded'); if ($req) echo "*"; ?></label></td>
		<td><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" /></td></tr>
				
		<tr><td><label for="email"><?php _e('Mail', 'dss-loaded'); if ($req)?></label></td>
		<td><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" /></td></tr>
		
		<tr><td><label for="url"><?php _e('Website', 'dss-loaded'); ?></label></td>
		<td><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" /></td></tr>
	
	<?php } ?>

		<tr><td><label for="comment"><?php _e('Your Comment', 'dss-loaded'); ?></label></td>
		<td><textarea name="comment" id="comment" cols="5" rows="10"></textarea></td></tr>
		
						<tr><td>&nbsp;</td>
		<td><input name="submit" type="submit" id="submit" value="<?php _e('Submit Comment', 'dss-loaded'); ?>" /> </tr></td>
		<?php comment_id_fields(); ?>
	</table>
	</fieldset>	

	<div><?php do_action('comment_form', $post->ID); ?></div>

	</form>
<?php
   } // If registration required and not logged in
?>
</div> <!-- end #respond -->
<?php
}
?>
</div><!-- end boxcomments -->
<?php } ?>