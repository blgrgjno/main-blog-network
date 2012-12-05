<?php
### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

### Use WordPress 2.6 Constants
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}

### Report Post Table Name
global $wpdb;

$wpdb->reportpost = $wpdb->prefix.'reportpost';

$wprp_message="";

// Handle the POST

/* This function to be Called When a Report Rquest Received
----------------------------------------*/
function wprp_handle_reports()
{
	global $wpdb, $wprp_message;
	
	// get Post PARAM
	$post_id=(int)$_POST['postID'];
	$report_as=htmlentities(strip_tags($_POST['report_as']), ENT_QUOTES);
	$description=htmlentities(strip_tags($_POST['description']), ENT_QUOTES);
	$ipaddress=get_ipaddress();
	$nonce=$_POST['wpnonce'];

	// Get the Post
	$post=get_post($post_id);
	// Check for POST
	if(!$post_id || !$post)
	{
		echo "<strong>Ugyldig innlegg</strong>";
		return;
	}
	// Security CHECK
	if (!wp_verify_nonce($nonce, $post_id) )
	{
		echo "<strong>Sikkerhetssjekk feilet. Vennligst forsøk å sende igjen.</strong>";
		return;
	}
	
	include_once('ReportPost.class.php');
	
	$rp = new ReportPost($wpdb);
	
	if($rp->add($post_id, $report_as, $description ))
	{
		$reported=true;
	}else{
		echo "Beklager, vi greide ikke utføre forespørslen din. Vennligst rapporter dette til webredaktør per e-post.";
	}
	
	/*
	// tpValirable
	$reported=false;
	
	// Check for Existing Post Report
	$post_count=$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->reportpost WHERE post_id=%s",$post_id));
	
	if(is_numeric($post_count) && $post_count>0)
	{
		// Update the Description
		$result=$wpdb->query( $wpdb->prepare("UPDATE $wpdb->reportpost SET description=CONCAT(description,%s) WHERE post_id=%s"," <br />[".$ipaddress."] : ".$report_as." | ".$description,$post_id));
		
		$reported=true;
	}else{
		// Do Report!
		$result=$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->reportpost(post_id,post_title,user_ip,description,stamp) VALUES(%s,%s,%s,%s,%s)",$post_id, $post->post_title, $ipaddress,"[".$ipaddress."] : ".$report_as." | ".$description,time()));
		$reported=true;
		
		// Send Mail
		$send_email=get_option("rp_send_email");
		if($send_email==1)
		{
			// SEND EMAIL
			$mail_to=get_option("rp_email_address");
			$mail_subject="[REPORT] : ".$post->post_title;
			$mail_body="Following Post has been Reported through ".get_option("blogname")."\n-----\n";
			$mail_body.="POST ID: ".$post_id."\n";
			$mail_body.="POST TITLE: ".$post->post_title."\n";
			$mail_body.="Reported As: ".$report_as."\n";
			$mail_body.="Description: \n".$description."\n";
			$mail_body.="\n-----\nThank You";
			
			$mail_header="From: Admin <".get_option("admin_email").">";
			
			// Send mail // @ Prvent from Showing Any Error Message JUST in CASE
			@mail($mail_to,$mail_subject,$mail_body,$mail_header);
		}
		
	}*/
	
	if($reported)
	{
		// get thanks Option
		$thanksMsg=get_option('rp_thanks_msg');
		if(empty($thanksMsg))
			$thanksMsg="<strong>Takk for at du varslet om [post_title]</strong>";
		
		$thanksMsg=str_replace("[post_title]",$post->post_title,$thanksMsg);
		echo $thanksMsg;
		echo "<br />Varslet som: ".$report_as;
		if(!empty($description))
			echo "<br />Beskrivelse: ".$description;

	}
}


### Function: Get IP Address
if(!function_exists('get_ipaddress')) {
	function get_ipaddress() {
		if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if(strpos($ip_address, ',') !== false) {
			$ip_address = explode(',', $ip_address);
			$ip_address = $ip_address[0];
		}
		return $ip_address;
	}
}


// Determin How to Call POST
if(isset($_POST['do_ajax_report']))
{
	wprp_handle_reports();
}
?>