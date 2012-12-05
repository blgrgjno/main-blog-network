<?php
require_once('afg_libs.php');

if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/lib/codemirror.css");
    wp_enqueue_script('afg_custom_css_js', BASE_URL . "/CodeMirror/lib/codemirror.js");
    wp_enqueue_script('afg_custom_css_theme_js', BASE_URL . "/CodeMirror/mode/css/css.js");
    wp_enqueue_style('afg_custom_css_theme_css', BASE_URL . "/CodeMirror/theme/cobalt.css");
    wp_enqueue_style('afg_custom_css_style', BASE_URL . "/CodeMirror/css/docs.css");
    add_action('admin_head', 'afg_advanced_headers');
}

function afg_advanced_headers() {
    echo "
          <link href=\"https://plus.google.com/110562610836727777499\" rel=\"publisher\" />
          <script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\"></script>
          ";
   }

   function afg_advanced_settings_page() {
       $url=$_SERVER['REQUEST_URI'];
   ?>
   <div class='wrap'>
   <h2><a href='http://www.ronakg.com/projects/awesome-flickr-gallery-wordpress-plugin/'><img src="<?php
      echo (BASE_URL . '/images/logo_big.png'); ?>" align='center'/></a>Advanced Settings | Awesome Flickr Gallery</h2>

<?php
      if (isset($_POST['afg_advanced_save_changes']) && $_POST['afg_advanced_save_changes']) {
          update_option('afg_disable_slideshow', isset($_POST['afg_disable_slideshow'])? $_POST['afg_disable_slideshow']: '');
          update_option('afg_slideshow_option', $_POST['afg_slideshow_option']);
          update_option('afg_custom_css', $_POST['afg_custom_css']);
          echo "<div class='updated'><p><strong>Settings updated successfully.</strong></p></div>";
      }
?>
         <form method='post' action='<?php echo $url ?>'>
            <?php echo afg_generate_version_line() ?>
            <div class="postbox-container" style="width:69%; margin-right:1%">
               <div id="poststuff">
                  <div class="postbox" style='box-shadow:0 0 2px'>
                     <h3>Custom CSS</h3>
                        <div style="background-color:#FFFFE0; border-color:#E6DB55; maargin:5px 0 15px; border-radius:3px 3px 3px 3px; border-width: 1px; border-style: solid; padding: 8px 10px; line-height: 20px">
                Check <a href='<?php echo BASE_URL . '/afg.css';?>' target='_blank'>afg.css</a> to see existing classes and properties for gallery which you can redefine here. Note that there is no validation applied to CSS Code entered here, so make sure that you enter valid CSS.
                    </div><br/>
                    <textarea id='afg_custom_css' name='afg_custom_css'><?php echo get_option('afg_custom_css');?></textarea>
       <script type="text/javascript">var myCodeMirror = CodeMirror.fromTextArea(document.getElementById('afg_custom_css'), {
       lineNumbers: true, indentUnit: 4, theme: "cobalt", matchBrackets: true} );</script>
</div>
               </div>
            <input type="submit" name="afg_advanced_save_changes" id="afg_advanced_save_changes" class="button-primary" value="Save Changes" />
         </div>

         <div class="postbox-container" style="width: 29%;">
<?php
      $message = "Settings on this page are global and hence apply to all your Galleries.";
      echo afg_box('Help', $message);
      echo afg_donate_box();
      echo afg_share_box();
?>
            </div>
         </form>
      </div>
<?php
   }
?>
