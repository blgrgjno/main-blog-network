<?php
/*
Plugin Name: NHOP User Plugin
Plugin URI: 
Description: Utvider WordPress' brukerhåndtering til å støtte private brukere og virksomheter, med tilhørende spesialfelter.
Author: Making Waves // TKM
Version: 1.0
Author URI: http://labs.makingwaves.com
*/

class NhopUserPlugin{
	function NhopUserPlugin() {
		// Actions
		add_action('register_form', array($this, 'NHOP_RegisterForm'));
		add_action('login_head', array($this, 'NHOP_LoginHead'));
		add_action('show_user_profile', array($this, 'NHOP_ProfileShow'));
		add_action('edit_user_profile', array($this, 'NHOP_ProfileShow'));
		add_action('profile_update', array($this, 'NHOP_ProfileUpdate'));
		
		// Filters
		add_filter('user_contactmethods', array($this, 'NHOP_ContactMethods'));
		add_filter('registration_errors', array($this, 'NHOP_RegErrors') );
		add_filter('login_redirect', array($this, 'NHOP_LoginRedirect') );
	}
	
	/* Remove aim, jabber and yim from contact methods */
	function NHOP_LoginRedirect($content) {
		if (strpos($content, '/wp-admin/') !== false) return "/";
		return $content;
	}
	
	/* Remove aim, jabber and yim from contact methods */
	function NHOP_ContactMethods($contactmethods) {
		unset($contactmethods['aim']);
		unset($contactmethods['jabber']);
		unset($contactmethods['yim']);
		return $contactmethods;
	}
	
	function NHOP_ProfileShow() {
		global $user_ID;
		get_currentuserinfo();
		if( $_GET['user_id'] ) $user_ID = $_GET['user_id'];
	?>
	<h3>Tilleggsinformasjon</h3>
	<table class="form-table">
	<tbody>
	<?php
		$user_type = get_usermeta( $user_ID, 'user_type' );
	?>
		<tr>
			<th><label>Brukertype:</label></th>
			<td>
	<?php
		if (!current_user_can( 'manage_options' )) {
	?>
			<strong><?php echo ($user_type == "virksomhet") ? "Virksomhet" : "Privatperson"; ?></strong>
	<?php
	}
	else {
			
			if ($user_type == "virksomhet") $virksomhet_checked = "checked";
			else $privat_checked = "checked";
	?>
			<input type="radio" name="user_type" value="privat" id="input_privat" <?php echo $privat_checked; ?> /><label for="input_privat"> Privatperson</label>
			&nbsp;
			<input type="radio" name="user_type" value="virksomhet" id="input_virksomhet" <?php echo $virksomhet_checked; ?> /><label for="input_virksomhet"> Virksomhet/organisasjon</label>
	<?php
	}
	?>
			</td>
		</tr>
	<?php
		$value = get_usermeta( $user_ID, 'adresse_post' );
	?>
		<tr>
			<th><label for="adresse_post">Postadresse:</label></th>
			<td><input type="text" name="adresse_post" id="adresse_post" value="<?php echo $value; ?>" class="regular-text" /></td>
		</tr>
	<?php
		if ($user_type == "virksomhet") {
			$value = get_usermeta( $user_ID, 'saksbehandler' );
	?>
			<tr>
				<th><label for="saksbehandler">Saksbehandler:</label></th>
				<td><input type="text" name="saksbehandler" id="saksbehandler" value="<?php echo $value; ?>" class="regular-text" /></td>
			</tr>
	<?php
		}
	?>
	</tbody>
	</table>
	<?php
	}

