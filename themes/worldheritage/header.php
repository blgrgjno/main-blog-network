<?php
/**
 * The header
 *
 * @package WordPress
 * @subpackage World Heritage
 * @since World Heritage 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title><?php wp_title(); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header>
	<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to navigation', 'worldheritage' ); ?>"><?php _e( 'Skip to navigation', 'worldheritage' ); ?></a>
	<h1><a href="<?php echo home_url(); ?>"><?php bloginfo( 'title' ); ?></a></h1>
</header>

<div id="content">
