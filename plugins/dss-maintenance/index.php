<?php
/*

Plugin Name: DSS Maintenance
Plugin URI: http://metronet.no/
Description: Maitenance mode for DSS network
Author: Ryan Hellyer / Metronet
Version: 1.0
Author URI: http://metronet.no/

Copyright (c) 2013 Ryan Hellyer / Metronet


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


add_action( 'template_redirect', 'dss_maintenance_page' );

function dss_maintenance_page() {
	if ( is_user_logged_in() ) {
		return;
	}
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" lang="nb-NO">
<![endif]-->
<!--[if IE 7]>
<html id="ie7" lang="nb-NO">
<![endif]-->
<!--[if IE 8]>
<html id="ie8" lang="nb-NO">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="nb-NO">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Maintenance</title>
<link rel="stylesheet" type="text/css" media="all" href="https://blogg.regjeringen.no/wp-content/themes/dss-framework/style.css" />
<!--[if lt IE 9]>
<script src="https://blogg.regjeringen.no/wp-content/themes/dss-framework/js/html5.js" type="text/javascript"></script>
<![endif]-->
</head>
<body>
<div id="page" class="hfeed">
	<header id="branding" role="banner">
		<a href="http://blogg.regjeringen.no/">
			<img id="logo" src="https://blogg.regjeringen.no/wp-content/themes/dss-framework/images/logo.png" alt="Regjeringen logo" />
		</a>
		<div id="header-content">
			<h1 id="site-title">Bloggen er utilgjengelig akkurat nå</h1>
		</div>
	</header><!-- #branding -->
	<footer id="colophon" role="contentinfo">
			<div id="site-generator">
				<a href="http://blogg.regjeringen.no/" title="Website by DSS">Website by DSS</a>
			</div>
	</footer><!-- #colophon -->
</div><!-- #page -->
		
</body>
</html><?php
	die;
}