	function NHOP_ProfileUpdate($user_ID){
		global $wpdb;
		
		$value = $wpdb->prepare(strip_tags($_POST['user_type']));
		if ($value) {
			update_user_meta($user_ID ,'user_type', $value);
		}
		
		$value = $wpdb->prepare(strip_tags($_POST['adresse_post']));
		if ($value) {
			update_user_meta($user_ID ,'adresse_post', $value);
		}
		
		$value = $wpdb->prepare(strip_tags($_POST['saksbehandler']));
		if ($value) {
			update_user_meta($user_ID ,'saksbehandler', $value);
		}
		
		// Force display name to "Firstname Lastname" for subscribers
		if ( !current_user_can('edit_users') ) {
			$user_info = get_userdata($user_ID);
			update_user_meta($user_ID, 'display_name', $user_info->first_name." ".$user_info->last_name);
		}
		else {
			delete_user_meta($user_ID, 'display_name');
		}
		
		// Fix URLs without http://
		$website = trim($wpdb->prepare(strip_tags($_POST['url'])));
		if (strpos($website, "http") != 0) $website = "http://" . $website;
		update_user_meta($user_ID, 'user_url', $website);
		
		/*
		// Flush disk cache on password change
		if (defined('W3TC_DIR')) {
			require_once W3TC_DIR . '/lib/W3/Plugin/TotalCache.php';
			$w3_plugin_totalcache = & W3_Plugin_TotalCache::instance();
			$w3_plugin_totalcache->flush_file();
		}
		*/
	}

	/* Login/Register */

	function NHOP_LoginHead(){
?>
<style type="text/css">
#login h1 a {
	background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/logo_login.png);
	background-position:left top;
	height: 90px;
	margin:0 0 16px 8px;
	padding:0;
	width:auto;
	display:block;
	
	-moz-border-radius:11px;
	-khtml-border-radius:11px;
	-webkit-border-radius:11px;
	border-radius:5px;
	border:1px solid #e5e5e5;
	-moz-box-shadow:rgba(200,200,200,1) 0 4px 18px;
	-webkit-box-shadow:rgba(200,200,200,1) 0 4px 18px;
	-khtml-box-shadow:rgba(200,200,200,1) 0 4px 18px;
	box-shadow:rgba(200,200,200,1) 0 4px 18px;
	
	overflow:hidden;
}
input.input {
	background:none repeat scroll 0 0 #FBFBFB;
	border:1px solid #E5E5E5;
	font-size:24px;
	margin-right:6px;
	margin-top:2px;
	padding:3px;
	width:97%;
}
textarea.input {
	background:none repeat scroll 0 0 #FBFBFB;
	border:1px solid #E5E5E5;
	font-family:arial,helvetica,sans-serif;
	font-size:12px;
	color:#555;
	margin-right:6px;
	margin-top:2px;
	padding:3px;
	width:97%;
}
input.input, textarea.input, #user_pass, #user_login, #user_email {
	margin-bottom:0;
}
#login form p, ul {
	margin-bottom:16px;
}
small {
	display:block;
	text-align:right;
	color:#777777;
	padding-top:2px;
}
<?php
	if ($_GET['action'] == "register") {
?>
#login {
	width:410px;
}
<?php
	}
?>
</style>
<script type='text/javascript' src='<?php trailingslashit(get_option('siteurl'));?>wp-includes/js/jquery/jquery.js?ver=1.2.3'></script>
<script type="text/javascript">
	jQuery(document).ready( function() {
		jQuery('#login h1 a').attr('href', '<?php echo get_option('home'); ?>');
		jQuery('#login h1 a').attr('title', '<?php echo get_option('blogname'); ?>');

		jQuery('#legend_saksbehandler').hide();
		jQuery('#legend_lastname').hide();
		jQuery('#field_saksbehandler').hide();
			
		jQuery('#input_privat').click(selectPrivat);
		jQuery('#input_virksomhet').click(selectVirksomhet);
		
		function selectPrivat() {
			jQuery('#label_firstname').text("Fornavn");
			jQuery('#field_lastname').show();
			jQuery('#field_saksbehandler').hide();
			jQuery('#label_about').text("Presentasjon av deg selv");
			jQuery('#label_website').text("Din hjemmeside");
		}
		
		function selectVirksomhet() {
			jQuery('#label_firstname').text("Navn på virksomhet/organisasjon");
			jQuery('#field_lastname').hide();
			jQuery('#field_saksbehandler').show();
			jQuery('#label_about').text("Presentasjon av virksomheten");
			jQuery('#label_website').text("Hjemmeside");
		}
		
		if (jQuery('input[name="user_type"]:checked').val() == 'virksomhet') {
			selectVirksomhet();
		}
		else {
			selectPrivat();
		}
	});
