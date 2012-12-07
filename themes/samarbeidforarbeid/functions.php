<?php

/* 
 * Disable theme updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 */
function samarbeid_hidden_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}
add_filter( 'http_request_args', 'samarbeid_hidden_theme', 5, 2 );

/**
 * @package sfa
 * @subpackage sfa-theme
 */


add_image_size( 'featured-image', 457, 470, true );

if ( function_exists( 'add_feed' ) )
	add_feed('innspill', 'create_feed_innspill');

function create_feed_innspill() {
	include(TEMPLATEPATH . '/atom-innspill.php');
}
 
function the_category_extended($separator, $html_before, $html_after) {
	global $current_cat_id; // used in single.php too, to get the to last posts
	global $current_cat_names;
	global $last_current_cat_slug; // used in single.php to create the link 'flere aktuelle saker'
	global $single_cat_id; //if a posts belong to multiple categories, this will always contain the last
	                       //in the returned list used for listing of relevant feed elements. 
	$i = 0;
	//edit below for categories you want excluded
	$exclude = array('Temabeskrivelse', 'Debattinnlegg', 'Forside', 'Uncategorized', 'Tema');
	//don't edit below here!
	$new_the_category = '';
	$current_cat_id = '';
	$current_cat_names ='';
	foreach((get_the_category()) as $category){
		if (!in_array($category->cat_name, $exclude)){
			$new_the_category .= '<a href="/tema/' . $category->slug . '">' . $category->name . '</a>' . $separator;
			/* $new_the_category .= '<a href="' . get_bloginfo(url) . '/' . get_option('category_base') . '/' . $category->slug . '">' . $category->name . '</a>' . $separator; */
			// For single.php => get the category id and names
			$current_cat_id .= get_cat_id($category->name).', ';
			$single_cat_id = get_cat_id($category->name);
			$current_cat_names .= $category->name.', ';
			$i++;
			$last_current_cat_slug = $category->slug; // used in single.php to create the link 'flere aktuelle saker'
		}
	}
	if ( $i > 0 ) { // If there are items to show
		echo $html_before;
		echo substr($new_the_category, 0, strrpos($new_the_category, $separator));
		echo $html_after;
	}
}



function alike_multiple_cats($tema_id, $current_categories) {
	$alike = FALSE;
	foreach($current_categories as $current_category) {
		if ( $current_category->cat_ID == $tema_id ) { $alike = TRUE; }
	} return $alike;
}

function alike_single_cat($tema_id, $current_cat_id) {
	$alike = FALSE;
	if ( $current_cat_id == $tema_id ) { $alike = TRUE; }
	return $alike;
}


function get_class_if_current($tema_id) {
	if ( is_single() ) { $current_categories = get_the_category(); }
	if ( is_single() && alike_multiple_cats($tema_id, $current_categories) ) { echo ' class="current-cat"'; }
	
	if ( is_category() ) { $current_cat_id = $GLOBALS['cat']; }
	if ( is_category() && alike_single_cat($tema_id, $current_cat_id) ) { echo ' class="current-cat"'; }
}

