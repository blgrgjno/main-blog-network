<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

# Include the database functions
require_once('model.php');

function ajaxrc_get_mod_posts_url($args) {
    
	$defaults = array('echo' => false,
		'show' => 'all',
		'action' => false,
		'comment_id' => false,
		'mode' => 'list',
		'nonce' => false,
		'ip' => false);

	if(isset($_REQUEST['show'])) { $defaults['show'] = $_REQUEST['show']; }
	if(isset($_REQUEST['mode'])) { $defaults['mode'] = $_REQUEST['mode']; }
	if(isset($_REQUEST['comment_id'])) { $defaults['comment_id'] = intval($_REQUEST['comment_id']); }
	if(isset($_REQUEST['ip'])) { $defaults['ip'] = $_REQUEST['ip']; }
    
    $args = wp_parse_args($args, $defaults);
    extract($args);
    
    $url = get_bloginfo('wpurl').'/wp-admin/admin.php?page=rc_moderation';

    if ($show !== 'all') {
        $url .= '&show=' . $show;
    }

    $url .= '&mode=' . $mode;

    if ($comment_id && $comment_id > 0) {
        $url .= '&comment_id=' . $comment_id;
    }

    if ($ip && $ip > 0) {
        $url .= '&ip=' . $ip;
    }

    if ($action) {
        $url .= '&action=' . $action;
    }

    if ($nonce) {
        $url = wp_nonce_url($url,$nonce);
    }

    if ($echo) {
        echo $url; 
    }

    return $url;

}

function rc_create_report_list($cur_comment_ID)
{
		$rc_reports_list = rc_get_reports_list($cur_comment_ID);
		$rc_cur_comment = rc_get_comment_data($cur_comment_ID);
		$rc_comment_status = rc_get_comment_status($cur_comment_ID);

		$cur_comment_post_ID = $rc_cur_comment[0]->comment_post_ID;
		$cur_comment_content = $rc_cur_comment[0]->comment_content;
		$cur_comment_author = $rc_cur_comment[0]->comment_author;
		$cur_comment_author_email = $rc_cur_comment[0]->comment_author_email;
		$cur_comment_author_IP = $rc_cur_comment[0]->comment_author_IP;
		$cur_comment_date = $rc_cur_comment[0]->comment_date;
		$cur_comment_content = $rc_cur_comment[0]->comment_content;

		$post_url = get_permalink($cur_comment_post_ID);
		$cur_comment_post_title = get_the_title($cur_comment_post_ID);

		$report_info = "";
		$html = '';

		$author_email_string = '';
		if ($cur_comment_author_email)
			$author_email_string = "(<a href='mailto:".$cur_comment_author_email."'>$cur_comment_author_email</a>)";

		if ($cur_comment_post_title)
			$report_info .= "<li>Related Post: <a href='".$post_url."' target='_blank'>$cur_comment_post_title</a></li>";

		if ($cur_comment_date)
			$report_info .= "<li>Posted On: $cur_comment_date</li>";

		if ($cur_comment_author)
			$report_info .= "<li>Comment Author: $cur_comment_author $author_email_string</li>";

		if ($cur_comment_author_IP)
			$report_info .= "<li>Comment Author IP: $cur_comment_author_IP</li>";

		$html .= "

		<p><a href='admin.php?page=rc_moderation'>&laquo; Back to Moderation</a></p>

		<h3>Viewing reports for Comment #".$cur_comment_ID."</h3>
		";
		
		$return = "&return=report";
		
		if ($rc_comment_status == 0)
			$html .= "<h3><a href='admin.php?page=rc_moderation&mode=approve&comment_id=".$cur_comment_ID.$return."' title='Approve'>Approve Comment</a></h3>";
		else
			$html .= "<h3><a href='admin.php?page=rc_moderation&mode=unapprove&comment_id=".$cur_comment_ID.$return."' title='Unapprove'>Unapprove Comment</a></h3>";
							
		$html .= "

			<p><strong>The reported comment:</strong></p>
			<ul style='margin-left:15px'>
				<li>".$cur_comment_content."</li>
			</ul>

			<p><strong>More details about this comment:</strong></p>
			<ul style='margin-left:15px'>
				".$report_info."
			</ul>

			<h3>Reports for this comment</h3>
			<table class='widefat post fixed' cellspacing='0'>
				<thead>
				<tr>
					<th scope='col' id='report_id' class='manage-column column-title'>Reporter's Comment</th>
					<th scope='col' id='comment' class='manage-column column-submitted'>Date Reported</th>
					<th scope='col' id='ip' class='manage-column column-edited'>Reporter's IP</th>
				</tr>
				</thead>

				<tbody>
		";
		
			if (count($rc_reports_list) > 0)
			{
				foreach ($rc_reports_list as $report)
				{
					$html .= "
					<tr id='comment-".$report->ID."' class='alternate status-publish iedit' valign='top'>
						<td class='column-comment'>
							<ul style='font-size: 11px;'>
								<li>".stripslashes($report->reporter_comment)."</li>
							</ul>
							<div class='row-actions'>
							<span class='delete'><a href='admin.php?page=rc_moderation&mode=deletereport&report_id=".$report->id."&comment_id=".$report->comment_ID."' title='Delete this report' onclick='if ( confirm('You are about to delete report \'#".$report->id."\'\n \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;'>Delete Report</a></span>
							</div>
						</td>
						<td class='column-reporter_date'><strong>".$report->reporter_date."</strong></td>
						<td class='column-reporter_ip'><a href='http://www.dnsstuff.com/tools/ipall/?tool_id=67&token=&toolhandler_redirect=0&ip=".$report->reporter_ip."' target='_blank'>".$report->reporter_ip."</a></td>
					</tr>
					";
				}
			}
			else
			{
				$html .= "
					<tr>
						<th scope='row' class='check-column'></th>
						<td class='column-comment'>
							<p><strong>There are no reports for this comment.</strong></p>
						</td>
						<td class='column-reporter_date'></td>
						<td class='column-reporter_ip'></td>
					</tr>
					";
			}

		$html .= "
			</tbody>

			<tfoot>
			<tr>
				<th scope='col' id='report_id' class='manage-column column-title'>Reporter's Comment</th>
				<th scope='col' id='comment' class='manage-column column-submitted'>Date Reported</th>
				<th scope='col' id='ip' class='manage-column column-edited'>Reporter's IP</th>
			</tr>
			</tfoot>
		</table>
	";

	return $html;
}

