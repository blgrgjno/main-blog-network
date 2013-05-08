<?php

# Counts number of reports for all comments
function rc_comment_report_counts($comment_ID=0) {

    global $wpdb, $_rc_comments_reported_db;

	 $comment_clause = '1';
	 if ($comment_ID > 0)
		$comment_clause = '`comment_ID` = '.$comment_ID;

    $sql = "SELECT `comment_ID`, count(`comment_ID`) as `num_reports` FROM `".$_rc_comments_reported_db."` WHERE $comment_clause GROUP BY `comment_ID`";
    $result = $wpdb->get_results($sql);

    $reports = array();

    foreach ($result as $report)
    {
        $reports[$report->comment_ID] = $report->num_reports;
    }

    return $reports;
}

# Generates results for list of reported comments
function rc_get_comments_list($show)
{
	global $wpdb,$_rc_comments_reported_db,$_rc_comments_moderated_db;
	
	$show_clause = '';
	
	if ($show == "pending")
		$show_clause = " AND m.`status` = 0";
	elseif ($show == "approved")
		$show_clause = " AND m.`status` = 1";
 
	$sql = "SELECT 
				c.`comment_ID`,
				c.`comment_post_ID`,
				c.`comment_author`,
				c.`comment_author_email`,
				c.`comment_author_url`,
				c.`comment_author_IP`,
				c.`comment_date`,
				c.`comment_content`,
				r.`reporter_ip`,
				r.`reporter_date`,
				r.`reporter_comment`,
				m.`status`
			FROM
				`".$wpdb->prefix."comments` c,
				`".$_rc_comments_reported_db."` r,
				`".$_rc_comments_moderated_db."` m
			WHERE
				r.`comment_ID` = c.`comment_ID`
			AND
				m.`comment_ID` = c.`comment_ID`
				$show_clause
			AND
 				c.`comment_approved` != 'trash'			
			GROUP BY
				c.`comment_ID`
			";
	
	return $wpdb->get_results($sql);
}

# Gets a list of reports for a specific comment ID
function rc_get_reports_list($comment_ID=0)
{
	global $wpdb, $_rc_comments_reported_db;
	
	if ($comment_ID > 0)
	{
		$sql = "SELECT * FROM `".$_rc_comments_reported_db."` WHERE `comment_ID` = $comment_ID";
		return $wpdb->get_results($sql);
	}
}

# Gets all data for a specific comment ID
function rc_get_comment_data($comment_ID=0)
{
	if ($comment_ID == 0)
		return false;
		
	global $wpdb;
	
	$sql = "SELECT * FROM `".$wpdb->prefix."comments` WHERE `comment_ID` = $comment_ID LIMIT 1";
	return $wpdb->get_results($sql);
}

# Gets the moderated status for a comment
function rc_get_comment_status($comment_ID=0)
{
	if ($comment_ID == 0)
		return false;
		
   global $wpdb, $_rc_comments_moderated_db;

	$sql = "SELECT `status` FROM ".$_rc_comments_moderated_db." WHERE `comment_ID` = ".$comment_ID;
	$result = $wpdb->get_results($sql);

	return $result[0]->status;
}

# Changes the status of a reported comment
function rc_change_comment_status($comment_ID, $status=1) 
{
   global $wpdb, $_rc_comments_moderated_db;

   $sql = "UPDATE `".$_rc_comments_moderated_db."` SET `status` = ".mysql_real_escape_string($status)." WHERE `comment_ID` = $comment_ID";
   return $wpdb->query($sql);
}

# Add a comment report
function rc_add_report($cinfo, $reporter_comment)
{
   global $wpdb, $_rc_comments_moderated_db, $_rc_comments_reported_db;

	$sql = "SELECT count(*) as `count` FROM `".$_rc_comments_moderated_db."` WHERE `comment_ID` = ".$cinfo->comment_ID;
	$result = $wpdb->get_results($sql);
	
	if ($result[0]->count < 1)
	{
		$sql = "INSERT INTO `".$_rc_comments_moderated_db."` VALUES ('', '".$cinfo->comment_ID."', '0')";
		$result = $wpdb->query($sql);
	}

	$sql = "INSERT INTO `".$_rc_comments_reported_db."` VALUES ('', '".$cinfo->comment_ID."', '".$_SERVER['REMOTE_ADDR']."', now(), '".mysql_real_escape_string(urldecode($reporter_comment))."')";
	$result = $wpdb->query($sql);
	
	return $result;
}

# Delete a comment and all associated reports
function rc_delete_comment($comment_ID)
{
	$deleted1 = wp_delete_comment( $comment_ID ); 
	$deleted2 = rc_delete_report('all', $comment_ID);
	
	if ($deleted1 && $deleted2)
		return true;
}

# Delete a comment report
function rc_delete_report($report_ID, $comment_ID) {

   global $wpdb, $_rc_comments_reported_db, $_rc_comments_moderated_db;
	
	if ($report_ID == 'all')
   	$sql = "DELETE FROM `".$_rc_comments_reported_db."` WHERE `comment_ID` = $comment_ID";
	else
   	$sql = "DELETE FROM `".$_rc_comments_reported_db."` WHERE `ID` = $report_ID";

	$wpdb->query($sql);

   $sql = "SELECT count(*) as `count` FROM `".$_rc_comments_reported_db."` WHERE `comment_ID` = $comment_ID";
	$result = $wpdb->get_results($sql);

	if ($result[0]->count < 1)
	{
	   $sql = "DELETE FROM `".$_rc_comments_moderated_db."` WHERE `comment_ID` = $comment_ID";
		$wpdb->query($sql);
	}	

	return true;
}

# Get count comments
function rc_comments_count($type='all') {

	global $wpdb, $_rc_comments_reported_db, $_rc_comments_moderated_db;

	$clause = '';

	if ($type == "approved")
		$clause = ' AND m.`status` = 1';
	elseif ($type == "pending")
		$clause = ' AND m.`status` = 0';

 	$sql = "SELECT count(r.`comment_ID`) as count FROM `".$_rc_comments_reported_db."` r, `".$_rc_comments_moderated_db."` m, `".$wpdb->prefix."comments` c WHERE r.`comment_ID` = m.`comment_ID` AND r.`comment_ID` = c.`comment_ID` $clause AND c.`comment_approved` != 'trash'";
	$result = $wpdb->get_results($sql);
	return $result[0]->count;
}
?>