</script>
	<?php
	}
	
	# Add Fields to Register Form
	function NHOP_RegisterForm(){
	?>
		<?php if( isset( $_GET['user_type'] ) ) $_POST['user_type'] = $_GET['user_type']; ?>
		<p><label>Jeg registrerer brukeren min som en</label></p>
		<ul>
			<input tabindex="30" type="radio" name="user_type" value="privat" id="input_privat" <?php if ($_POST['user_type'] != "virksomhet") echo 'checked="checked"'; ?> /><label for="input_privat"> privatperson</label>
			&nbsp;
			<input tabindex="31" type="radio" name="user_type" value="virksomhet" id="input_virksomhet" <?php if ($_POST['user_type'] == "virksomhet") echo 'checked="checked"'; ?> /><label for="input_virksomhet"> virksomhet/organisasjon</label>
		</ul>
		
		<?php if( isset( $_GET['firstname'] ) ) $_POST['firstname'] = $_GET['firstname']; ?>
		<p><label id="label_firstname" for="firstname">Fornavn/navn på virksomhet</label><br />
		<input name="firstname" id="firstname" size="25" value="<?php echo $_POST['firstname'];?>" type="text" tabindex="32" class="input" /><br />
		</p>
		
		<?php if( isset( $_GET['lastname'] ) ) $_POST['lastname'] = $_GET['lastname']; ?>
		<p id="field_lastname"><label for="lastname"><?php _e('Last Name');?> </label><br />
		<input name="lastname" id="lastname" size="25" value="<?php echo $_POST['lastname'];?>" type="text" tabindex="33" class="input" /><br />
		<small id="legend_lastname">Fylles ikke inn av virksomheter.</small>
		</p>
		
		<?php if( isset( $_GET['adresse_post'] ) ) $_POST['adresse_post'] = $_GET['adresse_post']; ?>
		<p><label for="adresse_post">Postadresse</label><br />
		<input class="custom_field input" tabindex="34" name="adresse_post" id="adresse_post" size="25" value="<?php echo $_POST['adresse_post'];?>" type="text" /><br />
		<small>Vises ikke offentlig.</small>
		</p>

		<?php if( isset( $_GET['saksbehandler'] ) ) $_POST['saksbehandler'] = $_GET['saksbehandler']; ?>
		<p id="field_saksbehandler"><label for="saksbehandler">Saksbehandler</label><br />
		<input class="custom_field input" tabindex="35" name="saksbehandler" id="saksbehandler" size="25" value="<?php echo $_POST['saksbehandler'];?>" type="text" /><br />
		<small id="legend_saksbehandler">Fylles ikke inn av privatpersoner.</small>
		<small>Vises ikke offentlig.</small>
		</p>

		<?php if( isset( $_GET['about'] ) ) $_POST['about'] = $_GET['about']; ?>
		<p><label id="label_about" for="about">Presentasjonstekst</label><br />
		<textarea name="about" id="about" cols="25" rows="5" tabindex="36" class="input"><?php echo stripslashes($_POST['about']);?></textarea><br />
		<small>Valgfritt. Bør ikke overstige 140 tegn.</small>
		</p>
		
		<?php if( isset( $_GET['website'] ) ) $_POST['website'] = $_GET['website'];	?>
		<p><label id="label_website" for="website">Hjemmeside</label><br />
		<input name="website" id="website" size="25" value="<?php echo $_POST['website'];?>" type="text" tabindex="37" class="input" /><br />
		<small>Valgfritt. Vises offentlig.</small>
		</p>
		
		<?php
	}
	
	# Check Required Fields
	function NHOP_RegErrors($errors) {
		if ($_POST['user_type'] == "virksomhet") {
			if(empty($_POST['firstname']) || $_POST['firstname'] == ''){
				$errors->add('empty_firstname', __('<strong>Feil</strong>: Vennligst oppgi navnet på virksomheten.'));
			}
			if(empty($_POST['saksbehandler']) || $_POST['saksbehandler'] == ''){
				$errors->add('empty_saksbehandler', __('<strong>Feil</strong>: Vennligst oppgi saksbehandler.'));
			}
			
			// Check for duplicate name
			
			global $wpdb;
			$qs = "SELECT COUNT(*) FROM $wpdb->users WHERE UCASE(display_name) LIKE UCASE('" . mysql_real_escape_string(trim($_POST['firstname'])) . "'); ";
			$count = $wpdb->get_var($wpdb->prepare($qs));
			
			if ($count > 0) {
				$contact_email = "";
				if (function_exists('get_theme_option')) {
					$contact_email = get_theme_option('contact_email');
				}
				if (!$contact_email) $contact_email = get_option('admin_email');
				
				$errors->add('empty_firstname', __('<strong>Feil</strong>: '.$_POST['firstname'].' har allerede opprettet en konto. Om du mener dette er feil, vennligst kontakt <a href="mailto:'.$contact_email.'">nettredaktør</a>.'));
			}
		}
		else {
			if(empty($_POST['firstname']) || $_POST['firstname'] == '' || empty($_POST['lastname']) || $_POST['lastname'] == ''){
				$errors->add('empty_firstname', __('<strong>Feil</strong>: Vennligst oppgi både fornavn og etternavn.'));
			}
		}
		if(empty($_POST['adresse_post']) || $_POST['adresse_post'] == ''){
			$errors->add('empty_adresse_post', __('<strong>Feil</strong>: Vennligst oppgi postadresse.'));
		}
		
		return $errors;
	}	
	

}# END Class NhopUserPlugin