function the_navigation_sfa() {	
	//	$idObj_1 = get_category_by_slug('sykefravaer');
	$idObj_1 = get_category_by_slug('integrering');
	$idObj_2 = get_category_by_slug('frafall-i-videregaaende-opplaering');
	$idObj_3 = get_category_by_slug('naringsutvikling');
	$idObj_4 = get_category_by_slug('barekraftig-okonomi');
?>
<li id="tema_id_<?php echo $idObj_1->term_id ?>"<?php get_class_if_current($idObj_1->term_id) ?>>
	<a href="<?php echo get_option('siteurl') . '/tema/integrering/' ?>" title="<?php echo $idObj_1->name ?>" ><?php echo $idObj_1->name ?></a></li>
    
<li id="tema_id_<?php echo $idObj_2->term_id ?>"<?php get_class_if_current($idObj_2->term_id) ?>>
	<a href="<?php echo get_option('siteurl') . '/tema/frafall-i-videregaaende-opplaering/' ?>" title="<?php echo $idObj_2->name ?>" ><?php echo $idObj_2->name ?></a></li>

<li id="tema_id_<?php echo $idObj_3->term_id ?>"<?php get_class_if_current($idObj_3->term_id) ?>>
	<a href="<?php echo get_option('siteurl') . '/tema/naringsutvikling/' ?>" title="<?php echo $idObj_3->name ?>" ><?php echo $idObj_3->name ?></a></li>

<li id="tema_id_<?php echo $idObj_4->term_id ?>"<?php get_class_if_current($idObj_4->term_id) ?>>
	<a href="<?php echo get_option('siteurl') . '/tema/barekraftig-okonomi/' ?>" title="<?php echo $idObj_4->name ?>" ><?php echo $idObj_4->name ?></a></li>
<?php
}

function embed_lazy_video($watch_id, $width){
  ?>
  <a class="external video" href="http://stream.regjeringen.no/dssplayer_2_0_0_6.swf?watch=<?php echo $watch_id; ?>&amp;width=<?php echo $width; ?>">Se video (krever flash)</a>
  <?php
}

function get_search_form_sfa($show_searchstring) {
	do_action( 'get_search_form' );

	$search_form_template = locate_template(array('searchform.php'));
	if ( '' != $search_form_template ) {
		require($search_form_template);
		return;
	}

	$form = '<form action="'. get_option('home') .'" method="get" class="search" id="portal-search">
	<fieldset>
	<label class="accessibilityHidden" for="s">'. esc_attr__('Search') .'</label>
	<input title="Søk i redaksjonelt innhold" type="text" class="inputField toggleable"  value="';
	if ( $show_searchstring == TRUE ) { $form .= esc_attr(apply_filters('the_search_query', get_search_query())); } else{$form .= esc_attr(apply_filters('the_search_query', 'Søk i redaksjonelt innhold'));}
	$form .= '" name="s" id="s" />
	<button type="submit" title="Send inn s&oslash;k">'. esc_attr__('Search') .'</button>
      </fieldset>
    </form>';

	echo apply_filters('get_search_form', $form);
}



function temabeskrivelse($cat) {
	$args = array(
				  'posts_per_page' => 1,
				  'category__and' => array($cat,get_cat_id('Temabeskrivelse'))
				  );
	query_posts($args);
if (have_posts()) :
	while (have_posts()) : the_post(); ?>
    <div class="mod condensed-article">
      <div class="inner">
        <div class="hd">
          <h5><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h5>
        </div>
        <div class="bd">
          <?php // bilde
          $post_id = get_the_ID();
          if ( get_post_meta($post_id, 'Sidekolonne_Bilde_URL', true) ) {
            echo '<div class="resource-container">';
              $temabeskrivelse_bilde_url = get_post_meta($post_id, 'Sidekolonne_Bilde_URL', true);
              echo '<img src="'.$temabeskrivelse_bilde_url.'" alt="" width="80" />';
            echo '</div>';
          } ?>
          <div class="text-container">
        	 <?php the_excerpt('Read the rest of this entry &raquo;') ?>
            <?php edit_post_link('Rediger', '', ''); ?>
          </div>        
        </div>
        <div class="ft">
          <hr/>
        </div>
      </div>
    </div> 
	<?php endwhile;
endif;
}


