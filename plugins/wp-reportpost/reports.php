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

/* Variables */
$type = $_GET['type'];
$id = $_GET['id'];

/* Load Reports Class*/
include_once('ReportPost.class.php');

global $wpdb;

$wprp = new ReportPost($wpdb);

$report = $wprp->findReports('ORDER BY id DESC',1,"WHERE id=".$id);

if(count($report) <= 0)
	die('Error! Unable to Load Details! Please contact Developper at <a href="http://rjeevan.com" target="_blank">http://rjeevan.com</a>');

$report= $report[0];
$permalink = get_bloginfo('wpurl')."/wp-admin/post.php?action=edit&post=".$report->postID;

//print_r($report);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Report Details</title>
<style type="text/css">
	html, body{
		background:#f8f8f8;
		font-family:Verdana, Geneva, sans-serif;
		color:#333;
		font-size:0.9em;
	}
	td{
		background-color:#FFF;
		border-bottom:1px dashed #ccc;
	}
	.border td{
		border-bottom:1px dashed #ccc;
		background-color:#f8f8f8;
	}
	.border .alt td{
		background-color:#fff;
	}
	th{
		text-align:left;
		color:#666;
		font-size:90%;
		border-bottom:1px dashed #ccc;
	}
	.archive-comment{
		display:block;
		padding:10px;
		border-bottom:1px dashed #ccc;
		border-top:1px dashed #ccc;
		background-color:#fff;
	}
	.archive-meta{
		font-size:90%;
	}
	
</style>
</head>
<body>
<h3>Report details</h3>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
	  <tr>
	    <th width="25%">Post / Page: </td>
	    <td><a href="<?php echo $permalink;?>" title="Edit The Post" target="_top"><?php echo $report->post_title;?></a></td>
  </tr>
	<tr>
	    <th>first reported on:</td>
	    <td><?php echo date("dS F, Y",$report->stamp);?></td>
  </tr>
	  <tr>
	    <th>Status</td>
	    <td><?php echo ($report->status == 1)? "New" : "Archived";?></td>
  </tr>
</table>
<h3>Reports:</h3>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="border">
	<tr>
    	<th width="15%">IP</td>
        <th width="30%">Type</td>
        <th>Comments</td>
    </tr>
<?php
$comments = $wprp->getComments($report->id);
$alt ='';
foreach($comments as $comment):
$alt = ($alt=='')? 'class="alt"' : '';
?>
    <tr <?php echo $alt;?>>
    	<td><?php echo $comment->ip;?></td>
        <td><?php echo $comment->type;?></td>
        <td><?php echo nl2br($comment->comment);?></td>
    </tr>
<?php endforeach;?>
</table>

<?php if($_GET['display'] =='archive') : ?>
<h3>Archived By</h3>
<?php
$archive = $wprp->getArchive($report->id);
if(count($archive) <=0)
{
	echo "Sorry, No Archive Record Found!";
}else{
	$archive = $archive[0];
	
	// Get the User
	$user_info = get_userdata($archive->moderatorID);
	
?>
<div class="archived-by">
	Archived By: <?php echo $user_info->display_name;?> [user login: <?php echo $user_info->user_login; ?>]
</div>
<div class="archive-comment"><?php echo nl2br($archive->comment);?></div>
<div class="archive-meta">on: <?php echo date("dS F, Y", $archive->stamp);?>, IP: <?php echo $archive->ip;?></div>
<?php	
}

endif;
?>
</body>
</html>
