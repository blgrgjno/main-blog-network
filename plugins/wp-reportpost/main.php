<?php
/*
Plugin Name: WP-ReportPost
Plugin URI: http://ww2.rjeevan.com/projects/wordpress/wp-reportpost
Description: <strong>(Do Not Update! Translation Mod by TKM/MW)</strong> This plugin attach form (jQuery / Ajax) to your contents that can be Invoked by any end-user to make a report on posts / pages. This plugin gives you much flexibility as what to report and archive old reports... No coding skills needed, simply install and activate.
Version: 1.2.4
Author: Rajeevan
Author URI: http://ww2.rjeevan.com
*/

# Variables
include_once('ReportPost.class.php');
$wprp = NULL;
# Map Actions
add_action('init',wprp_init);
function wprp_init()
{
	if(is_admin())
	{
		add_action('admin_menu', 'wprp_custom_field');
		add_action('save_post', 'wprp_custom_field_save');
	}
		
	// If Admin Pages, No need for Styles & Scripts
	if(is_admin() || is_feed())
		return;
	add_action('wp','wprp_wp');
}

# Attach Autometic Report IF ENABLED
function wprp_wp()
{
	if(is_admin() || is_feed()) // Again to check FEEDS
		return; 
	// Load all options
	$rp_page = (int) get_option("rp_page");
	$usersonly = (int) get_option("rp_registeronly");
	
	// Check Registered Users Only 
	if($usersonly == 1 && is_user_logged_in() == false)
		return;
	
	if(is_page() /*|| is_front_page() || is_home()*/ ){

		if($rp_page != 1)	// Return IF PAGE OPTION DISABLED
			return false;
		
	}else{
		// Get More Options
		$rp_if = (int) get_option("rp_if");

		if($rp_if != 3) // BY THEME EDIT
		{
//			add_filter('the_content',wprp_attach_report,100); // Call Attach Report Option
			add_filter('get_the_excerpt', wprp_get_the_excerpt,1);
		}
			
	} // END IF

	
	// Add jQuery
	wp_enqueue_script('jquery');
	add_action('wp_head',wprp_head);	// Adding Scripts and CSS to header	
	
	return true;
}

#Except Exception
function wprp_get_the_excerpt($output)
{
	// Remove Content Filter
	remove_filter('the_content', wprp_attach_report, 100);
	return $output;
}

# Attach REPORT to THE_CONTENT
function wprp_attach_report($text)
{
	# Get Report option to Validate Custom Fields
	$rp_if = (int) get_option("rp_if");
	global $post;
	
	if($rp_if == 2) // Repeats as Above But for ALL INC Single
	{
		$custom_field = (int)get_post_meta($post->ID, 'wprp', true);
		
		if(!$custom_field || empty($custom_field) || !is_numeric($custom_field) || $custom_field!=1)
			return $text; // Return Contents as it is....
	}

	//get_the_category()
	# Validate Category IF SELECTED!
	$cat_selected = get_option("rp_categories");
	
	if($cat_selected && !empty($cat_selected) && $cat_selected != 0 && !is_page()) // Means Category selected to Filter out
	{
		$cat_selected = explode(",",$cat_selected ); // CONVERT TO ARRAY
		
		if(in_category($cat_selected) || post_is_in_descendant_category($cat_selected))
		{
			return wprp_report_form($text);
		}
		
		return $text;
	}

	// Call Attach to Add HTML at the END!
	// Allways Return the Contents... Or it will Display EMPTY STRING!
	return wprp_report_form($text);
}

if(!function_exists("post_is_in_descendant_category"))
{
	function post_is_in_descendant_category( $cats, $_post = null )
	{
		foreach ( (array) $cats as $cat ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $cat, 'category');
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
}


# Adding Scripts and CSS to header
function wprp_head()
{	
	// Add CSS
	?>
    <link href="<?php echo path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/assets/wprp.css") ?>" rel="stylesheet" type="text/css" />
    <?php
	
	// Add  Javascripts to Do the Biddings
	?>
    <script language="javascript" type="text/javascript">
		var wprpURL = '<?php echo path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) ) ); ?>';
    </script>
    <script language="javascript" type="text/javascript" src="<?php echo path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/assets/wprp.js"); ?>" ></script>
    <?php
}


