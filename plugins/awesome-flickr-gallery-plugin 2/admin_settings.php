<?php
include_once('afg_libs.php');
include_once('edit_galleries.php');
include_once('add_gallery.php');
include_once('view_delete_galleries.php');
include_once('advanced_settings.php');
require_once('afgFlickr/afgFlickr.php');

add_action('admin_init', 'afg_admin_init');
add_action('admin_init', 'afg_auth_read');
add_action('admin_menu', 'afg_admin_menu');
add_action('wp_ajax_afg_gallery_auth', 'afg_auth_init');

function afg_admin_menu() {
    add_menu_page('Awesome Flickr Gallery', 'Awesome Flickr Gallery', 'activate_plugins', 'afg_plugin_page', 'afg_admin_html_page', BASE_URL . "/images/afg_logo.png", 898);
    $afg_main_page = add_submenu_page('afg_plugin_page', 'Default Settings | Awesome Flickr Gallery', 'Default Settings', 'activate_plugins', 'afg_plugin_page', 'afg_admin_html_page');
    $afg_add_page = add_submenu_page('afg_plugin_page', 'Add Gallery | Awesome Flickr Gallery', 'Add Gallery', 'manage_links', 'afg_add_gallery_page', 'afg_add_gallery');
    $afg_saved_page = add_submenu_page('afg_plugin_page', 'Saved Galleries | Awesome Flickr Gallery', 'Saved Galleries', 'manage_links', 'afg_view_edit_galleries_page', 'afg_view_delete_galleries');
    $afg_edit_page = add_submenu_page('afg_plugin_page', 'Edit Galleries | Awesome Flickr Gallery', 'Edit Galleries', 'manage_links', 'afg_edit_galleries_page', 'afg_edit_galleries');
    $afg_advanced_page = add_submenu_page('afg_plugin_page', 'Advanced Settings | Awesome Flickr Gallery', 'Advanced Settings', 'activate_plugins', 'afg_advanced_page', 'afg_advanced_settings_page');
   
    add_action('admin_print_styles-' . $afg_edit_page, 'afg_edit_galleries_header');
    add_action('admin_print_styles-' . $afg_add_page, 'afg_edit_galleries_header');
    add_action('admin_print_styles-' . $afg_saved_page, 'afg_view_delete_galleries_header');
    add_action('admin_print_styles-' . $afg_main_page, 'afg_admin_settings_header');
    
    // adds "Settings" link to the plugin action page
    add_filter( 'plugin_action_links', 'afg_add_settings_links', 10, 2);

    afg_setup_options();
}

