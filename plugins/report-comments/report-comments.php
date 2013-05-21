<?php
/*
 * Plugin Name: AJAX Report Comments
 * Plugin URI: http://tierra-innovation.com/wordpress-cms/plugins/report-comments/
 * Description: Uses AJAX to allow visitors to notify the administrator about inappropriate comments.
 * Version: 2.0.4
 * Author: Tierra Innovation
 * Author URI: http://www.tierra-innovation.com/
 */

/*
 * This plugin is currently available for use in all personal
 * or commercial projects under both MIT and GPL licenses. This
 * means that you can choose the license that best suits your
 * project, and use it accordingly.
 *
 * MIT License: http://www.tierra-innovation.com/license/MIT-LICENSE.txt
 * GPL2 License: http://www.tierra-innovation.com/license/GPL-LICENSE.txt
 */

$rc_version = '2.0.4';

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', get_option('siteurl') . '/wp-content/plugins' );

// Module globals
$_rc_db_version = '2.0.4';

// These need to be declared globally so they are in scope for the activation hook
global $wpdb, $_rc_db_version, $_rc_comments_reported_db, $_rc_comments_moderated_db;

$_rc_comments_reported_db = $wpdb->prefix . "comments_reported";
$_rc_comments_moderated_db = $wpdb->prefix . "comments_moderated";

// Installer hook
register_activation_hook(__FILE__, '_rc_install');
register_activation_hook( __FILE__, 'report_comments_install' );

define('AJAXRC_FOLDER', dirname(plugin_basename(__FILE__)));
define('AJAXRC_FULLPATH', WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.AJAXRC_FOLDER.DIRECTORY_SEPARATOR);

add_action('admin_menu', 'ajaxrc_add_menus');

function ajaxrc_add_menus() {
	add_menu_page('AJAX Report Comments', 'AJAX Report Comments', 8, AJAXRC_FOLDER, 'rc_options_page');
	add_submenu_page( AJAXRC_FOLDER , __('Moderation', 'ajaxrc'), __('Moderation', 'ajaxrc'), 'manage_options', 'rc_moderation', 'rc_moderation');
}

# Include moderation functions
require_once(AJAXRC_FULLPATH.DIRECTORY_SEPARATOR.'moderation.php' );

