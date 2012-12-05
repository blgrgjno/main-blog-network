<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */
 /*
Template Name: Bidrag
*/

get_header();

?>

    <div id="section-featured" class="white-bg">
      <ul class="condensed-articles">
        <li id="thanks">
          <div class="text-container">
            <div class="hgroup">
              <h2>Takk for at du bidrar til debatt!</h2>
            </div>
            <p>Hei, vi synes bloggen din er spennende og mener at mye av det du skriver om passer bra inn i regjeringens prosjekt "Samarbeid for arbeid". Vi har derfor løftet fram denne bloggposten din på vår nettside <a href="http://www.samarbeidforarbeid.no/">www.samarbeidforarbeid.no</a>. Takk for at du engasjerer deg!</p>
          </div>
          <div class="resource-container">
          	<a href="http://www.fluvi.tv/players/DSS/player.swf?watch=1400&amp;width=480" class="external video">Se video (krever flash)</a>
            <!--img src="<?php echo bloginfo('stylesheet_directory') . '/resource/image/'; ?>/Stoltenberg_480x270_foto_SMK.jpg"/-->
          </div>
        </li>
      </ul>

    </div> <!-- end section-featured -->

    <div class="article layoutSidebar white-bg">
        <div class="line">
          <div class="unit size2of3">
            <div id="section-main">
              <div id="share-exposure">
                <ul class="share-on-social-media">
                  <li>
                    <script type="text/javascript">
                      tweetmeme_source = 'Samarbeid for arbeid';
                      tweetmeme_url = <?php echo "'".(get_page_link()."?pr_post_id=".$_GET["pr_post_id"])."'";?>;
                    </script>
                    <script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
                  </li>
                  <li>
                    <a name="fb_share" type="box_count" share_url="<?php echo (get_page_link()."?pr_post_id=".$_GET['pr_post_id'])."&t=Takk for at du er en del av debatten"?>">Del</a>
                    <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
                  </li>
                </ul>
              </div>

              <div class="bvt_sfa_feedlist">
                <?php if (function_exists('bvt_sfa_postfeed')) bvt_sfa_postfeed(); ?>
              </div>
            </div>
            <div id="section-main-bottom">
              <!-- plugins go here -->
            </div>
          </div>
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">
              <div class="framed border mod blue shadow">
                <div class="inner">
                  <div class="hd"><h5>Synliggjør dine meninger</h5></div>
                  <div class="bd">
                    <p>
                      Du kan synliggjøre dine meninger på denne siden gjennom å tipse oss om lenken til din eller andres blogg eller gjennom å sende inn innspill.
                    </p>
                    <p>
                      <a href="/gi-innspill">Fortell oss om aktuelle innspill og artikler</a>
                    </p>
                  </div>
                  <div class="ft"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php get_footer(); ?>
