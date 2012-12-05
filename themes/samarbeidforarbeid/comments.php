<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */

 

if ( post_password_required() ) : ?>
<p><?php _e('Enter your password to view comments.'); ?></p>
<?php return; endif; ?>


<?php if ( comments_open() ) : ?>


<h2 id="comments">
  Kommentarer <?php comments_number(__(''), __('(1)'), __('(%)')); ?>
</h2>
<?php if ( comments_open() ) : ?>
	<a id="comment-anchor" href="#postcomment" title="Skriv en kommentar">Skriv en kommentar</a>
<?php endif; ?>

<?php if ( have_comments() ) : ?>
<ol id="commentlist">


<?php foreach ($comments as $comment) : ?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
	<?php if ($comment->comment_approved == '0') : ?>
		<em><?php _e('Your comment is awaiting moderation.') ?></em>
		<br />
  <?php endif; ?>
	<cite><?php comment_type(_x('Comment', 'noun'), __('Trackback'), __('Pingback')); ?> <?php _e('by'); ?> <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite> <?php edit_comment_link(__("Edit This"), ' |'); ?>
  <?php 
    echo get_avatar( $comment->comment_author_email, $size = '48'); 
   ?>	
	<?php comment_text() ?>	
	<hr/>
	</li>

<?php endforeach; ?>

</ol>

<?php else : // If there are no comments yet ?>
	<p><?php _e('No comments yet.'); ?></p>
<?php endif; ?>

<p id="comment-tools"><?php post_comments_feed_link(__('<abbr title="Really Simple Syndication">RSS</abbr> feed for comments on this post.')); ?>
<?php if ( pings_open() ) : ?>
	<a href="<?php trackback_url() ?>" rel="trackback"><?php _e('TrackBack <abbr title="Universal Resource Locator">URL</abbr>'); ?></a>
<?php endif; ?>
</p>




<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>
<p><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url( get_permalink() ) );?></p>
<?php else : ?>


<div class="webforms mod shadow">
  <h2 id="postcomment"><span><?php _e('Leave a comment'); ?></span></h2>
  <form class="webform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
  
  <p class="info">Kommentarer du legger inn blir først synlig for andre brukere etter at de har blitt godkjent av moderator.</p>
  
  
  <?php if ( is_user_logged_in() ) : ?>  
  
    <p id="logged-in" class="info"><?php printf(__('Logged in as %s.'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account') ?>"><?php _e('Log out &raquo;'); ?></a></p>  
  
  <?php else : ?>
    <div class="line">
      <div class="unit size1of4">
        <label class="required" for="author"><?php _e('Name'); ?> (påkrevd)</label>
      </div>
      <div class="unit size3of4 lastUnit">
        <input class="required" type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22"/>
      </div>
    </div>
    <div class="line">
      <div class="unit size1of4">
        <label class="required" for="email"><?php _e('Epost');?> (påkrevd)</label>
      </div>
      <div class="unit size3of4 lastUnit">
        <input class="required email" type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" />
      </div>
    </div>
    <div class="line">
      <div class="unit size1of4">
        <label for="url"><?php _e('Website'); ?></label>
      </div>
      <div class="unit size3of4 lastUnit">
        <input type="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22"/>
      </div>
    </div>  
  <?php endif; ?>
  
  <!--<p><small><strong>XHTML:</strong> <?php printf(__('You can use these tags: %s'), allowed_tags()); ?></small></p>-->
  
  <div class="line">
    <div class="unit size1of4">
      <label for="comment" class="required">Kommentar (påkrevd)</label>
    </div>
    <div class="unit size3of4 lastUnit">
      <textarea class="required" name="comment" id="comment" cols="58" rows="15"></textarea>
    </div>
  </div>  
  

  
  <p>
    <button class="btn btn-green goto" name="submit" type="submit" id="submit"><span><?php esc_attr_e('Submit Comment'); ?></span></button>
    <input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
  </p>
  <?php do_action('comment_form', $post->ID); ?>
  
  </form>
</div>

<?php endif; // If registration required and not logged in ?>

<?php else : // Comments are closed ?>




<?php endif; ?>