function afg_add_settings_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__)) . '/index.php' ) {
        $settings_link = '<a href="plugins.php?page=afg_plugin_page">' . 'Settings</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

function afg_admin_settings_header() {
    wp_enqueue_script('admin-settings-script');
    add_action('admin_head', 'afg_admin_headers');
}

function afg_admin_headers() {
    echo "
          <link href=\"https://plus.google.com/110562610836727777499\" rel=\"publisher\" />
          <script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\">
</script>";
}

function afg_setup_options() {
    if (get_option('afg_descr') == '1') update_option('afg_descr', 'on');
    if (get_option('afg_descr') == '0') update_option('afg_descr', 'off');
    if (get_option('afg_captions') == '1') update_option('afg_captions', 'on');
    if (get_option('afg_captions') == '0') update_option('afg_captions', 'off');
    if (get_option('afg_credit_note') == '1' || get_option('afg_credit_note') == 'Yes') update_option('afg_credit_note', 'on');
    if (get_option('afg_credit_note') == '0') update_option('afg_credit_note', 'off');
    if (!get_option('afg_pagination')) update_option('afg_pagination', 'on');
    if (get_option('afg_slideshow_option') == '' || get_option('afg_slideshow_option') == 'highslide') update_option('afg_slideshow_option', 'colorbox');
    if (get_option('afg_custom_css') == '') update_option('afg_custom_css', '/* Start writing your custom CSS here */');
    if (get_option('afg_disable_slideshow')) update_option('afg_slideshow_option', 'disable');

    $galleries = get_option('afg_galleries');
    if (!$galleries) {
        $galleries = array('0' =>
            array(
                'name' => 'My Photostream',
                'gallery_descr' => 'All photos from my Flickr Photostream with default settings.',
            )
        );
        update_option('afg_galleries', $galleries);
    }

    if (!get_option('afg_sort_order')) update_option('afg_sort_order', 'flickr');

    update_option('afg_version', VERSION);
}

/* Keep afg_admin_init() and afg_get_all_options() in sync all the time
 */

function afg_admin_init() {
    register_setting('afg_settings_group', 'afg_api_key');
    register_setting('afg_settings_group', 'afg_user_id');
    register_setting('afg_settings_group', 'afg_per_page');
    register_setting('afg_settings_group', 'afg_photo_size');
    register_setting('afg_settings_group', 'afg_captions');
    register_setting('afg_settings_group', 'afg_descr');
    register_setting('afg_settings_group', 'afg_columns');
    register_setting('afg_settings_group', 'afg_credit_note');
    register_setting('afg_settings_group', 'afg_bg_color');
    register_setting('afg_settings_group', 'afg_version');
    register_setting('afg_settings_group', 'afg_galleries');
    register_setting('afg_settings_group', 'afg_width');
    register_setting('afg_settings_group', 'afg_pagination');
    register_setting('afg_settings_group', 'afg_users');
    register_setting('afg_settings_group', 'afg_include_private');
    register_setting('afg_settings_group', 'afg_auth_token');
    register_setting('afg_settings_group', 'afg_disable_slideshow');
    register_setting('afg_settings_group', 'afg_slideshow_option');
    register_setting('afg_settings_group', 'afg_dismis_ss_msg');
    register_setting('afg_settings_group', 'afg_api_secret');
    register_setting('afg_settings_group', 'afg_flickr_token');
    register_setting('afg_settings_group', 'afg_custom_size');
    register_setting('afg_settings_group', 'afg_custom_size_square');
    register_setting('afg_settings_group', 'afg_custom_css');
    register_setting('afg_settings_group', 'afg_sort_order');

    // Register javascripts
    wp_register_script('edit-galleries-script', BASE_URL . '/js/edit_galleries.js');
    wp_register_script('admin-settings-script', BASE_URL . '/js/admin_settings.js');
    wp_register_script('view-delete-galleries-script', BASE_URL . '/js/view_delete_galleries.js');
    wp_register_script('delete-users-script', BASE_URL . '/js/delete_users.js');
}

function afg_get_all_options() {
    return array(
        'afg_api_key' => get_option('afg_api_key'),
        'afg_user_id' => get_option('afg_user_id'),
        'afg_photo_size' => get_option('afg_photo_size'),
        'afg_per_page' => get_option('afg_per_page'),
        'afg_sort_order' => get_option('afg_sort_order'),
        'afg_custom_size' => get_option('afg_custom_size'),
        'afg_custom_size_square' => get_option('afg_custom_size_square'),
        'afg_captions' => get_option('afg_captions'),
        'afg_descr' => get_option('afg_descr'),
        'afg_columns' => get_option('afg_columns'),
        'afg_credit_note' => get_option('afg_credit_note'),
        'afg_bg_color' => get_option('afg_bg_color'),
        'afg_width' => get_option('afg_width'),
        'afg_pagination' => get_option('afg_pagination'),
        'afg_api_secret' => get_option('afg_api_secret'),
        'afg_flickr_token' => get_option('afg_flickr_token'),
        'afg_slideshow_option' => get_option('afg_slideshow_option'),
    );
}

function print_all_options() {
    $all_options = afg_get_all_options();
    foreach($all_options as $key => $value) {
        echo $key . ' => ' . $value . '<br />';
    }
}

function afg_auth_init() {
    session_start();
    global $pf;
    unset($_SESSION['afgFlickr_auth_token']);
    $pf->setToken('');
    $pf->auth('read', $_SERVER['HTTP_REFERER']);
    exit;
}

function afg_auth_read() {
    if ( isset($_GET['frob']) ) {
        global $pf;
        $auth = $pf->auth_getToken($_GET['frob']);
        update_option('afg_flickr_token', $auth['token']['_content']);
        $pf->setToken($auth['token']['_content']);
        header('Location: ' . $_SESSION['afgFlickr_auth_redirect']);
        exit;
    }
}

create_afgFlickr_obj();

function afg_admin_html_page() {
    global $afg_photo_size_map, $afg_on_off_map, $afg_descr_map, 
        $afg_columns_map, $afg_bg_color_map, $afg_width_map, $pf,
        $afg_sort_order_map, $afg_slideshow_map;
?>
   <div class='wrap'>
   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
    echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Awesome Flickr Gallery Settings</h2>

<?php
    if ($_POST) {
        global $pf;

        if (isset($_POST['submit']) && $_POST['submit'] == 'Delete Cached Galleries') {
            delete_afg_caches();
            echo "<div class='updated'><p><strong>Cached data deleted successfully.</strong></p></div>";
        }
        else if (isset($_POST['submit']) && $_POST['submit'] == 'Save Changes') {
            update_option('afg_api_key', $_POST['afg_api_key']);
            if (!$_POST['afg_api_secret'] || $_POST['afg_api_secret'] != get_option('afg_api_secret'))
                update_option('afg_flickr_token', '');
            update_option('afg_api_secret', $_POST['afg_api_secret']);
            update_option('afg_user_id', $_POST['afg_user_id']);
            if (ctype_digit($_POST['afg_per_page']) && (int)$_POST['afg_per_page']) {
                update_option('afg_per_page', $_POST['afg_per_page']);
            }
            else {
                update_option('afg_per_page', 10);
                echo "<div class='updated'><p><strong>You entered invalid value for Per Page option.  It has been set to 10.</strong></p></div>";
            }
            update_option('afg_sort_order', $_POST['afg_sort_order']);
            update_option('afg_photo_size', $_POST['afg_photo_size']);
            if (get_option('afg_photo_size') == 'custom') {
                if (ctype_digit($_POST['afg_custom_size']) && (int)$_POST['afg_custom_size'] >= 50 && (int)$_POST['afg_custom_size'] <= 500) {
                    update_option('afg_custom_size', $_POST['afg_custom_size']);
                }
                else {
                    update_option('afg_custom_size', 100);
                    echo "<div class='updated'><p><strong>You entered invalid value for Custom Width option.  It has been set to 100.</strong></p></div>";
                }
                update_option('afg_custom_size_square', $_POST['afg_custom_size_square']?$_POST['afg_custom_size_square']:'false');
            }
            update_option('afg_captions', $_POST['afg_captions']);
            update_option('afg_descr', $_POST['afg_descr']);
            update_option('afg_columns', $_POST['afg_columns']);
            update_option('afg_slideshow_option', $_POST['afg_slideshow_option']);
            update_option('afg_width', $_POST['afg_width']);
            update_option('afg_bg_color', $_POST['afg_bg_color']);

            if (isset($_POST['afg_credit_note']) && $_POST['afg_credit_note']) update_option('afg_credit_note', 'on');
            else update_option('afg_credit_note', 'off');

            if (isset($_POST['afg_pagination']) && $_POST['afg_pagination']) update_option('afg_pagination', 'off');
            else update_option('afg_pagination', 'on');

            echo "<div class='updated'><p><strong>Settings updated successfully.</br></br><font style='color:red'>Important Note:</font> If you have installed a caching plugin (like WP Super Cache or W3 Total Cache etc.), you may have to delete your cached pages for the settings to take effect.</strong></p></div>";
            if (get_option('afg_api_secret') && !get_option('afg_flickr_token')) {
                echo "<div class='updated'><p><strong>Click \"Grant Access\" button to authorize Awesome Flickr Gallery to access your private photos from Flickr.</strong></p></div>";
            }
        }
        create_afgFlickr_obj();
    }
    $url=$_SERVER['REQUEST_URI'];
?>
    <form method='post' action='<?php echo $url ?>'>
        <?php echo afg_generate_version_line() ?>
               <div class="postbox-container" style="width:69%; margin-right:1%">
                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Flickr Settings</h3>
                        <table class='form-table'>
                           <tr valign='top'>
                              <th scope='row'>Flickr API Key</th>
                              <td style='width:28%'><input type='text' name='afg_api_key' size='30' value="<?php echo get_option('afg_api_key'); ?>" ><font style='color:red; font-weight:bold'>*</font></input> </td>
                              <td><font size='2'>Don't have a Flickr API Key?  Get it from <a href="http://www.flickr.com/services/api/keys/" target='blank'>here.</a> Go through the <a href='http://www.flickr.com/services/api/tos/'>Flickr API Terms of Service.</a></font></td>
                           </tr>
                                <th scope='row'>Flickr API Secret</th>
                           <td style="vertical-align:top"><input type='text' name='afg_api_secret' id='afg_api_secret' value="<?php echo get_option('afg_api_secret'); ?>"/>
                            <br /><br />
<?php if (get_option('afg_api_secret')) { 
    if (get_option('afg_flickr_token')) { echo "<input type='button' class='button-secondary' value='Access Granted' disabled=''"; } else {
        ?>
    <input type="button" class="button-primary" value="Grant Access" onClick="document.location.href='<?php echo get_admin_url() .  'admin-ajax.php?action=afg_gallery_auth'; ?>';"/>
                        <?php }}
    else {
    echo "<input type='button' class='button-secondary' value='Grant Access' disabled=''";    
} ?>
                           </td>
                           <td style="vertical-align:top"><font size='2'><b>ONLY</b> If you want to include your <b>Private Photos</b> in your galleries, enter your Flickr API Secret here
                            and click Save Changes.</font>
                        </td>
                    </tr>

                           <tr valign='top'>
                              <th scope='row'>Flickr User ID</th>
                              <td><input type='text' name='afg_user_id' size='30' value="<?php echo get_option('afg_user_id'); ?>" /><font style='color:red; font-weight:bold'>*</font> </td>
                              <td><font size='2'>Don't know your Flickr User ID?  Get it from <a href="http://idgettr.com/" target='blank'>here.</a></font></td>
                           </tr>
                        </table>
                     </div>
                  </div>

                  <div id="poststuff">
                     <div class="postbox" style='box-shadow:0 0 2px'>
                        <h3>Gallery Settings</h3>
                        <table class='form-table'>

                           <tr valign='top'>
                              <th scope='row'>Max Photos Per Page</th>
                              <td style="width:28%"><input type='text' name='afg_per_page' id='afg_per_page' onblur='verifyPerPageBlank()' size='3' maxlength='3' value="<?php
    echo get_option('afg_per_page')?get_option('afg_per_page'):10;
?>" /><font style='color:red; font-weight:bold'>*</font></td>
                           </tr>

                            <tr valign='top'>
                              <th scope='row'>Sort order of Photos</th>
                              <td><select type='text' name='afg_sort_order' id='afg_sort_order'>
                                    <?php echo afg_generate_options($afg_sort_order_map, get_option('afg_sort_order', 'flickr')); ?>
                              </select>
                              <td><font size='2'>Set the sort order of the photos as per your liking and forget about how photos are arranged on Flickr.</font></td>
                              </td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Size of the Photos</th>
                              <td><select name='afg_photo_size' id='afg_photo_size' onchange='customPhotoSize()'>
                                    <?php echo afg_generate_options($afg_photo_size_map, get_option('afg_photo_size', '_m')); ?>
                              </select></td>
                           </tr>

                           <tr valign='top' id='afg_custom_size_block' style='display:none'>
                             <th>Custom Width</th>
                             <td><input type='text' size='3' maxlength='3' name='afg_custom_size' id='afg_custom_size' onblur='verifyCustomSizeBlank()' value="<?php echo get_option('afg_custom_size')?get_option('afg_custom_size'):100; ?>"><font color='red'>*</font> (in px)
                             &nbsp;Square? <input type='checkbox' name='afg_custom_size_square' value='true' <?php if (get_option('afg_custom_size_square') == 'true') echo "checked=''"; ?>>
                             </td>
                             <td><font size='2'>Fill in the exact width for the photos (min 50, max 500).  Height of the photos will be adjusted
                                                accordingly to maintain aspect ratio of the photo. Enable <b>Square</b> to crop
                                                the photo to a square aspect ratio.</td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Photo Titles</th>
                              <td><select name='afg_captions'>
                                    <?php echo afg_generate_options($afg_on_off_map, get_option('afg_captions', 'on')); ?>
                              </select></td>
                              <td><font size='2'>Photo Title setting applies only to Thumbnail (and above) size photos.</font></td>
                           </tr>

                           <tr valign='top'>
                              <th scope='row'>Photo Descriptions</th>
                              <td><select name='afg_descr'>
                                    <?php echo afg_generate_options($afg_descr_map, get_option('afg_descr', 'off')); ?>
                              </select></td>
                              <td><font size='2'>Photo Description setting applies only to Small and Medium size photos.</td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Number of Columns</th>
                                 <td><select name='afg_columns'>
                                       <?php echo afg_generate_options($afg_columns_map, get_option('afg_columns', '2')); ?>
                                 </select></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Slideshow Behavior</th>
                                 <td><select name='afg_slideshow_option'>
                                       <?php echo afg_generate_options($afg_slideshow_map, get_option('afg_slideshow_option', 'colorbox')); ?>
                                 </select></td>
                                 <td><font size='2'></font></td>
                              </tr>


                              <tr valign='top'>
                                 <th scope='row'>Background Color</th>
                                 <td><select name='afg_bg_color'>
                                       <?php echo afg_generate_options($afg_bg_color_map, get_option('afg_bg_color', 'Transparent')); ?>
                                 </select></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Gallery Width</th>
                                 <td><select name='afg_width'>
                                       <?php echo afg_generate_options($afg_width_map, get_option('afg_width', 'auto')); ?>
                                 </select></td>
                                 <td><font size='2'>Width of the Gallery is relative to the width of the page where Gallery is being generated.  <i>Automatic</i> is 100% of page width.</font></td>
                              </tr>

                              <tr valign='top'>
                                 <th scope='row'>Disable Pagination?</th>
                                 <td><input type='checkbox' name='afg_pagination' value='off'
<?php
    if (get_option('afg_pagination', 'off') == 'off') {
        echo 'checked=\'\'';
    }
?>/></td>
                                 <td><font size='2'>Useful when displaying gallery in a sidebar widget where you want only few recent photos.</td>
                                 </tr>

                                 <tr valign='top'>
                                    <th scope='row'>Add a Small Credit Note?</th>
                                    <td><input type='checkbox' name='afg_credit_note' value='Yes'
<?php
    if (get_option('afg_credit_note', 'on') == 'on') {
        echo "checked=''";
    }
?>/></td>
                                    <td><font size='2'>Credit Note will appear at the bottom of the gallery as - </font>
                                       Powered by
                                       <a href="http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin" title="Awesome Flickr Gallery by Ronak Gandhi"/>
                                          AFG</a></td>
                                 </tr>
                              </table>
                        </div></div>
                        <input type="submit" name="submit" id="afg_save_changes" class="button-primary" value="Save Changes" />
                        <br /><br />
                        <div id="poststuff">
                           <div class="postbox" style='box-shadow:0 0 2px'>
                              <h3>Your Photostream Preview</h3>
                              <table class='form-table'>
                                 <tr><th>If your Flickr Settings are correct, 5 of your recent photos from your Flickr photostream should appear here.</th></tr>
                                 <td>
<?php
    global $pf;
    if (get_option('afg_flickr_token')) $rsp_obj = $pf->people_getPhotos(get_option('afg_user_id'), array('per_page' => 5, 'page' => 1));
    else $rsp_obj = $pf->people_getPublicPhotos(get_option('afg_user_id'), NULL, NULL, 5, 1);
    if (!$rsp_obj) echo afg_error();
    else {
        foreach($rsp_obj['photos']['photo'] as $photo) {
            $photo_url = "http://farm{$photo['farm']}.static.flickr.com/{$photo['server']}/{$photo['id']}_{$photo['secret']}_s.jpg";
            echo "<img src=\"$photo_url\"/>&nbsp;&nbsp;&nbsp;";
        }
    }
?>
                                    <br />
                                    Note:  This preview is based on the Flickr Settings only.  Gallery Settings 
                                    have no effect on this preview.  You will need to insert gallery code to a post 
                                    or page to actually see the Gallery.
                                 </td>
                           </table></div>
                           <input type="submit" name="submit" class="button-secondary" value="Delete Cached Galleries"/>
                        </div>
<?php
    if (DEBUG) {
        print_all_options();
    }
?>
                     </div>
                     <div class="postbox-container" style="width: 29%;">
<?php
    $message = "<b>What are Default Settings?</b> - Default Settings serve as a 
        template for the galleries.  When you create a new gallery, you can assign 
        <i>Use Default</i> to a setting.  Such a setting will reference the <b>Default 
        Settings</b> instead of a specific setting defined for that particular 
        gallery. <br /> <br />
        When you change any of <b>Default Settings</b>, all the settings in a gallery 
        referencing the <b>Default Settings</b> will inherit the new value.<br /><br />
        <font color='red'><b>Important Note about Private Photos:</b></font><br/>To access
        your private photos from Flickr, make sure that your App's authentication
        type is set to <b>Web Application</b> and the <b>Callback URL</b>
        points to <font color='blue'><i>" . get_admin_url() . "</i></font>
        ";
    echo afg_box('Help', $message);

    $message = "Just insert the code <strong><font color='steelblue'>[AFG_gallery]</font></strong> in any of your posts or pages to display the Awesome Flickr Gallery.
        <br /><p style='text-align:center'><i>-- OR --</i></p>You can create a new Awesome Flickr Gallery with different settings on page <a href='{$_SERVER['PHP_SELF']}?page=afg_add_gallery_page'>Add Galleries.";
    echo afg_box('Usage Instructions', $message);

    echo afg_donate_box(); 
    echo afg_share_box();
?>
        </div>
            </form>
<?php

}
?>