function debattinnlegg($cat) {
	$args = array(				  
				  'category__and' => array($cat,get_cat_id('Debattinnlegg'))
				  );
	query_posts($args);
if (have_posts()) : ?>
<div class="mod framed blue shadow slideshow">
  <div class="inner">
    <div class="hd">
      <h5>Kjør debatt</h5>
    </div> <!-- .head -->
    <div class="bd">
      <ul class="slides">
        <?php while (have_posts()) : the_post();  $post_id = get_the_ID();?>
        <li id="postid_<?php echo $post_id ?>" class="condensed-article">
          <h6><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h6>
          
          <?php // bilde
          $post_id = get_the_ID();
          if ( get_post_meta($post_id, 'Sidekolonne_Bilde_URL', true) ) {
            echo '<div class="resource-container">';
              $temabeskrivelse_bilde_url = get_post_meta($post_id, 'Sidekolonne_Bilde_URL', true);
              echo '<img src="'.$temabeskrivelse_bilde_url.'" alt="" width="80" />';
            echo '</div>';
          } ?>
          
          <div class="text-container">
            <?php the_excerpt() ?>
            <?php edit_post_link('Rediger', '', ''); ?>   
          </div>
          <div>
          <a class="btn btn-blue facebook-share external" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode(the_permalink())?>&amp;t=<?php echo urlencode(the_title('','',false)); ?>"><span>Inviter dine venner til debatt!</span></a>
          </div>  
          <!-- div> --><!-- Del andre steder  -->
              <!-- ?php if (function_exists('sharethis_button')) { sharethis_button(); } ? -->
          <!-- /div -->
        </li>
        <?php endwhile; ?>
      </ul>
    </div> <!-- .body -->
  </div>
</div>
<?php
endif;
}



// Add Post Thumbnail Images for posts (since wp 2.9
add_theme_support( 'post-thumbnails', array( 'post' ) );
set_post_thumbnail_size( 115, 60, true ); // 115 pixels wide by 60 pixels tall, hard crop mode


function get_media_content($post_id) {
	
	// If Meta Video_Watch_ID
	if ( get_post_meta($post_id, 'Video_Watch_ID', true) ) {
		$video_watch_id = get_post_meta($post_id, 'Video_Watch_ID', true);
		if ( !is_single() ) { echo embed_lazy_video($video_watch_id, '480'); } // 'liten', 'middel' eller 'stor'
		else { echo embed_lazy_video($video_watch_id, '540'); }
		// Set a mini feature image
		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) {
			// output the featured image as mini with 115 x 60
			if ( !is_single() ) { the_post_thumbnail(array( 115,60 ), array('class' => 'thumbnail') ); }
		} else {
			if ( !is_single() ) {
				// output some dummies
				$stylesheet_directory = get_bloginfo('stylesheet_directory');
				echo '<img class="thumbnail" src="'. $stylesheet_directory . '/resource/image/mini-ingress-dummy.gif" alt="" />';
			}
		}
	
	} else { // If no Meta Video_Watch_ID
		if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) {
			// output the featured image with the croped size 480 x 270 and the same as mini with 115 x 60
			$post_title = get_the_title('', false);
			if ( !is_single() ) {

				the_post_thumbnail( 'featured-image', array('alt' => $post_title) );
				the_post_thumbnail(array( 115,60 ), array('class' => 'thumbnail') );
			} // else { show // thumbnail in the post
			//	the_post_thumbnail( 'large', array('alt' => $post_title) );
			// } //
					
		} else {
			if ( !is_single() ) {
				// output some dummies
				$stylesheet_directory = get_bloginfo('stylesheet_directory');
				echo '<img src="'. $stylesheet_directory . '/resource/image/ingress-dummy.gif" alt=""/>';
				echo '<img class="thumbnail" src="'. $stylesheet_directory . '/resource/image/mini-ingress-dummy.gif" alt=""  />';
			}
		}
	}
}

function excerpt($num){
  $limit = $num+1;  
  $origexcerpt = get_the_excerpt();
  $excerpt = explode(" ", $origexcerpt, $limit);
  if(count($excerpt) > $limit-1){
    array_pop($excerpt);
  }  
  $excerpt = implode(" ",$excerpt);  
  if (strlen($excerpt) < strlen($origexcerpt)) {
    $excerpt = $excerpt." ...";
  }
  echo $excerpt;
}

