<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */
/*
Template Name: Nettmøte
*/
get_header();



?>
	
    <div id="section-featured">
      
      <ul class="condensed-articles">
      
                     <li id="postid_nettmote">
            <div class="text-container">
              	  <div class="hgroup">

                	<h2>Nettmøte om samarbeid for arbeid</h2>              
              	  </div>
              	<p>I dag kl. 15.30 stiller statsminister Jens Stoltenberg, kunnskapsminister Kristin Halvorsen og kommunal- og regionalminister Liv Signe Navarsete til nettmøte på VGNett og på samarbeidforarbeid.no.</p>              	
              </div>
            	<div class="resource-container">
                
            	    <a class="external video" href="http://www.fluvi.tv/players/DSS/player.swf?watch=1299&amp;width=480">Se video</a>

  <img width="115" height="60" src="/wp-content/themes/samarbeidforarbeid/resource/image/ministre.jpg" class="thumbnail wp-post-image" alt="" title="stoltenberg" />
                              	</div>                
            	
            </li>
      
      
               <li id="postid_330">
            <div class="text-container containerBlockLink">
              	  <div class="hgroup">

                	<h2>Statsministeren inviterer til Samarbeid for arbeid</h2>              
              	  </div>
              	<p>Vår felles arbeidsinnsats er det viktigste grunnlaget for velferdsstaten. Derfor inviterer vi til samråd om hvordan man kan sikre en høy arbeidsstyrke og god bruk av arbeidskraften. ...</p>
              	<a class="read-more" href="http://samarbeidforarbeid.regjeringen.no/forside/statsministeren-inviterer-til-samarbeid-for-arbeid/" title="Statsministeren inviterer til Samarbeid for arbeid">Les mer</a>
              </div>
            	<div class="resource-container">
                
            	    <a class="external video" href="http://www.fluvi.tv/players/DSS/player.swf?watch=1299&amp;width=480">Se video</a>

  <img width="115" height="60" src="http://samarbeidforarbeid.regjeringen.no/wp-content/uploads/2010/01/stoltenberg-115x60.jpg" class="thumbnail wp-post-image" alt="" title="stoltenberg" />
                              	</div>                
            	
            </li>

                    
      </ul> <!-- condensed-articles -->
	</div> <!-- end section-featured -->
    
    <div class="article layoutSidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              <iframe src="http://www.coveritlive.com/index2.php/option=com_altcaster/task=viewaltcast/altcast_code=c5fe03a219/height=750/width=560" scrolling="no" height="750px" width="560px" frameBorder ="0" allowTransparency="true"  ><a href="http://www.coveritlive.com/mobile.php/option=com_mobile/task=viewaltcast/altcast_code=c5fe03a219" >Chat med regjeringstoppene om arbeidsliv</a></iframe>

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
              
              
              <div class="framed mod green">
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
              </div>
            </div>
          </div>        
        </div>      
      </div>  
     
<?php get_footer(); ?>