/* Attach Custom Fields*/
function wprp_custom_field()
{
	$report_if=(int)get_option("rp_if");
	if($report_if != 2)
		return;
		
	if( function_exists( 'add_meta_box' )) {
    	add_meta_box( 'wprb_custom_field', 'Add Report this option?', 'wprb_custom_field_html', 'post', 'side', 'high' );
	}
}

function wprp_custom_field_save($post_id )
{
	// Verify
	if ( !wp_verify_nonce( $_POST['wprp_noncename'], plugin_basename(__FILE__) )) {
    	return $post_id;
 	}
	if ( 'post' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
	}else{
		return $post_id;
	}

	// Get the POST
	$wprp = (int) isset($_POST['wprp_report']) ? $_POST['wprp_report'] : -1;
	
	// Update the META
	update_post_meta($post_id,'wprp', $wprp);
	
	return $post_id;
}

function wprb_custom_field_html()
{
	// Use nonce for verification

  echo '<input type="hidden" name="wprp_noncename" id="wprp_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	global $post;
	
	// Last value
	$custom_field = (int)get_post_meta($post->ID, 'wprp', true);
	if(!$custom_field || empty($custom_field) || !is_numeric($custom_field) || $custom_field!=1)
		$custom_field = 0;
	?>
    <label><input type="checkbox" name="wprp_report" id="wprp_report" value="1" <?php if($custom_field==1) echo 'checked="checked"';?> /> By selecting this option you can allow users to Report This Post</label>
    <?php
}


/* Attach Reporting Form to Contents
----------------------------------------*/
function wprp($echo = false) // MANUAL ADD CALL
{
	$text = wprp_report_form('');
	if($echo)
		echo $text;
		
	return $text;
}

function wprp_report_form($text)
{
	// Get Current POST from Global
	global $post;
	
	// Get the Options
	$options=get_option('rp_options');
	$options=(empty($options)) ? array("Report") : split('\|',$options);

	// Create Options
	$select_options="";
	foreach($options as $opt)
		$select_options .='<option value="'.$opt.'">'.$opt.'</option>'."\n";
	

	$nonce= wp_create_nonce ($post->ID);
	// Create the FORM
	$form='
	<div class="wprp_clear"></div>
	<div class="wprp_wrapper">
	<div class="wprp_report_link" id="wprp_report_link_'.$post->ID.'">
    	<a href="#" onclick="return wprp_toggle(\'#wprpform'.$post->ID.'\',\''.$post->ID.'\');">'.get_option('rp_display_text').'</a>
    </div>
	<div id="wprp_message_'.$post->ID.'" class="wprp_message">
		<img src="'.path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/loading.gif").'" title="Sender varsling. Vennligst vent..." alt="Sender varsling. Vennligst vent..." /> Sender varsling. Vennligst vent...
	</div>
	<div class="wprp_form" id="wprpform'.$post->ID.'">
    <form action="" method="post" enctype="text/plain" onsubmit="return wprp_report(this);">
    	<table width="100%" border="0" cellspacing="0" cellpadding="5">
		  <tr>
			<td align="right"><label>Varsle som: </label></td>
			<td align="left"><select name="report_as" id="report_as">
					'.$select_options.'
				</select></td>
		  </tr>
		  <tr>
			<td align="right"><label>Beskrivelse: <br/> (valgfritt) </label></td>
			<td align="left"><textarea name="description" cols="40" rows="3"></textarea></td>
		  </tr>
		  <tr>
			<td align="right" colspan="2">
				<input type="hidden" value="'.$post->ID.'" name="post"/>
				<input type="hidden" name="_wpnonce" value="'.$nonce.'" />
				<input name="do_report" type="submit" value="Send varsling" class="wprp_submit"/>
			</td>
		  </tr>
		</table>
    </form>
    </div>
	</div>
	';
	
	// Attach Form to the Contents
	
	$text .= $form;

	// Return Final Contents
	return $text;
}

