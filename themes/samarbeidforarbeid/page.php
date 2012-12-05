<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */

get_header();

?>
      <div class="article layoutSidebar emptySidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

              <div class="line">
                <div class="unit size4of5">
                  <h1><?php the_title(); ?></h1>
                  <div class="resource-container">
                    <?php $post_id = get_the_ID(); ?>
                    <?php if ( get_post_meta($post_id, 'Video_Watch_ID', true) ) { ?>                  
                        <?php $video_watch_id = get_post_meta($post_id, 'Video_Watch_ID', true); ?>
                        <?php echo embed_video($video_watch_id, 'middel'); // 'liten', 'middel' eller 'stor' ?>                    
                    <?php } ?>
                  </div>
                  <div class="text">
                    <?php the_content(); ?>
                  </div> 
                </div>
                <div class="unit size1of5 lastUnit">                  
                  <ul class="share-on-social-media">
                    <li><?php if (function_exists('tweetmeme')) echo tweetmeme(); ?></li>
                    <li><?php if (function_exists('fbshare_manual')) echo fbshare_manual(); ?></li>
                  </ul>
                </div>
              </div>
              
            
              
              <?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>
              
<?php endwhile; endif; ?>              
              
            </div>
            <div id="section-main-bottom">
              <!-- plugins go here -->
            </div>
          </div>        
          
          
          
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">            
              <!-- sidebar stuff goes here -->
            </div>
          </div>        
        </div>      
      </div>

<?php get_footer(); ?>
