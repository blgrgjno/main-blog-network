<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */governmentHomepage
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php wp_title( '' ); ?></title>
    <link rel="shortcut icon" href="<?php echo bloginfo('stylesheet_directory') . '/favicon.ico'; ?>" />
    <?php get_header_image_src(); ?>
    <?php 
      if(is_front_page()){
        echo '<meta name="description" content="Bli med i debatten på samarbeid for arbeid du også!" />';        
      }
      else{
        ?>        
        <meta name="description" content="<?php echo the_excerpt_rss()?>" />
      <?php
     } 
    ?>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>print.css" media="print" />    
    <?php //if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
    <?php wp_head(); ?>
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RDF/RSS 1.0 Feed" href="<?php bloginfo('rdf_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RSS 0.92 Feed" href="<?php bloginfo('rss_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RSS 2.0 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; Atom Feed" href="<?php bloginfo('atom_url'); ?>" />    
    <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory') . '/resource/javascript/'; ?>init.js"></script>
  </head>
  <body <?php body_class(); ?>>
 
<!-- wp_user_panel -->
<?php if ( is_user_logged_in() ){
	global $current_user;
	get_currentuserinfo();
?>
<div id="global_admin_panel">
  <div class="page">
	<a class="wp_first_item" href="<?php echo get_option('siteurl') . '/wp-admin/' ?>">Kontrollpanel</a>
    <div class="wp_info">
    <p>Hei, <a href="<?php echo get_option('siteurl') . '/wp-admin/profile.php' ?>" title="Endre profilen din"><?php echo($current_user->display_name); ?></a>
    | <a href="<?php echo get_option('siteurl') . '/wp-admin/post-new.php' ?>" title="Legg til nytt innlegg">Legg til nytt innlegg</a>
    | <a title="Logg ut" href="<?php echo wp_logout_url(); ?>">Logg ut</a></p>
    </div>
  </div>
</div>
<div class="global_admin_panel_line"></div>
<?php } /* end is_user_logged_in */
?>


    <div id="wrapper" class="<?php echo $_COOKIE['bodySize']?>">
      <a class="accessibilityHidden" href="#primaryNav">Gå til temameny</a>
      <a class="accessibilityHidden" href="#section-featured">Gå til toppsaker</a>
      <a class="accessibilityHidden" href="#section-main">Gå til hovedtekst</a>
      <div id="header" class="sfa">  
        <div id="portalBranding" class="line">
          <div class="unit size1of2">
          <a id="governmentHomepage" href="http://regjeringen.no/"><span class="accessibilityHidden">Gå til regjeringens nettsider</span></a>            
          </div>
          <div class="unit size1of2 lastUnit">
            <div id="portal-search-container">
          	<?php get_search_form_sfa(FALSE); // (show searchstring) ?>
          	<ul class="navList textSizeSelector">
          	 <li class="sizeDefault">
          	   <a title="Normal tekst" href="<?php echo home_url(); ?>/setsize/?bodySize=sizeDefault">A</a>
             </li>
             <li class="sizeLarge">
              <a title="Medium tekst" href="<?php echo home_url(); ?>/setsize/?bodySize=sizeLarge">A</a>
             </li>
             <li class="sizeVeryLarge">
              <a title="Stor tekst" href="<?php echo home_url(); ?>/setsize/?bodySize=sizeVeryLarge">A</a>
             </li>
            </ul>
            </div>
          </div>                 
        </div> 
        
        <div id="portalTopTools">
          <div class="line">
            <div class="unit size3of5">
              <div id="secondaryNav">
                <ul class="navList with-divider">
                  <?php wp_list_pages('depth=0&title_li=&sort_column=menu_order&include=45,2684,8,112'); ?>
                </ul>              
              </div>
            </div>
            <div class="unit size2of5 lastUnit">
              <h1 id="siteTitle">
                <a href="<?php echo get_option('home'); ?>/"><span class="accessibilityHidden"><?php bloginfo('name'); ?></span></a>
              </h1>              
            </div>
          </div>        
        </div> <!-- end portalTopTools line -->        
      </div> <!-- end header page sfa -->


    <div id="left-side-outer">
      <div id="left-side">
        <div class="helper-top shadowborder"></div>
        <div class="helper-bottom shadow"></div>        
      </div>
    </div> <!-- end left-side-outer -->
    
    <div id="center-column">
      
    
      
    
      <div id="primaryNav">
 
        <h2><span>Tema:</span></h2>
        <ul>
            <?php the_navigation_sfa(); ?>
        </ul>

      </div> <!-- end primaryNav --> 
