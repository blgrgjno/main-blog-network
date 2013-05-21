<?php

$root = dirname( __FILE__ ) . '/../../..';

// Make sure we don't pay for another plugin's errors (Ahem, WP-Super-Cache...)
define( 'WP_CACHE', false );

require( $root . '/wp-blog-header.php');

if (file_exists( $root . '/wp-load.php' ))
	require_once( $root . '/wp-load.php' );  // WP 2.6+
else
	require_once( $root . '/wp-config.php' ); // Pre 2.6

if (isset($_GET['c'])) {
    $c = (int)$_GET['c'];
}

$valid_req = FALSE;

if (is_numeric($c)) {
	$valid_req = TRUE;

	// Get cookie to see if they've already reported this comment
	$reported_comments_list = $_COOKIE['rc_reported_comments_list'];

	$alreadyReported = FALSE;
	if ($reported_comments_list !== "")
	{
		if (in_array($c, split(",", $reported_comments_list)))
			$alreadyReported = TRUE;
	}

	if ($alreadyReported == FALSE)
		$cinfo = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}comments` WHERE `comment_ID` = $c");

}

if (count($cinfo) < 1) {
	$valid_req = FALSE;
}

if ($valid_req == TRUE)
{
	$reporter_ip_address = $_SERVER['REMOTE_ADDR'];
	$reporter_hostname = gethostbyaddr( $reporter_ip_address );
	$reporter_browser = $_SERVER['HTTP_USER_AGENT'];
	$reporter_referrer = $_SERVER['HTTP_REFERER'];

	$reporter_comment = 'No comment entered.';
	if (isset($_GET['r'])) {
	    $reporter_comment = $_GET['r'];
	}

	$enable_email = get_option('rc_enable_email');
	$addr = get_option('rc_email');
	$rc_cookie_days = get_option('rc_cookie_days');
	$http_host = str_replace("www.", ".", $_SERVER['HTTP_HOST']);

	$commentReported = FALSE;

	if ($enable_email == 1 && $addr !== "")
	{
		$subject = get_option('rc_email_subject');
		$msg = get_option('rc_email_msg');

		$post_url = get_permalink($cinfo->comment_post_ID);
		$post_title = get_the_title($cinfo->comment_post_ID);
		$post_title = htmlspecialchars(html_entity_decode($post_title, ENT_QUOTES, 'UTF-8'), ENT_NOQUOTES, 'UTF-8');

		$subject = str_replace('%COMMENT_ID%', $cinfo->comment_ID, $subject);
		$subject = str_replace('%COMMENT_URL%', $post_url . '#comment-' . $cinfo->comment_ID, $subject);
		$subject = str_replace('%POST_ID%', $cinfo->comment_post_ID, $subject);
		$subject = str_replace('%POST_URL%', $post_url, $subject);
		$subject = str_replace('%POST_TITLE%', $post_title, $subject);
		$subject = str_replace('%COMMENT_AUTHOR%', $cinfo->comment_author, $subject);
		$subject = str_replace('%COMMENT_DATE%', $cinfo->comment_date, $subject);
		$subject = str_replace('%REPORTER_IP_ADDRESS%', $reporter_ip_address, $subject);
		$subject = str_replace('%REPORTER_COMMENT%', $reporter_comment, $subject);
		$subject = str_replace('%REPORTER_HOSTNAME%', $reporter_hostname, $subject);
		$subject = str_replace('%REPORTER_REFERRER%', $reporter_referrer, $subject);
		$subject = str_replace('%REPORTER_BROWSER%', $reporter_browser, $subject);
		/* $subject = str_replace('%COMMENT_TEXT%', $cinfo->comment_content, $subject); */

		$msg = str_replace('%COMMENT_ID%', $cinfo->comment_ID, $msg);
		$msg = str_replace('%COMMENT_URL%', $post_url . '#comment-' . $cinfo->comment_ID, $msg);
		$msg = str_replace('%POST_ID%', $cinfo->comment_post_ID, $msg);
		$msg = str_replace('%POST_URL%', $post_url, $msg);
		$msg = str_replace('%POST_TITLE%', $post_title, $msg);
		$msg = str_replace('%COMMENT_AUTHOR%', $cinfo->comment_author, $msg);
		$msg = str_replace('%COMMENT_DATE%', $cinfo->comment_date, $msg);
		$msg = str_replace('%COMMENT_TEXT%', $cinfo->comment_content, $msg);
		$msg = str_replace('%REPORTER_IP_ADDRESS%', $reporter_ip_address, $msg);
		$msg = str_replace('%REPORTER_COMMENT%', $reporter_comment, $msg);
		$msg = str_replace('%REPORTER_HOSTNAME%', $reporter_hostname, $msg);
		$msg = str_replace('%REPORTER_REFERRER%', $reporter_referrer, $msg);
		$msg = str_replace('%REPORTER_BROWSER%', $reporter_browser, $msg);

		# Email the report
		$commentReported = mail($addr, $subject, $msg, 'From: ' . get_option('admin_email'));
	}

	# Add report to the admin moderation queue
	$added = rc_add_report($cinfo, $reporter_comment);

	if ($commentReported || $added)
	{
		if (is_array($reported_comments_list))
		{
			$reported_comments_list[] = $c;
			$reported_comments_list = implode(",", $reported_comments_list);
		}
		else
			$reported_comments_list = $c;
	
		# Set a cookie with the list of already reported comments
		setcookie('rc_reported_comments_list',$reported_comments_list,time() + (86400 * $rc_cookie_days), '/', "$http_host"); // 86400 = 1 day

		$response['message'] = get_option('rc_success');
	}
}
else {
	if ($alreadyReported == TRUE)
		$response['message'] = get_option('rc_already');
	else
		$response['message'] = get_option('rc_error');
}

//Don't want to require PHP 5.2 quite yet, even though everyone should be using it by now.
//echo json_encode( $response );
echo <<< EOT
{"message":"$response[message]","reported":"$commentReported"}
EOT;
	exit;

?>