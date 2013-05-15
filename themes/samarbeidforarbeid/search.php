<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */

get_header(); ?>

      <div class="article layoutSidebar emptySidebar">
        <div class="line">
          <div class="unit size4of5">    
            <div id="section-main">
            <!-- center column content goes here -->              
              <h1>S&oslash;keresultat</h1>
              <div>
                <?php get_search_form_sfa(TRUE); // show searchstring, default: none ?>
              </div>
              
              <?php if (have_posts()) : ?>
              <p class="info">
                <?php
                  /* If this is a search result */
                  if (is_search()) {                
                    /* Search Counter */
					$s = esc_html( $s );
                    $allsearch = &new WP_Query("s=$s&showposts=-1");
                    $key = esc_html( wp_specialchars($s, 1 ) );
                    $count = $allsearch->post_count;
                    _e('Vi fant ');
                      echo $count;
                    if ( $count == 1 ) { _e(' resultat '); }
                    else { _e(' resultater '); }
                    _e('p&aring; <strong>&lsquo;');
                      echo esc_html( $key ); _e('&lsquo;</strong>');
                    wp_reset_query();
                  }
                ?>
              </p>        
              <ol class="archive-listing">
                <?php while (have_posts()) : the_post(); ?>            
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
              <div class="navigation">
                <ul>
                  <li><?php next_posts_link('&laquo; Eldre saker') ?></li>
                  <li><?php previous_posts_link('Nyere saker &raquo;') ?></li>
                </ul> 
              </div>
              <?php else : ?>
                <p class="info">Vi fant ingen resultater p&aring; <strong>&lsquo;<?php the_search_query(); ?>&lsquo;</strong></p>		
              <?php endif; ?>           
            </div>    
          </div> <!-- end unit size2of3 -->          
        </div>      
      </div>

<?php get_footer(); ?>