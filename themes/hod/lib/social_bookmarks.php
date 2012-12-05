<?php

function social_bookmarks() {
	global $post;
	$posttitle = 'Fremtidens helsetjeneste: '.get_the_title($post->ID);
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
	
?>
	<span class="socialBookmarks">
		<a href="javascript:window.print();" title="Skriv ut" class="share_print">Skriv ut</a>
		<a href="http://www.facebook.com/sharer.php?u=<?php echo $share_url; ?>&amp;t=<?php echo $posttitle; ?>" title="Del på Facebook" class="share_facebook" target="_blank">Del siden på Facebook</a>
		<a href="http://twitter.com/home?status=<?php echo $twitterstatus; ?>" title="Del på Twitter" class="share_twitter" target="_blank">Del siden på Twitter</a>
	</span>
<?php
}

?>