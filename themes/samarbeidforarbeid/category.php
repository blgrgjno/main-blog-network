<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */

// the current category id
$current_cat_id = $GLOBALS['cat'];


get_header();
?>


	<?php if ( $current_cat_id != get_cat_id('Tema') ) { ?>
    
    	<div id="section-featured">
        	<ul class="condensed-articles">
			
			<?php
			// list short version of the articles in this cat
			if (have_posts()) :
				sfa_list_condensed_articles($current_cat_id); // more in function.php
			else :
				echo '<li>Ingen innlegg funnet.</li>';
			endif;
			?>
            
            </ul> <!-- condensed-articles -->
        </div> <!-- end section-featured -->
    
	<?php } ?> 
     
    
    <div class="article layoutSidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              
              
				<?php if ( $current_cat_id == get_cat_id('Tema') ) { ?>
                
                    <div class="line">
                    <div class="unit size4of5">
                        <h1>Temaoversikt:</h1>
                        <div class="shortintro">
                        	<ul>
                        		<?php wp_list_categories('title_li=&child_of=4&orderby=id&depth=1&hide_empty=0&style=none'); ?>
                            </ul>
                        </div>
                    </div>
                    </div>
                
                <?php } else { ?> 
              
              
              <!-- plugins/samarbeid-postrank -->
            	<?php if (function_exists('bvt_sfa_postfeed')) bvt_sfa_postfeed(); ?>
                
                <?php } ?>

            </div>
            <div id="section-main-bottom">
              <!-- plugins go here -->
            </div>
          </div>        
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">            
              <!-- sidebar content goes here -->
              
              <!-- Temabeskrivelse -->
              <?php temabeskrivelse($current_cat_id); // send in cat-id (integer) ?>
            
<!--              <div class="mod">
                <div class="inner">
                  <a href="<?php echo get_option('home'); ?>/gi-innspill#gode-eksempler" class="btn btn-green goto"><span>Gi oss innspill og eksempler</span></a>
                </div>
              </div>      -->
            
              <!-- Debattinnlegg -->
              <?php debattinnlegg($current_cat_id); // send in cat-id (integer) ?>
              
              
            </div>
          </div>        
        </div>      
      </div>  
    
<?php get_footer(); ?>