<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */

// get the post her that the navigation nows which item to mark
if (have_posts()) : while (have_posts()) : the_post();

get_header();

?>

      <div class="article layoutSidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              
              
              <div class="line">
              	<p class="categorization-list">
                    <?php the_category_extended(', ', 'G&aring; til temaforsiden: ', ''); ?>
                    </p>
                <div class="unit size4of5">
            
                  <h1><?php the_title(); ?></h1>
                  
                  <dl class="byline">
                    <dt>Publisert:</dt>
                    <dd><?php the_time('j. F Y') ?></dd>  
                    <?php if ( is_user_logged_in() ){ ?>            
                    	<dt>Redaksjonelle verktøy:</dt>
                    	<dd><?php edit_post_link('Rediger'); ?></dd>
                    <?php } ?>
                  </dl>                           
    
                  <div class="shortintro">
                    <?php the_excerpt(); ?>
                  </div>
                </div>
                <div class="unit size1of5 lastUnit">
                  <ul class="share-on-social-media">
                    <li><?php if (function_exists('tweetmeme')) echo tweetmeme(); ?></li>
                    <li><?php if (function_exists('fbshare_manual')) echo fbshare_manual(); ?></li>
                  </ul>
                </div>
              </div>
              <div class="resource-container">
			  <?php get_media_content(get_the_ID()); ?>
			  </div>
              
              <div class="text">
                <?php the_content(); ?>
              </div>
              

  
              <?php endwhile; else: ?>

		<p></p>

<?php endif; ?> 
              
              
            </div> <!-- end section-main -->
            <div id="section-main-bottom">
              
                  <a href="<?php echo get_option('home'); ?>/gi-innspill/#gode-eksempler" class="btn btn-blue"><span>Send inn gode eksempler</span></a>
              
              <!-- plugins go here -->
              <?php if(function_exists('selfserv_sexy')) { selfserv_sexy(); } ?>
              
              <!--hr/-->
              <div class="feedback">
                 <?php comments_template();?>
              </div>              
            </div>
          </div> <!-- end unit size2of3 -->
          
         
          
          
          
          
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">            
              <!-- sidebar content goes here -->
              <div class="mod">
                <div class="inner">
                  <a href="<?php echo get_option('home'); ?>/gi-innspill/#gode-eksempler" class="btn btn-green goto"><span>Gi oss innspill og eksempler</span></a>
                </div>
              </div>
              
              <div class="mod condensed-articles">
                <div class="inner">
                  <div class="hd"><h5>Siste aktuelle saker</h5></div>
                  <div class="bd">
                    <ol>
                    
                    
 <?php
  //ignore sticky for this page
  remove_filter('posts_orderby', 'sticky_orderby',1);
	$args = array(	        
				  'cat' => $current_cat_id . '-' . get_cat_id('Temabeskrivelse') . ',-' . get_cat_id('Debattinnlegg'),
				  'posts_per_page' => 2
				  );
	query_posts($args);
	while (have_posts()) : the_post(); ?>

					  <li>
                        <h6><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h6>
                        <div class="text">
                          <span class="date"><?php the_time('j. F Y') ?></span>
                          <?php if ( is_user_logged_in() ){ post_sticky_status(); } /* end is_user_logged_in */ ?>
                          <?php the_excerpt(); ?>
                        </div>
                      </li>
<?php endwhile; ?>       
                      
                    </ol>
                  </div>
                  <div class="ft">
                    <a href="<?php echo get_option('home'); ?>/aktuelt/?v=<?php echo $last_current_cat_slug ?>">Flere aktuelle saker</a>
                    <hr/>
                  </div>
                </div>
              </div>
              
              <div class="mod">
                <div class="inner">
                  <div class="bvt_sfa_sidebar">
                 	  <!-- plugins/samarbeid-postrank -->                 	
                 	  <?php if (function_exists('bvt_sfa_postfeed')) bvt_sfa_postfeed($single_cat_id); ?>                               
                  </div>
                </div>
              </div>
            </div>
          </div>        
        </div>      
      </div>      

<?php get_footer(); ?>
