<?php 
function mytheme_add_admin() 
{
	global $themename, $shortname, $options;
	if ( $_GET['page'] == 'functions.php' )
	{
		if ( 'save' == $_REQUEST['action'] ) 
		{
			foreach ($options as $value) {
				if($value['type'] != 'multicheck'){
					update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
				}else{
					foreach($value['options'] as $mc_key => $mc_value){
						$up_opt = $value['id'].'_'.$mc_key;
						update_option($up_opt, $_REQUEST[$up_opt] );
					}
				}
			}

			foreach ($options as $value) {
				if($value['type'] != 'multicheck'){
					if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } 
				}else{
					foreach($value['options'] as $mc_key => $mc_value){
						$up_opt = $value['id'].'_'.$mc_key;						
						if( isset( $_REQUEST[ $up_opt ] ) ) { update_option( $up_opt, $_REQUEST[ $up_opt ]  ); } else { delete_option( $up_opt ); } 
					}
				}
			}
			header("Location: themes.php?page=functions.php&saved=true");
			die;
		}
	else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
				if($value['type'] != 'multicheck'){
                	delete_option( $value['id'] ); 
				}else{
					foreach($value['options'] as $mc_key => $mc_value){
						$del_opt = $value['id'].'_'.$mc_key;
						delete_option($del_opt);
					}
				}
			}
            header("Location: themes.php?page=functions.php&reset=true");
            die;

		}
	else if( 'uninstall' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
				if($value['type'] != 'multicheck'){
                	delete_option( $value['id'] ); 
				}else{
					foreach($value['options'] as $mc_key => $mc_value){
						$del_opt = $value['id'].'_'.$mc_key;
						delete_option($del_opt);
					}
				}
			}
			update_option('template', 'default');
			update_option('stylesheet', 'default');
			delete_option('current_theme');
			$theme = get_current_theme();
			do_action('switch_theme', $theme);
            header("Location: themes.php");
            die;

		}
	}
	add_theme_page($themename." Options", "$themename Options", 'edit_themes', 'functions.php', 'mytheme_admin');
}
 
function mytheme_admin() {
 
global $themename, $shortname, $options;
 
if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
 
?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url')?>/admin-style.css" media="all" />
<div class="wrap">
<h2><?php echo $themename;  _e(' Settings') ?></h2>

<form method="post">
 
<?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
<div class="open"> 

<?php break; case "open-options-div": ?>
<div id="options-div">

<?php break; case "close": ?>
</div><br />

<?php break; case "clear": ?>
<div class="clear"></div>

<?php break; case "title": ?>
<div class="title"><h3><?php echo $value['name']; ?></h3></div>

<?php break; case 'textarea': ?>
<div class="textarea">
<span class="title"><?php echo $value['name']; ?></span><span class="description"><?php echo $value['desc']; ?></span>
<textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?></textarea>
<br />
</div>

<?php break; case 'text': ?>
<div  class="text">
<span class="title"><?php echo $value['name']; ?></span>
<input class="width" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo stripslashes($value['std']); } ?>" />
<br/>
<span class="description"><?php echo $value['desc']; ?></span>
</div>
 
<?php break; case "checkbox": ?>
<div class="checkbox">
<span class="title"><?php echo $value['name']; ?></span>
<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> class="cb" />
<span class="description"><?php echo $value['desc']; ?></span>
</div>

<?php break; case 'select': ?>
<div class="select">
<span class="title"><?php echo $value['name']; ?></span>
<select class="width" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><option "selected"><?php echo stripslashes(get_settings( $value['id'] )); ?></option><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['options']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select>
<span class="description"><?php echo $value['desc']; ?></span>
</div>

<?php break; case "submit": ?>
<div class="submit">
<input name="save" type="submit" value="Save changes" class="save" />
<input type="hidden" name="action" value="save" />
</div>
</form>
<form method="post" style="margin-top:-20px;">
	<div class="submit">
	<input name="reset" type="submit" value="Reset to default" class="reset" />
	<input type="hidden" name="action" value="reset" />
	</div>
</form>
<form method="post" style="margin-top:-20px;">
	<div class="submit">
	<input name="uninstall" type="submit" value="Uninstall theme" class="reset" />
	<input type="hidden" name="action" value="uninstall" />
	</div>
</form>

<?php break; 
}
}
?>
 
</form>

<div id="side">
<div class="side-widget">
<span class="title"><?php _e('Quick links') ?></span>				
<ul>
	<li><a href="http://ajaydsouza.com/wordpress/wpthemes/connections-reloaded/"><?php _e('Connections Reloaded page') ?></a></li>
	<li><a href="http://ajaydsouza.org"><?php _e('Support forum') ?></a></li>
</ul>
</div>
<div class="side-widget">
<span class="title"><?php _e('Connections Reloaded recent developments') ?></span>				
<?php require_once(ABSPATH . WPINC . '/rss.php'); wp_widget_rss_output('http://ajaydsouza.com/archives/category/wordpress/themes/connections-reloaded/feed/', array('items' => 2, 'show_author' => 0, 'show_date' => 1));
?>
</div>
<div class="side-widget">
<span class="title"><?php _e('Support the development') ?></span>
<div id="donate-form">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="KGVN7LJLLZCMY">
<input type="hidden" name="lc" value="IN">
<input type="hidden" name="item_name" value="Connections Reloaded Donation">
<input type="hidden" name="item_number" value="conrel">
<strong><?php _e('Enter amount in USD: ') ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Send your donation to the author of Connections Reloaded">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div>
</div>
</div>

</div> 
<?php
}
?>