function rc_set_status($cur_comment_ID, $mode, $return)
{
	if ($mode == "approve")
		$success = rc_change_comment_status($cur_comment_ID, 1);
	elseif ($mode == "unapprove")
		$success = rc_change_comment_status($cur_comment_ID, 0);

	if ($mode == "approve" || $mode == "unapprove")
	{
		if ($return == "report")
			return rc_create_report_list($cur_comment_ID);
		else
			return rc_moderation('done');
	}
}

function rc_moderation($reports='rc_moderation') {

	$show = $_REQUEST['show'];
	$mode = $_REQUEST['mode'];
	$return = $_REQUEST['return'];
	$cur_comment_ID = $_REQUEST['comment_id'];
	$cur_report_ID = $_REQUEST['report_id'];

	if ($cur_comment_ID > 0 && is_numeric($cur_comment_ID) && !$cur_report_ID && $reports !== "done")
	{
		if ($mode == "report")
		{
			print rc_create_report_list($cur_comment_ID);
		}
		elseif ($mode == "approve" || $mode == "unapprove")
		{
			print rc_set_status($cur_comment_ID, $mode, $return);
		}
		elseif ($mode == "deletecomment")
		{
			$deleted = rc_delete_comment($cur_comment_ID);
		
			if ($deleted == true)
				return rc_moderation('done');
		}

	}
	elseif ($mode == "deletereport" && $cur_report_ID > 0 && is_numeric($cur_report_ID))
	{
		$deleted = rc_delete_report($cur_report_ID, $cur_comment_ID);
		
		$output = "<p><a href='admin.php?page=rc_moderation&mode=report&comment_id=".$cur_comment_ID."'>&lt; Back to Reports for Comment #".$cur_comment_ID."</a></p><p>";
		
		if ($deleted)
			$output .= "The selected report was removed.";
		else
			$output .= "<strong>Error:</strong> The report you selected could not be deleted. It may have already been deleted.";
			
		$output .= "</p>";

		print $output;			
	}
	else
	{

	 	if ($show == 'approved')
			$approved_link_style = ' class="current"';
		else if ($show == 'pending')
			$pending_link_style = ' class="current"';
		else
			$all_link_style = ' class="current"';

		$rc_comment_report_counts = rc_comment_report_counts();
		$rc_all_report_count = rc_comments_count();
		$rc_approved_count = rc_comments_count('approved');
		$rc_pending_count = rc_comments_count('pending');
		$rc_comment_list = rc_get_comments_list($show);
	
		$nonce = wp_create_nonce('ajaxrc');

		print "

<script type=\"text/javascript\">
<!--
function confirm_delete() {
	return confirm(\"Are you sure you want to delete this comment?\");
}
//-->
</script>


			<div class='wrap'>

			<div id='icon-options-general' class='icon32'><br /></div>

			<h2>AJAX Report Comments :: Moderation</h2>

			<ul class='subsubsub'>
				<li><a href='admin.php?page=rc_moderation&mode=list'$all_link_style>All Reports ($rc_all_report_count)</a> | </li>
				<li><a href='admin.php?page=rc_moderation&show=approved&mode=list'$approved_link_style>Approved ($rc_approved_count)</a> | </li>
				<li><a href='admin.php?page=rc_moderation&show=pending&mode=list'$pending_link_style>Pending ($rc_pending_count)</a></li>
			</ul>

			<div class='tablenav'>";
   
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%', ajaxrc_get_mod_posts_url(array()) ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $max_pages,
				'current' => $paged
			));
    
			if ( $page_links ) {
		
		print "
	
			<div class='tablenav-pages'>
		
				$page_links_text = sprintf( '<span class='displaying-num'>' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
					number_format_i18n( $offset ),
					number_format_i18n( $offset + count($posts) ),
					number_format_i18n( $max_items ),
					$page_links
				);
		
				echo $page_links_text;

			</div>

		";
	
			}

		print "
			</div>
			<table class='widefat post fixed' cellspacing='0'>
				<thead>
				<tr>
					<th scope='col' id='comment_id' class='manage-column column-title'>Report Info</th>
					<th scope='col' id='comment' class='manage-column column-submitted'>Comment</th>
					<th scope='col' id='ip' class='manage-column column-edited'>Commenter's IP</th>
					<th scope='col' id='ip' class='manage-column column-date_added'>Date Added</th>
					<th scope='col' id='status' class='manage-column column-status'>Status</th>
				</tr>
				</thead>

				<tbody id='the-comment-list' class='list:comment'>
				";

				foreach ($rc_comment_list as $comment)
				{
					$reported_count = $rc_comment_report_counts[$comment->comment_ID];

					$status = '';
					$class = '';
					if ($comment->status == 1)
					{
						$status = "Approved";
						$class = "approved";
					}
					elseif ($comment->status == 0)
					{
						$status = "Pending";
						$class = "unapproved";
					}

					if ($reported_count < 2)
					{
						$times = "time";
						$reports = "report";
					}
					else
					{
						$times = "times";
						$reports = "reports";
					}
				
					if ($show == "")
						$return = "&return=all";

				print "
					<tr id='comment-".$comment->comment_ID."' class='".$class."' valign='top'>
						<td class='post-title column-comment_id'><strong>Comment #".$comment->comment_ID." - Reported ".$reported_count." $times</strong><a href='admin.php?page=rc_moderation&mode=report&comment_id=".$comment->comment_ID."' title='View reports'>View Reports</a>
							<div class='row-actions' style='min-width: 250px'>
								<span class='publish'>
								";
							
								if ($comment->status == 0)
									print "<a href='admin.php?page=rc_moderation&mode=approve&show=".$show."&comment_id=".$comment->comment_ID.$return."' title='Approve'>Approve</a> ";
								else
									print "<a href='admin.php?page=rc_moderation&mode=unapprove&show=".$show."&comment_id=".$comment->comment_ID.$return."' title='Unapprove'>Unapprove</a> ";
							
							print "| </span>
									<span class='edit'><a href='comment.php?action=editcomment&c=".$comment->comment_ID."&amp;_wpnonce=$nonce' title='Edit/View Comment'>Edit</a> | </span>
									<span class='delete'><a href='admin.php?page=rc_moderation&mode=deletecomment&show=".$show."&comment_id=".$comment->comment_ID.$return."' title='Unapprove' onclick=\"return confirm_delete();\">Delete</a></span>
							</div>
						</td>
						<td class='column-comment'>
							<ul style='font-size: 11px;'>
								<li>".$comment->comment_content."</li>
							</ul>
						</td>
						<td class='column-ip'><a href='http://www.dnsstuff.com/tools/ipall/?tool_id=67&token=&toolhandler_redirect=0&ip=".$comment->comment_author_IP."' target='_blank'>".$comment->comment_author_IP."</a></td>
						<td class='column-date_added'>".$comment->comment_date."</a></td>
						<td class='status column-status'>".$status."</td>
					</tr>
					";
				}

			print "
				</tbody>

				<tfoot>
				<tr>
					<th scope='col' id='comment_id' class='manage-column column-title'>Report Info</th>
					<th scope='col' id='comment' class='manage-column column-submitted'>Comment</th>
					<th scope='col' id='ip' class='manage-column column-edited'>Commenter's IP</th>
					<th scope='col' id='ip' class='manage-column column-date_added'>Date Added</th>
					<th scope='col' id='status' class='manage-column column-status'>Status</th>
				</tr>
				</tfoot>
			</table>
			</div>
			<div style='clear: both;'></div>

		";
	
	}

}

?>