// Installer function to initialize db tables
function _rc_install() {

	global $wpdb, $_rc_db_version, $_rc_comments_reported_db, $_rc_comments_moderated_db;

	$pluginURL = WP_PLUGIN_URL;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "CREATE TABLE IF NOT EXISTS $_rc_comments_reported_db (
			   `id` bigint(20) NOT NULL AUTO_INCREMENT,
			   `comment_ID` bigint(20) NOT NULL DEFAULT '0',
			   `reporter_ip` varchar(15) DEFAULT NULL,
			   `reporter_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			   `reporter_comment` text NOT NULL,
				 PRIMARY KEY (`id`)
	)";

	dbDelta($sql);

	$sql = "CREATE TABLE IF NOT EXISTS $_rc_comments_moderated_db (
			   `id` bigint(20) NOT NULL AUTO_INCREMENT,
			   `comment_ID` bigint(20) NOT NULL DEFAULT '0',
			   `status` int(1) NOT NULL DEFAULT '0',
				 PRIMARY KEY (`id`)
	)";

	dbDelta($sql);

	add_option("rc_db_version", $_rc_db_version);

}

function rc_dashboard_status() {

	$rc_approved_count = rc_comments_count('approved');
	$rc_pending_count = rc_comments_count('pending');
	$rc_all_report_count = rc_comments_count('all');

    echo '<tr class="first">

		<td colspan="4" style="background: #fff; border-top: 1px solid #ececec; border-bottom: 1px solid #ececec;"><p class="sub" style="padding: 12px 12px 12px 20px !important;">AJAX Report Comments</p></td>

	</tr><tr class="first">';

			$url = get_bloginfo('wpurl').'/wp-admin/admin.php?page=rc_moderation&show=all';
			$num = number_format_i18n($rc_all_report_count);
			$text = __ngettext( 'All Reported Comments', 'All Reported Comments', $rc_all_report_count );
			echo '<td class="first b b_comments"><a href="' . $url .'">' . $num . '</a></td>';
			echo '<td class="first t posts"><a class="comments" href="' . $url .'">' . $text . '</a></td>';

			$url = get_bloginfo('wpurl').'/wp-admin/admin.php?page=rc_moderation&show=approved';
			$num = number_format_i18n($rc_approved_count);
			$text = __ngettext( 'Approved Comments', 'Approved Comments', $rc_approved_count );
			echo '<td class="first b b_approved"><a class="approved" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
			echo '<td class="first t"><a class="approved" href="' . $url .'">' . $text . '</a></td>';

			echo '</tr><tr>';

			$url = get_bloginfo('wpurl').'/wp-admin/admin.php?page=rc_moderation&show=pending';
			$num = number_format_i18n($rc_pending_count);
			$text = __ngettext( 'Reported Comments', 'Reported Comments', $rc_pending_count );
			echo '<td class="last b b-waiting"><a class="waiting" href="' . $url .'"><span class=\'pending-count\'>' . $num . '</span></a></td>';
			echo '<td class="last t"><a class="waiting" href="' . $url .'">' . $text . '</a></td>';

			echo '<td class="b b_approved"></td>';
			echo '<td class="last t">&copy; Tierra Innovation, v2.0</td>';

	echo '</tr>';
}

add_action('right_now_table_end','rc_dashboard_status');

function report_comments_install() {

$email = get_option('admin_email');
if ($email == "")
	$email = "user@domain.com";

/* Placeholder for RECaptcha support - coming in next version */
//		add_option('rc_publickey', '');
//		add_option('rc_privatekey', '');
//		add_option('rc_valid_error', 'The text you entered did not match the code in the image. Please try again.');

	add_option('rc_linktext', 'Report this comment');
	add_option('rc_beforelink', '<p>');
	add_option('rc_afterlink', '</p>');
	add_option('rc_cookie_days', '365');
	add_option('rc_threshold', '0');
	add_option('rc_textarea_msg', 'Please tell us why you are reporting this comment (optional):');
	add_option('rc_allow_reporter_comment', 1);
	add_option('rc_success', 'The comment has been reported. Thank you.');
	add_option('rc_already', 'You have already reported this comment.');
	add_option('rc_failure', 'There was an error reporting this comment. Please try again later.');
	add_option('rc_enable_email', 0);
	add_option('rc_email', $email);
	add_option('rc_email_subject', 'Comment reported on post: %POST_TITLE%');
	add_option('rc_email_msg', 'A visitor on your site has reported the following comment:

TITLE: %POST_TITLE%
URL: %COMMENT_URL%
Author: %COMMENT_AUTHOR%
Date: %COMMENT_DATE%

Comment text:

%COMMENT_TEXT%

Reason for report:

%REPORTER_COMMENT%

----------------------------------------------

Information about the comment reporter:

IP Address: %REPORTER_IP_ADDRESS%
Hostname: %REPORTER_HOSTNAME% 
Browser: %REPORTER_BROWSER%
Referring URL: %REPORTER_REFERRER%

');

}

function rc_options_page() {

	global $rc_version;

	$email = get_option('admin_email');
	if ($email == "")
		$email = "user@domain.com";

	if (isset($_POST['set_defaults'])) {
		echo '<div id="message" class="updated fade"><p><strong>';

/* Placeholder for RECaptcha support - coming in next version */
//		update_option('rc_publickey', '');
//		update_option('rc_privatekey', '');
//		update_option('rc_valid_error', 'The text you entered did not match the code in the image. Please try again.');

		update_option('rc_linktext', 'Report this comment');
		update_option('rc_beforelink', '<p>');
		update_option('rc_afterlink', '</p>');
		update_option('rc_cookie_days', '365');
		update_option('rc_threshold', '0');
		update_option('rc_textarea_msg', 'Please tell us why you are reporting this comment (optional):');
		update_option('rc_allow_reporter_comment', 1);
		update_option('rc_success', 'The comment has been reported. Thank you.');
		update_option('rc_already', 'You have already reported this comment.');
		update_option('rc_failure', 'There was an error reporting this comment. Please try again later.');
		update_option('rc_enable_email', 0);
		update_option('rc_email', $email);
		update_option('rc_email_subject', 'Comment reported on post: %POST_TITLE%');
		update_option('rc_email_msg', 'A visitor on your site has reported the following comment:

URL: %COMMENT_URL%
Author: %COMMENT_AUTHOR%
Date: %COMMENT_DATE%

Comment text:

%COMMENT_TEXT%

Reason for report:

%REPORTER_COMMENT%

----------------------------------------------

Information about the comment reporter:

IP Address: %REPORTER_IP_ADDRESS%
Hostname: %REPORTER_HOSTNAME% 
Browser: %REPORTER_BROWSER%
Referring URL: %REPORTER_REFERRER%
');

		echo 'Default Options Loaded!';
		echo '</strong></p></div>';

	} else if (isset($_POST['info_update'])) {

		$rc_cookie_days = stripslashes((string)$_POST["rc_cookie_days"]);

		if (!is_numeric($rc_cookie_days))
			$rc_cookie_days = 365;

		?><div id="message" class="updated fade"><p><strong><?php 

/* Placeholder for RECaptcha support - coming in next version */
//		update_option('rc_publickey', stripslashes((string)$_POST["rc_publickey"]));
//		update_option('rc_privatekey', stripslashes((string)$_POST["rc_privatekey"]));
//		update_option('rc_valid_error', stripslashes((string)$_POST["rc_valid_error"]));

		update_option('rc_linktext', stripslashes((string)$_POST["rc_linktext"]));
		update_option('rc_beforelink', stripslashes((string)$_POST["rc_beforelink"]));
		update_option('rc_afterlink', stripslashes((string)$_POST["rc_afterlink"]));
		update_option('rc_cookie_days', $rc_cookie_days);
		update_option('rc_threshold', stripslashes((string)$_POST["rc_threshold"]));
		update_option('rc_textarea_msg', stripslashes((string)$_POST["rc_textarea_msg"]));
		update_option('rc_allow_reporter_comment', stripslashes((int)$_POST["rc_allow_reporter_comment"]));
		update_option('rc_success', stripslashes((string)$_POST["rc_success"]));
		update_option('rc_already', stripslashes((string)$_POST["rc_already"]));
		update_option('rc_failure', stripslashes((string)$_POST["rc_failure"]));
		update_option('rc_enable_email', stripslashes((int)$_POST["rc_enable_email"]));
		update_option('rc_email', stripslashes((string)$_POST["rc_email"]));
		update_option('rc_email_subject', stripslashes((string)$_POST["rc_email_subject"]));
		update_option('rc_email_msg', stripslashes((string)$_POST["rc_email_msg"]));

		echo "Configuration Updated!";
	    ?></strong></p></div><?php

	} ?>

	<div class="wrap">

	<div id="icon-options-general" class="icon32"><br /></div>

	<h2>AJAX Report Comments</h2>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<input type="hidden" name="info_update" id="info_update" value="true" />

	<h3>Comment Display Options</h3>

	<p>Optionally set a threshold to automatically hide comments reported more than X number of times.</p>
	<p><strong>Note:</strong> Setting the "Hide comment" option to '0' disables this feature.</p>

	<table>
	<tr>
		<th><label for="rc_threshold" style='font-weight: normal;'>Hide comment automatically if more than</label></th>
		<td><input name="rc_threshold" id="rc_threshold" type="text" size="2" value="<?php echo htmlspecialchars(get_option('rc_threshold')) ?>" /> reports</td>
	</tr>
	</table>

	<h3>Link Display Options</h3>

	<p>Set the link text and tags surrounding the text that will appear after each comment.</p>

	<table class="form-table">
	<tr>
		<th><label for="rc_beforelink">Code Before Link:</label></th>
		<td><input name="rc_beforelink" id="rc_beforelink" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_beforelink')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_linktext">Link Text:</label></th>
		<td><input name="rc_linktext" id="rc_linktext" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_linktext')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_afterlink">Code After Link:</label></th>
		<td><input name="rc_afterlink" id="rc_afterlink" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_afterlink')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_allow_reporter_comment">Allow reporter to include a reason for reporting the comment:</label></th>
		<td>
			<input name="rc_allow_reporter_comment" type="hidden" value="0" />
			<input name="rc_allow_reporter_comment" id="rc_allow_reporter_comment" type="checkbox" value="1" <?php if (get_option('rc_allow_reporter_comment')==1) print 'checked'; ?> />
		</td>
	</tr>
	<tr>
		<th><label for="rc_textarea_msg">Text to display above reporter comment box:</label></th>
		<td><input name="rc_textarea_msg" id="rc_textarea_msg" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_textarea_msg')) ?>" /></td>
	</tr>
	</table>

	<p>&nbsp;</p>

	<h3>Report Success &amp; Error Message Options</h3>

	<p>When reporting a comment, specify the various messages a user might see.</p>

	<table class="form-table">
	<tr>
		<th><label for="rc_success">Success Message:</label></th>
		<td><input name="rc_success" id="rc_success" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_success')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_failure">Error Message:</label></th>
		<td><input name="rc_failure" id="rc_failure" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_failure')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_already">Already Reported Message:</label></th>
		<td><input name="rc_already" id="rc_already" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_already')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="rc_cookie_days">Number of days to track previously reported comments:</label></th>
		<td><input name="rc_cookie_days" id="rc_cookie_days" type="text" size="5" value="<?php echo htmlspecialchars(get_option('rc_cookie_days')) ?>" /></td>
	</tr>
	</table>

	<p>&nbsp;</p>
	
<!-- Recaptcha will be implemented in the next version -->

<!--
	<h3>Recaptcha Options</h3>

	<p>Requires a free recapthcha account for image verification. Sign up at <a href="http://recaptcha.net/whyrecaptcha.html">http://recaptcha.net</a>.
		<br /><strong>Leave these fields blank to disable image verification.</strong></p>

	<table class="form-table">
		<tr>
			<th><label for="rc_publickey">Public Key:</label></th>
			<td><input name="rc_publickey" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_publickey')) ?>"/></td>
		</tr>
		<tr>
			<th><label for="rc_privatekey">Private Key:</label></th>
			<td><input name="rc_privatekey" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_privatekey')) ?>"/></td>
		</tr>
		<tr>
			<th><label for="rc_valid_error">Validation error message:</label></th>
			<td><input name="rc_valid_error" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_valid_error')) ?>"/></td>
		</tr>
	</table>
	
	<p>&nbsp;</p>
-->	

	<h3>Email Options (optional)</h3>

	<p>Set your address, subject title and message.  Every time a comment is reported, you will receive an email.</p>

	<table class="form-table">
	<tr>
		<th><label for="rc_enable_email">Enable Email:</label></th>
		<td>
			<input name="rc_enable_email" type="hidden" value="0" />
			<input name="rc_enable_email" id="rc_enable_email" type="checkbox" value="1" <?php if (get_option('rc_enable_email')==1) print 'checked'; ?> />
		</td>
	</tr>
	<tr>
		<th><label for="rc_email">Email Address:</label></th>
		<td><input name="rc_email" id="rc_email" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_email')) ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th><label for="rc_email_subject">Email Subject:</label></th>
		<td><input name="rc_email_subject" id="rc_email_subject" type="text" size="70" value="<?php echo htmlspecialchars(get_option('rc_email_subject')) ?>" /></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th><label for="rc_email_msg">Email Message:</label></th>
		<td width="200"><textarea name="rc_email_msg" id="rc_email_msg" cols="65" rows="14"><?php echo htmlspecialchars(get_option('rc_email_msg')) ?></textarea></td>
		<td valign="top"><strong>Available Tags</strong>

			<p>You can use the following tags in the email subject and/or message:</p>

			<ul>
				<li><strong>%COMMENT_ID%</strong> - The ID number of the comment</li>
				<li><strong>%COMMENT_URL%</strong> - The URL of the comment</li>
				<li><strong>%POST_ID%</strong> - The ID of the post</li>
				<li><strong>%POST_URL%</strong> - The URL of the post</li>
				<li><strong>%POST_TITLE%</strong> - The title of the post</li>
				<li><strong>%COMMENT_AUTHOR%</strong> - The author of the comment</li>
				<li><strong>%COMMENT_DATE%</strong> - The date of the comment</li>
				<li><strong>%COMMENT_TEXT%</strong> - The comment text</li>
				<li><strong>%REPORTER_IP_ADDRESS%</strong> - The reporter's IP address</li>
				<li><strong>%REPORTER_HOSTNAME%</strong> - The reporter's hostname</li>
				<li><strong>%REPORTER_REFERRER%</strong> - The referring URL for the reporter</li>
				<li><strong>%REPORTER_BROWSER%</strong> - The reporter's browser</li>
			</ul>

		</td>
	</tr>
	</table>

	<div class="submit">
		<input type="submit" name="info_update" class='button-primary' value="<?php _e('Update options'); ?> &raquo;" /> or 
		<input type="submit" name="set_defaults" class="button-secondary delete" value="<?php _e('Restore Default Options'); ?> &raquo;" />
	</div>

	</form>
	</div><?php
}

function rc_check_threshold($comment_ID) {

	global $wpdb, $_rc_comments_moderated_db, $_rc_comments_reported_db;

	$mod_status = rc_get_comment_status($comment_ID);
	$threshold = get_option('rc_threshold');

	if ($mod_status == 1)
		return 2;

	if ($threshold > 0)
	{
		$report_count = rc_comment_report_counts($comment_ID);
		$report_count = $report_count[$comment_ID];

		if ($report_count > $threshold)
			return 0;
	}

	return 1;
}

function rc_process($content) {

	$link_before = get_option('rc_beforelink');
	$link_after = get_option('rc_afterlink');
	$link_text = get_option('rc_linktext');
	$allow_reporter_comment = get_option('rc_allow_reporter_comment');
	$comment_ID = get_comment_ID();
	$show_link = rc_check_threshold($comment_ID);

	$t_out = '';

	if ($show_link == 1)
	{
		$t_out = $link_before;
		
		if ($allow_reporter_comment == 1)
		{
			$t_out .= "
				<span id=\"reportcomment_results_div_".$comment_ID."\"><a href=\"javascript:void(0);\" onclick=\"reportComment_AddTextArea( ".$comment_ID." );\" title=\"Report this comment\" rel=\"nofollow\">$link_text</a></span>
				<span id=\"reportcomment_comment_div_".$comment_ID."\"></span>
			";
		}
		else
		{
			$t_out .= "<span id=\"reportcomment_results_div_".$comment_ID."\"><a href=\"javascript:void(0);\" onclick=\"reportComment( ".$comment_ID." );\" title=\"Report this comment\" rel=\"nofollow\">$link_text</a></span>";
		}

		$t_out .= $link_after;
	}
	elseif ($show_link == 0)
	{
		$t_out = "<style type='text/css'>#comment-".$comment_ID." { display:none; }</style>";
	}

	return $content.$t_out;
}

function reportcomments_js_header() // this is a PHP function
{
  // use JavaScript SACK library for Ajax
  wp_print_scripts( array( 'sack' ));

  // Javascript functions for reporting comment
?>

<script type="text/javascript">
//<![CDATA[

	function reportComment( commentID )
	{

		<?php 
		if (get_option('rc_allow_reporter_comment') == 1)
		{
		?>	
			var reporter_comment = document.getElementById( 'reportcomment_comment_textarea_' + commentID ).value;
			var mysack = new sack( '<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/report-comments/report.php?c='+commentID+'&r='+escape(reporter_comment) );
		<?php 
		}
		else
		{
		?>
			var mysack = new sack( '<?php bloginfo( 'wpurl' ); ?>/wp-content/plugins/report-comments/report.php?c='+commentID );
		<?php	
		}
		?>
		
		mysack.method = 'POST';
	
		mysack.onError	= function() { die( "<?php print htmlspecialchars(get_option('rc_failure')); ?>" ) };
		mysack.onCompletion = function() { finishReport( commentID, eval( '(' + this.response + ')' )); }
	
		mysack.runAJAX();
	}

	function reportComment_AddTextArea( commentID )
	{
		document.getElementById( 'reportcomment_results_div_' + commentID ).innerHTML = "<?php print htmlspecialchars(get_option('rc_textarea_msg')); ?>";

		var textarea = "<textarea name=\"reportcomment_comment_textarea_" + commentID + "\" id=\"reportcomment_comment_textarea_" + commentID + "\" cols=\"55\" rows=\"4\" class=\"reportcomment_textarea\"></textarea><br /><input type=\"button\" name=\"Report Comment\" value=\"Report Comment\" onclick=\"reportComment( " + commentID + " );\" />";

		document.getElementById( 'reportcomment_comment_div_' + commentID ).innerHTML = textarea;
	}

	function finishReport( commentID, response )
	{
		var message = '<span class="reportedcomment_text">'+response.message+'</span>';
		document.getElementById( 'reportcomment_results_div_' + commentID ).innerHTML = message;
		<?php 
		if (get_option('rc_allow_reporter_comment') == 1)
		{
		?>	
			document.getElementById( 'reportcomment_comment_div_' + commentID ).innerHTML = '';
		<?php
		}
		?>
	}
//]]>
</script>

<style type="text/css">
	.reportedcomment_text { color: #777; }
</style>

<?php
} // end of PHP function reportcomments_js_header

add_action('wp_head', 'reportcomments_js_header' );
add_filter('comment_text', 'rc_process');

?>