# Run The Plugin!
if( class_exists('NhopUserPlugin') ){
	$nhop_user_plugin = new NhopUserPlugin();
}

# Override wordpress' user notification #
if ( !function_exists('wp_new_user_notification') ) :
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = new WP_User($user_id);	
	
	#-- NHOP --#
	global $wpdb;
	if( $_POST['firstname'] )	
		update_usermeta( $user_id, 'first_name', $wpdb->prepare(strip_tags($_POST['firstname'])));
	if( $_POST['lastname'] )	
		update_usermeta( $user_id, 'last_name', $wpdb->prepare(strip_tags($_POST['lastname'])));
	if( $_POST['website'] )	{
		// Fix URLs without http://
		$website = trim($wpdb->prepare(strip_tags($_POST['website'])));
		if (strpos($website, "http") != 0) $website = "http://".$website;
		update_usermeta( $user_id, 'user_url', $website);
	}
	if( $_POST['about'] )	
		update_usermeta( $user_id, 'description', $wpdb->prepare(strip_tags($_POST['about'])));
	if( $_POST['user_type'] )	
		update_usermeta( $user_id, 'user_type', $wpdb->prepare(strip_tags($_POST['user_type'])));
	if( $_POST['adresse_post'] )	
		update_usermeta( $user_id, 'adresse_post', $wpdb->prepare(strip_tags($_POST['adresse_post'])));
	if( $_POST['saksbehandler'] )	
		update_usermeta( $user_id, 'saksbehandler', $wpdb->prepare(strip_tags($_POST['saksbehandler'])));
	
	// Force display name to "Firstname Lastname"
	update_usermeta($user_id, 'display_name', $wpdb->prepare(strip_tags($_POST['firstname']))." ".$wpdb->prepare(strip_tags($_POST['lastname'])));
	
	
	#-- END NHOP --#
	
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
}
endif;

?>