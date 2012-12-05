<?php
/**
 * @package sfa
 * @subpackage sfa_Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title><?php wp_title('-', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <link rel="image_src" href="<?php echo bloginfo('stylesheet_directory') . '/resource/image/sfa-logo-sidebar.gif'; ?>"/>
    <meta name="description" content="<?php echo the_excerpt_rss()?>" />
    <meta name="viewport" content="width=320; user-scalable=false" />
    <link rel="stylesheet" type="text/css" media="all"  href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>libraries.css"/>
    <link rel="stylesheet" type="text/css" media="all"  href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>template.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>grids.css"/>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>print.css" media="print" />
    <link rel="stylesheet" type="text/css" href="<?php echo bloginfo('stylesheet_directory') . '/resource/css/'; ?>mobile.css" media="handheld" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php //if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
    <?php wp_head(); ?>
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RDF/RSS 1.0 Feed" href="<?php bloginfo('rdf_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RSS 0.92 Feed" href="<?php bloginfo('rss_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; RSS 2.0 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="Samarbeid for arbeid &raquo; Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
    
    <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory') . '/resource/javascript/'; ?>init.js"></script>
  </head>
  <body>
 
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


    <div id="wrapper" class="page-404">      
      <a class="accessibilityHidden" href="#section-main">Gå til hovedtekst</a>
      <div id="header">      
        <div id="portalBranding" class="line">
          <div class="unit size1of2">
            <a id="governmentHomepage" href="/"><span class="accessibilityHidden">Gå til regjeringens nettsider</span></a>            
          </div>
          <div class="unit size1of2 lastUnit" style="float:right; width: 255px;">
          	 <h1 id="siteTitle">
                <a href="<?php echo get_option('home'); ?>/"><span class="accessibilityHidden"><?php bloginfo('name'); ?></span></a>
              </h1>
          </div>                 
        </div>        
      </div> <!-- end header page sfa -->


    <div id="left-side-outer">
      <div id="left-side">
        <div class="helper-top shadowborder"></div>
        <div class="helper-bottom shadow"></div>        
      </div>
    </div> <!-- end left-side-outer -->
    
    <div id="center-column">
    
      <div>      
        <img src="<?php echo bloginfo('stylesheet_directory') . '/resource/image/'; ?>404.jpg"/>
      </div> <!-- end section-featured -->
      
      <div class="article layoutSidebar">
        <div class="line">
          <div class="unit size2of3">    
            <div id="section-main">
              <!-- center column content goes here -->
              
              <h1>Oops! - Gikk du inn feil dør?</h1>
              <p class="shortintro">Beklager, men siden du leter etter finnes ikke her.</p>
              <p class="text">Gå inn på <a href="http://samarbeidforarbeid.regjeringen.no/">http://samarbeidforarbeid.regjeringen.no</a></p>

            </div>
            <div id="section-main-bottom">
              <!-- plugins go here -->
            </div>
          </div>        
          <div class="unit size1of3 lastUnit">
            <div id="section-contextual">           
              <!-- sidebar content goes here -->         
              <div class="mod linklist">
                <div class="inner">
                  <div class="hd"><h5>Her finner du informasjon om de ulike temaene under Samarbeid for arbeid:</h5></div>
                  <div class="bd">
                    <ul>
                      <?php the_navigation_sfa(); ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>        
        </div>      
      </div>  
      
      
    <?php get_footer(); ?>
