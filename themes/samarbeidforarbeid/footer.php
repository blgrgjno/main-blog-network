<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */
?>

    </div> <!-- end center-column -->

    <div id="right-side-outer">
      <div id="right-side">
        <div class="helper-top shadowborder"></div>
        <div class="helper-bottom shadow"></div>
      </div>
    </div>

  </div> <!-- end wrapper -->

  <div id="footer">

    <div class="sizer" style="border:1px solid">
      <div class="line footer-helper layoutSidebar <?php if(is_page()) echo 'emptySidebar'?>"> 
        <!--fix this - its not 1992-->
       <div class="unit size2of3">Aktiviteten p&aring; nettsidene ble avsluttet 21. oktober 2010. Kildekoden <a href='http://gitorious.com/sfa'>er tilgjengelig</a> under GPLv2.</div>
       <div class="unit size2of3 lastUnit">&nbsp;</div>
      </div>
      <div class="line">
        <div class="unit size1of1 lastUnit">
          <ul class="navList with-divider">
           <?php wp_list_pages('depth=0&title_li=&sort_column=menu_order&include=436,2283,2806'); ?>
           <li class="last">
            <p>Følg oss på:</p>
            <ul class="navList socialmedialinks">
              <li><a title="Webstrøm for samarbeid for arbeid" class="socMedia socMediaLarge socMediaSyndicationLarge" href="/feed/atom"><span class="accessibilityHidden">Webstrøm for samarbeid for arbeid</span></a></li>
              <li><a title="Regjeringen på Twitter" class="external socMedia socMediaLarge socMediaTwitterLarge" href="http://twitter.com/regjeringen"><span class="accessibilityHidden">Regjeringen på Twitter</span></a></li>
              <li><a title="Regjeringen på Flickr" class="external socMedia socMediaLarge socMediaFlickrLarge" href="http://www.flickr.com/photos/statsministerenskontor/"><span class="accessibilityHidden">Regjeringen på Flickr</span></a></li>
              <li><a title="Regjeringen på Youtube" class="external socMedia socMediaLarge socMediaYouTubeLarge" href="http://www.youtube.com/regjeringen/"><span class="accessibilityHidden">Regjeringen på Youtube</span></a></li>
            </ul>
           </li>
          </ul>

        </div>
      </div>
    </div>
  </div>

  <?php wp_footer(); ?>
  <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
  <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory') . '/resource/javascript/'; ?>load.js?v=0.3"></script>
</body>
</html>
