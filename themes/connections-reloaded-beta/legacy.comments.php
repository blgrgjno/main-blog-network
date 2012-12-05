<?php
global $options;
foreach ($options as $value) {
if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php // Do not delete these lines
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
        if (!empty($post->post_password)) 
		{ // if there's a password
            if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) 
			{		// and it doesn't match the cookie
?>
			<p class="nocomments">
				<?php _e("This post is password protected. Enter the password to view comments."); ?>
			</p>
<?php
				return;
            }
        }
		/* This variable is for alternating comment background */
		$oddcomment = 'alt';
?>

<!-- You can start editing here. -->
<hr />
<!-- Comments start here -->
<?php if (is_array($comments)) { ?>
	<?php if ( !empty($post->post_password) && $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) { ?>
		<p>
			<?php _e('Enter your password to view comments.'); ?>
		</p>
	<?php return; } ?>
	<h2 id="comments">
		<?php comments_number('No Responses', 'One Response', '% Responses' );?>
		<?php _e('to'); ?> &#8220; <?php the_title(); ?> &#8221;
	</h2>
	
	<!-- Begin Comments -->
	<?php foreach ($comments as $comment) { ?>
	<?php if ($comment->comment_type != "trackback" && $comment->comment_type != "pingback" && !ereg("<pingback />", $comment->comment_content) && !ereg("<trackback />", $comment->comment_content)) { ?>
		<?php if (!$runcommentonce) { $runcommentonce = true; ?>
			<h3>
				<?php _e('Comments:') ?>
			</h3>
			<ol class="commentlist">
		<?php } ?>
				<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
					<div class="gravatar">
						<?php 
						if (function_exists('get_avatar')) {
							echo get_avatar($comment, 55);
						} else {
							//alternate gravatar code for < 2.5
							$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5($comment) . "&default=" . urlencode($default) . "&size=" . 55; 
							echo "<img src=\"$grav_url\" />";
						}
						?>
					</div>
					<cite>
						<?php comment_author_link() ?>
					</cite> <?php _e('says:') ?>
				    <?php if ($comment->comment_approved == '0') { ?>
						<em><?php _e('Your comment is awaiting moderation. Please do not submit it again.') ?></em>
				    <?php } ?>
				    <br />
				    <small class="commentmetadata">
						<a href="#comment-<?php comment_ID() ?>" title="">
						    <?php comment_date('F jS, Y') ?>
						    <?php _e(' at ') ?>
						    <?php comment_time() ?>
					    </a>
					    <?php edit_comment_link('e','',''); ?>
				    </small>
				    <?php comment_text() ?>
				</li>
				<?php /* Changes every other comment to a different class */	
					if ('alt' == $oddcomment) $oddcomment = '';
					else $oddcomment = 'alt';
				?>
	<?php } ?>
	<?php } /* end for each comment */ ?>
	<?php if ($runcommentonce) { ?></ol><?php } ?>
	<!-- End Comments -->
	<br />
<?php } else { // this is displayed if there are no comments so far ?>
	<?php if ('open' == $post-> comment_status) { ?>
		<!-- If comments are open, but there are no comments. -->
		<p class="nocomments"><?php _e('There are no comments yet. Be the first to post') ?></p>
	<?php } else { // comments are closed ?>
		<!-- If comments are closed. -->
		<p class="nocomments"><?php _e('Comments are closed.') ?></p>
	<?php } ?>
<?php } ?>

<?php if ('open' == $post-> comment_status) { ?>
	<h3 id="respond"><?php _e('Leave a Reply') ?></h3>
	<?php if ( get_option('comment_registration') && !$user_ID ) { ?>
	<p><?php _e('You must be ') ?><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>"><?php _e('logged in') ?></a> <?php _e('to post a comment.') ?></p>
	<?php } else { ?>
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		<?php if ( $user_ID ) { ?>
			<p><?php _e('Logged in as ') ?><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>">Logout &raquo;</a></p>
		<?php } else { ?>
			<p>
				<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
				<label for="author"><small><?php _e('Name');
					if ($req) _e('(required)'); ?>
				</small></label>
			</p>
			<p>
				<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
				<label for="email"><small><?php _e('Mail (will not be published)');
					if ($req) _e('(required)'); ?>
				</small></label>
			</p>
			<p>
				<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
				<label for="url"><small><?php _e('Website'); ?></small></label>
			</p>
		<?php } ?>
			<p><small><?php _e('<strong>XHTML:</strong> You can use these tags: '); echo allowed_tags(); ?></small></p>
			<p>
				<textarea name="comment" id="comment" cols="90%" rows="10" tabindex="4"></textarea>
			</p>
			<p>
				<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment'); ?>" />
				<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
			</p>
			<?php do_action('comment_form', $post->ID); ?>
		</form>
	<?php } // If registration required and not logged in ?>
<?php } // if you delete this the sky will fall on your head ?>
<br />
<?php if (is_array($comments)) { ?>
	<!-- Begin Trackbacks -->
	<?php foreach ($comments as $comment) { ?>
		<?php if (($comment->comment_type == "trackback") || ($comment->comment_type == "pingback") || ereg("<pingback />", $comment->comment_content) || ereg("<trackback />", $comment->comment_content)) { ?>
			<?php if (!$runonce) { $runonce = true; ?>
			<h3 id="trackbacks">
				<?php _e('Trackbacks &amp; Pingbacks:') ?>
			</h3>
			<ol class="commentlist">
			<?php } ?>
			<li class="<?php echo $oddcomment; ?>" id="comment-<?php comment_ID() ?>">
				<?php if (($comment->comment_type == "trackback") || ereg("<trackback />", $comment->comment_content))
					_e('<strong>Trackback from </strong>'); 
				elseif (($comment->comment_type == "pingback") || ereg("<pingback />", $comment->comment_content))
					_e('<strong>Pingback from </strong>'); 
				?>
				<?php comment_author_link() ?>
			    <?php if ($comment->comment_approved == '0') { ?>
					<em><?php _e('Your comment is awaiting moderation.') ?></em>
			    <?php } ?>
				<br />
				<small class="commentmetadata">
					<a href="#comment-<?php comment_ID() ?>" title="">
						<?php comment_date('F jS, Y') ?>
						<?php _e('at') ?>
						<?php comment_time() ?>
					</a>
				<?php edit_comment_link('e','',''); ?>
				</small>
				<?php comment_text() ?>
			</li>
		<?php } ?>
		<?php /* Changes every other comment to a different class */	
			if ('alt' == $oddcomment) $oddcomment = '';
			else $oddcomment = 'alt';
		?>
	<?php } ?>
	<?php if ($runonce) { ?>
		</ol>
	<?php } ?>
	<!-- End Trackbacks -->
<?php } ?>