function sfa_list_condensed_articles($current_cat_id) {
$args = array(
			  'post__in' => get_option('sticky_posts'),
			  'caller_get_posts' => 1,
			  'cat' => $current_cat_id . ',-' . get_cat_id('Temabeskrivelse') . ',-' . get_cat_id('Debattinnlegg')
            );
  query_posts($args);

  while (have_posts()) : the_post(); $post_id = get_the_ID();?>

            <li id="postid_<?php echo $post_id ?>">
            <div class="text-container containerBlockLink">
              	  <div class="hgroup">
                	<h2><?php the_title(); ?></h2>              
              	  </div>
              	<p><?php excerpt(30); ?></p>
              	<a class="read-more" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">Les mer</a>
              </div>
            	<div class="resource-container">
                
            	  <?php get_media_content(get_the_ID()); ?>

                  <?php /* if ( is_user_logged_in() ){ post_sticky_status(); } // end is_user_logged_in  edit_post_link('Rediger', '', ''); */ ?>
            	</div>                
            	
            </li>
<?php
 endwhile;
 }



function check_post_cat() {
	global $current_cat_names;
	$include = array('Temabeskrivelse', 'Debattinnlegg');
	$show = TRUE;
	foreach((get_the_category()) as $category) {
		if ( !in_array($category->cat_name, $include) ) {
			$show = TRUE;
		} else {
			$show = FALSE;
		}
	}
	return $show;
}


function selection_filter($categories) { ?>
	<div class="filter">    	
    	 <div class="hgroup">
    	   <h2>Utvalg:</h2>
         <?php    	   
           if( !isset($_GET['v']) ) { echo '<strong>'; } else { echo ''; }
           echo '<a href="'. get_option('siteurl') . '/aktuelt/">Alle</a>';
           if( !isset($_GET['v']) ) { echo '</strong>'; } else { echo ''; };    	 
         ?>
         <ul>
    	 </div>
    	 <ul>
       <?php
        foreach($categories as $category) {
				echo '<li>';
				if( $_GET['v'] == $category->category_nicename ) { echo '<strong>'; } else { echo ''; }
				echo '<a href="'. get_option('siteurl') . '/aktuelt/?v=' . $category->category_nicename;
				echo '" title="' . sprintf( __( "Filtrer %s" ), $category->cat_name ) . '" ' . '>' . $category->cat_name.'</a></li>';
				if( $_GET['v'] == $category->category_nicename ) { echo '</strong>'; } else { echo ''; }
			} ?>
        </ul>        
    </div>
<?php
}

function get_query_by_url($get_slug, $categories) {
	foreach($categories as $category) {
		if ( $category->category_nicename == $get_slug ) { $cat_id = $category->term_id; }
	}
	$query_arg = '&cat=' . $cat_id . ',-' . get_cat_id('Temabeskrivelse') . ',-' . get_cat_id('Debattinnlegg');
	return query_posts($query_arg);
	
}

function get_post_thumbnail_url($post_thumbnail) {
	$pieces = explode(" ", $post_thumbnail);
	$i='';
	foreach($pieces as $piece){
		if($i==3) { // find 'src'-piece inside the string
			$piece = substr($piece, 0, -1); // remove the last '"'
			$piece = substr($piece, 5); // remove 'src="'
			$thumbnail_url = $piece;
		}
		$i++;
	}
	return $thumbnail_url;
}

function get_header_image_src() {
	if ( is_single() ) {
		echo '<link rel="image_src" href="';
		if ( (function_exists('has_post_thumbnail') ) && ( has_post_thumbnail() ) ) {
			global $post;
			echo get_post_thumbnail_url( get_the_post_thumbnail( $post->ID, 'thumbnail' ) );
		} else {
			echo bloginfo('stylesheet_directory');
			echo '/resource/image/sfa-logo-sidebar.gif';
		}
		echo '" />' . "\n";
	}
}

?>
