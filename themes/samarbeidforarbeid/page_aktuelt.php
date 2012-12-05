<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */
/*
Template Name: Aktuelt
*/

get_header();

?>
      <div class="article layoutSidebar emptySidebar">
        <div class="line">
          <div class="unit size4of5">
            <div id="section-main">
              <!-- center column content goes here -->              
              <?php if (have_posts()) : while (have_posts()) : the_post(); ?>              
              <h1><?php the_title(); ?></h1>
              <?php endwhile; endif; ?>
              
              <?php $args = array(
								  'title_li' => '',
								  'child_of'=> get_cat_id('Tema'),								  
								  'orderby' => 'id',
								  'depth' => 1,
								  'order' => 'ASC',
								  'hide_empty' => 0
								  );
			  	$categories = get_categories($args);
			  ?>
              <?php selection_filter($categories); ?>
			  <?php
			  //ignore sticky for this page
			  remove_filter('posts_orderby', 'sticky_orderby',1);
				if( isset($_GET['v']) ) {
					get_query_by_url( $_GET['v'], $categories );
				} else {
					query_posts('posts_per_page=-1&cat=' . get_cat_id('Tema') .",+".get_cat_id('Forside') .',-' . get_cat_id('Temabeskrivelse') . ',-' . get_cat_id('Debattinnlegg'));
				}
			  ?>
              
              <ol class="archive-listing">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>            
                <li>
                  <h2>
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                  </h2>
                  <dl class="byline">
                    <dt class="accessibilityHidden">Dato:</dt>
                    <dd><?php the_time('j. F Y') ?></dd>
                    <?php the_category_extended(', ', '<dt>Tema:</dt><dd>', '</dd>'); ?>
                    <?php if ( is_user_logged_in() ){ ?>            
                    	<dt>Redaksjonelle verktøy:</dt>
                    	<dd><?php edit_post_link('Rediger'); ?></dd>
                    <?php } ?>
                  </dl>
                  <div class="shortintro">
                    <?php the_excerpt(); ?>
                  </div>
                  <hr />
                </li>  
         
                <?php endwhile; ?>
              </ol>


              
              <?php else :
			  	echo '<p>Ingen aktuelle innlegg funnet.</p>';
			  endif; ?>
              
            </div>
          </div> <!-- end unit size2of3 -->
          <div class="unit size1of5 lastUnit">
          
          </div>                
        </div>      
      </div>

<?php get_footer(); ?>
