<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title(); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" href="<?php echo home_url( '/' ); ?>wp-content/themes/dss-framework/style.css" />
<style>
/* =Footer styling
------------------------------------------------*/
.menu-footer li {
	list-style: none;
	display: inline;
	font-weight: normal;
	padding: 0 8px;
	border-right: 1px solid #888;
}
.menu-footer li:last-child {
	border-right: none;
}
.menu-footer li a {
	font-weight: normal !important;
}

/* =Add fonts
----------------------------------------------- */
@font-face {
	font-family: 'ScalaBold';
	src: url(<?php echo home_url( '/' ); ?>/wp-content/themes/dss-framework/fonts/scala-bold.eot);
	src: url(<?php echo home_url( '/' ); ?>/wp-content/themes/dss-framework/fonts/scala-bold.eot?#iefix) format('embedded-opentype'),
		 url(<?php echo home_url( '/' ); ?>/wp-content/themes/dss-framework/fonts/scala-bold.woff) format('woff'),
		 url(<?php echo home_url( '/' ); ?>/wp-content/themes/dss-framework/fonts/scala-bold.ttf) format('truetype'),
		 url(<?php echo home_url( '/' ); ?>/wp-content/themes/dss-framework/fonts/scala-bold.svg#ScalaBold) format('svg');
	font-weight: normal;
	font-style: normal;
}
</style>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="hfeed">
	<header id="branding" role="banner">
		<a href="<?php echo home_url( '/' ); ?>">
			<img id="logo" src="<?php echo home_url( '/' ); ?>wp-content/themes/dss-framework/images/logo.png" alt="Regjeringen logo" />
		</a>
		<div id="header-content">
			<a class="top-menu-link" href="http://regjeringen.no/">regjeringen.no</a>
			<h1 id="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'title' ); ?></a></h1>
			<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
		</div>
	</header><!-- #branding -->

	<div id="main">
		<div id="primary">
			<div id="content" role="main">
				<?php 
				$e404 = trim($_GET["e"]);
				if (!empty($e404) && strip_tags($e404) == '404' ) {
				?>
				<article id="404" style="background-color: #eeeeee;">
					<header class="entry-header">
						<hgroup>
							<h2 class="entry-title">Vi fant ikke siden du leter etter</h2>
						</hgroup>
					</header>
					<!--div class="entry-summary">
						<p>Her finner du en oversikt over Regjeringens og departementenes innspillsider og blogger.</p>
					</div-->
				</article>
				<?php 
				}
				?>
