<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */

get_header();



?>
	
    <div id="section-featured">
      
      <ul class="condensed-articles">
      	
        <?php sfa_list_condensed_articles(get_cat_id('Forside')); ?>
                    
      </ul> <!-- condensed-articles -->
	</div> <!-- end section-featured -->
    
    <div class="article layoutSidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              
              <!-- plugins/samarbeid-postrank -->
            	<?php if (function_exists('bvt_sfa_postfeed')) bvt_sfa_postfeed(); ?>

            </div>
            <div id="section-main-bottom">
              <!-- plugins go here -->
            </div>
          </div>        
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">            
              <!-- sidebar content goes here -->
              
             
              
              <div class="framed mod blue shadow">
                <div class="inner">
                  <div class="hd">
                    <h5>Inviter til debatt</h5>
                  </div>
                  <div class="bd">
                    <div class="resource-container">
                      <img alt="" src="<?php echo bloginfo('stylesheet_directory') . '/resource/image/sfa-logo-sidebar.gif';?>" />
                    </div>
                    <div class="text-container">
                      <p>Bli med i debatten på samarbeid for arbeid du også!</p>
                    </div>
                    <a class="btn facebook-share btn-blue" href="http://www.facebook.com/sharer.php?u=<?php echo get_bloginfo('url');?>&amp;t=Samarbeid%20om%20arbeid"><span>Inviter dine venner til debatt</span></a>                               
                  </div>
                  <div class="ft">
               
                  </div>
                </div>
              </div> 
              <!-- Debattinnlegg -->
              <?php debattinnlegg(get_cat_id('Forside')); // send in cat-id (integer) ?>
              
              
<!--              <div class="framed mod green">
                <div class="inner">
                  <div class="hd">
                    <h5>Gi oss innspill</h5>
                  </div>
                  <div class="bd">
                    <ul>
                      <li><a class="btn registrer-artikkel-eller-blogginnlegg" href="/gi-innspill#registrer-artikkel-eller-blogginnlegg"><span>Registrer blogginnlegg eller artikkel</span></a></li>
                      <li><a class="btn si-din-mening" href="/gi-innspill#si-din-mening"><span>Si din mening</span></a></li>
                      <li><a class="btn gode-eksempler" href="/gi-innspill#gode-eksempler"><span>Send inn gode eksempler</span></a></li>
                    </ul>
                  </div>                  
                </div>
              </div>-->
            </div>
          </div>        
        </div>      
      </div>  
     
<?php get_footer(); ?>