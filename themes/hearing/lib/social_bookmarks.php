<?php

function hearing_addthumbnail() {
?>
	<meta property="og:image" content="<?php bloginfo('stylesheet_directory'); ?>/img/thumbnail.jpg" /> 
<?php
}
add_action('wp_head', 'hearing_addthumbnail');

function get_social_bookmarks($showSocial=true, $showKudos=false) {
  return; # TODO: disbled. Add config parameter
	global $post;
	$posttitle = get_bloginfo('name').' - '.get_the_title($post->ID);
	if ($post->post_type == "post") {
		$statement_meta = get_statement_meta($post->ID);
		$share_url = $statement_meta->statement_url;
	}
	else {
		$share_url = get_permalink($post->ID);
	}
	
	$twitter_url = $share_url;
	if (function_exists('fts_shorturl')) {
		$twitter_url = fts_shorturl($share_url, 'tinyurl', false);
	}
	
	$twitterstatus = $posttitle." ".$twitter_url;
	if (strlen($twitterstatus) > 140) {
		$lenleft = 140 - strlen($twitter_url);
		$twitterstatus = substr($posttitle, 0, $lenleft-4)."... ".$twitter_url;
	}
	
	$outstr = '';
	
	if ($showSocial) {
		$outstr .= '<span class="socialBookmarks">';
		$outstr .= '	<a href="#print" onclick="window.print();return false;" title="Skriv ut" class="share_print">Skriv ut</a>';
		$outstr .= '	<a href="http://www.facebook.com/sharer.php?u=' . $share_url . '&amp;t=' . $posttitle . '" title="Del på Facebook" class="share_facebook" target="_blank">Del på Facebook</a>';
		$outstr .= '	<a href="http://twitter.com/home?status=' . $twitterstatus . '" title="Del på Twitter" class="share_twitter" target="_blank">Del på Twitter</a>';
		$outstr .= '	<a href="http://origo.no/-/sharing/share?u=' . $share_url . '" title="Del på Origo" class="share_origo" target="_blank">Del på Origo</a>';
		$outstr .= '	<a href="' . get_post_comments_feed_link($post->ID) . '" title="RSS-strøm for kommentarer på ' . $post->post_title . '"class="share_rss" target="_blank">RSS-strøm for kommentarer på ' . $post->post_title . '</a>';
		$outstr .= '</span>';
	}
	if ($showKudos) {
		$outstr .= '<div class="kudosButtons">';
		
		$outstr .= '<iframe src="https://www.facebook.com/plugins/like.php?locale=nb_NO&amp;app_id=205125446216064&amp;href=' . $share_url . '&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none;overflow:hidden;width:100px;height:21px;" allowTransparency="true"></iframe>';
		
		$outstr .= "<a class='twitter-share-button' data-count='horizontal' data-text='" . $posttitle . "' data-url='" . $share_url . "' data-via='googleos' href='http://twitter.com/share'></a>
					<script type='text/javascript'>
						jQuery(document).ready(function () {
							jQuery.getScript('https://platform.twitter.com/widgets.js');
						});
					</script>";
		
		$outstr .= "<g:plusone count='true' href='" . $share_url . "' size='medium'></g:plusone>
					<script type='text/javascript'>
						jQuery(document).ready(function () {
							jQuery.getScript('https://apis.google.com/js/plusone.js');
						});
					</script>";
		
		$outstr .= '</div>';
	}
	return $outstr;
}

?>