/* Add Menu to Admin Pages
----------------------------------------*/
add_action('admin_menu', 'wprp_admin_menu');
function wprp_admin_menu()
{
	global $wpdb, $wprp;
	
	if($wprp == NULL)
	{
		$wprp = new ReportPost($wpdb);
		$wprp->findReports('ORDER BY id DESC',1,'WHERE status=1');
	}
	$newReports = $wprp->totalRows;

	if($newReports > 0)
		$newReports = '<span class="update-plugins count-1"><span class="">'.$newReports.'</span></span>';
	else
		$newReports='';
	
	add_menu_page('WP-REPORTPOST New Reports', 'Reports'.$newReports, 4, dirname(__FILE__)."/new-reports.php",'', WP_PLUGIN_URL.'/wp-reportpost/assets/reports.png');
	add_submenu_page( dirname(__FILE__)."/new-reports.php", 'New Reports', 'New Reports', 4,  dirname(__FILE__)."/new-reports.php");
	add_submenu_page( dirname(__FILE__)."/new-reports.php", 'Archives', 'Archives', 4,  dirname(__FILE__)."/archive-reports.php");
	add_submenu_page( dirname(__FILE__)."/new-reports.php", 'Settings', 'Settings', 8,  dirname(__FILE__)."/wprpsettings.php");
}

# Add Dashboard Widget
add_action('right_now_table_end', 'wprp_right_now_table_end');
function wprp_right_now_table_end()
{
	global $wpdb, $wprp;
	
	if($wprp == NULL)
	{
		$wprp = new ReportPost($wpdb);
		$wprp->findReports('ORDER BY id DESC',1,'WHERE status=1');
	}
	
	$class = ($wprp->totalRows >0) ? "class='wprp_wdg'" : '';
	echo "<tr>";
	echo "<td $class> $wprp->totalRows</td>";
	echo "<td colspan=3 $class>New Reports</td>";
	echo "</tr>";
	
}
# Install
register_activation_hook(__FILE__,'wprp_install');
function wprp_install()
{
	// Update VERSION
	delete_option("rp_version");
	
	global $wpdb;
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$table_name = $wpdb->prefix . "wpreport";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "
			CREATE TABLE ".$table_name." (
			`id` INT( 11 ) NOT NULL AUTO_INCREMENT,
			`postID` INT( 11 ) NOT NULL ,
			`post_title` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`stamp` VARCHAR( 15 ) NOT NULL ,
			`status` TINYINT( 1 ) NOT NULL DEFAULT '1',
			PRIMARY KEY ( `id` )
			);";
		
		dbDelta($sql);
	}
	
	$table_name = $wpdb->prefix . "wpreport_comments";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "
			CREATE TABLE ".$table_name." (
			`reportID` INT( 11 ) NOT NULL ,
			`type` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`ip` VARCHAR( 20 ) NOT NULL,
			INDEX (  `reportID` )
			);";
		
		dbDelta($sql);
	}
	
	$table_name = $wpdb->prefix . "wpreport_archive";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "
			CREATE TABLE  ".$table_name." (
			`reportID` INT( 11 ) NOT NULL ,
			`moderatorID` INT( 11 ) NOT NULL ,
			`comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`stamp` VARCHAR( 15 ) NOT NULL ,
			`ip` VARCHAR( 20 ) NOT NULL ,
			PRIMARY KEY ( `reportID` )
			);";
		
		dbDelta($sql);
	}
	
	// Set Default Values
	if(!get_option('rp_send_email'))
		update_option('rp_send_email','0');
		
	if(!get_option('rp_email_address'))
		update_option('rp_email_address',get_option("admin_email"));
		
	if(!get_option('rp_display_text'))
		update_option('rp_display_text',"[!] Report this post");
		
	if(!get_option('rp_thanks_msg'))
		update_option('rp_thanks_msg',"<strong>Thanks for Reporting [post_title]</strong>");
		
	if(!get_option('rp_options'))
		update_option('rp_options',"Invalid Contents|Other");
		
	if(!get_option('rp_if'))
		update_option('rp_if',"0");
	
	if(!get_option('rp_registeronly'))
		update_option('rp_registeronly',"0");
	
	if(!get_option('rp_page'))
		update_option('rp_page',"0");
}

/* Comon functions */
// COMMON HANDLERS
function url_filter($url, $key) {
    $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
    $url = substr($url, 0, -1);
    return ($url);
}

/* GUI Support */
add_action('admin_print_scripts', 'wprp_js_admin_header' );
function wprp_js_admin_header()
{
			
	wp_enqueue_script('jquery');  
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('thickbox');
	
}
add_action('admin_print_styles', 'wprp_wp_print_styles');
function wprp_wp_print_styles()
{
	wp_enqueue_style('wprp-admin', WP_PLUGIN_URL."/wp-reportpost/assets/wprp-admin.css"); 
	wp_enqueue_style('thickbox'); 
}

?>