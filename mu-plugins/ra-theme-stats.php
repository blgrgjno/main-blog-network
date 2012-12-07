<?php
/*
Plugin Name: Wordpress Network Theme Stats
Plugin URI: http://wpmututorials.com/
Description: Adds submenu to see theme stats, shows themes by user and most popular themes.
Version: 2.8.2
Author: Ron Rennick
Author URI: http://ronandandrea.com/

(original plugin contributions by Phillip Studinski)
*/
/* Copyright:	(C) 2009 Ron Rennick, All rights reserved.  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
class RA_Theme_Stats {
	function RA_Theme_Stats() {
		if( function_exists( 'is_network_admin' ) )
			add_action( 'network_admin_menu', array( &$this, 'add_network_page' ) );
		else
			add_action( 'admin_menu', array( &$this, 'add_page' ) );
	}
	function add_page() {
		if( is_super_admin() ) {
			add_submenu_page('ms-admin.php', 'Theme Stats', 'Theme Stats', 'manage_network_themes', 'ra_theme_stats', array( &$this, 'admin_page' ) );
			if( $_GET['page'] == 'ra_theme_stats' )
				add_action( 'admin_head', array( &$this, 'show_hide_css' ) );
		}
	}
	function add_network_page() {
		add_submenu_page('themes.php', 'Theme Stats', 'Theme Stats', 'manage_network_themes', 'ra_theme_stats', array( &$this, 'admin_page' ) );
		if( $_GET['page'] == 'ra_theme_stats' )
			add_action( 'admin_head', array( &$this, 'show_hide_css' ) );
	}

	function admin_page() {
		if( !current_user_can( 'manage_network_themes' ) ) {
			wp_die( 'You don\'t have permissions to use this page.' );
		} ?>
	<div class=wrap>
	      <h2>Theme Statistics</h2>
	<?php
		global $wpdb, $current_site;
		$blogs  = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs " .
			"WHERE site_id = {$current_site->id} ORDER BY domain ASC");
		$blogtheme = array();
		if ($blogs) {
			foreach ($blogs as $blog) {
				if( ( $blogtemplate = get_blog_option( $blog->blog_id, 'stylesheet' ) ) )
					$blogtheme[$blog->blog_id] = $blogtemplate;
			}
		}
		$themeinfo = array();
		$themeblogs = array();
		// do stats
		if($blogs) {
			foreach ($blogs as $blog) {
				if( !array_key_exists( $blogtheme[$blog->blog_id], $themeinfo ) ) {
					$themeinfo[$blogtheme[$blog->blog_id]] = 1;
					$themeblogs[$blogtheme[$blog->blog_id]] = array();
					$themeblogs[$blogtheme[$blog->blog_id]][0] = $blog;
				} else {
					$themeblogs[$blogtheme[$blog->blog_id]][$themeinfo[$blogtheme[$blog->blog_id]]] = $blog;
					$themeinfo[$blogtheme[$blog->blog_id]]++;
				}
			}
		}
		arsort($themeinfo);
		// show stats
		echo '<ul>';
		foreach( $themeinfo as $themename => $themecount ) {
			echo '<li>';
			$this->show_hide_begin($themename, "$themename ($themecount)", '','ul'); ?>
	<?php
			foreach($themeblogs[$themename] as $bloginfo) {
				$url = "http://" . $bloginfo->domain . $bloginfo->path;
				if($bloginfo->path == '/') {
					$domain = explode('.',$bloginfo->domain);
					$blogname = $domain[0];
				} else {
					$blogname = substr($bloginfo->path, 1, -1);
				}
				echo '<li><a href="' . $url . '">' . $blogname .  '</a> - <a href ="' . $url . "wp-admin/" . '">Backend</a></li>';
			} ?>
	<?php
			$this->show_hide_end('ul');
			echo '<br /></li>';
		} ?>
	</ul>
	<script type="text/javascript"><!--
		function ra_show(id, newclass)
		{
		  var el = document.getElementById(id);
		  if(el) {
		    if(newclass) {
			if(el.className==newclass) el.className="ra-hide";
			else el.className=newclass;
		    } else {
			if(el.className=="") el.className="ra-hide";
			else el.className="";
		    }
		  }
		}
	//--></script>
	</div><?php
	}

	function show_hide_css() { ?>
	<style type="text/css">.ra-hide { display:none; }</style>
	<?php }

	function show_hide_begin($HTMLid, $linktext = 'Expand/Collapse', $CSSclass = '', $tag = 'div') {
		$q = "'";
		if($HTMLid && $linktext) {
			echo '<a href="javascript:void(0)" onclick="ra_show('.$q.$HTMLid.$q.','.
			$q.$CSSclass.$q.')">'.$linktext.'</a>';
			echo '<'.$tag.' id="'.$HTMLid.'" class="ra-hide">';
		}
	}
	function show_hide_end($tag = 'div') {
		echo '</'.$tag.'>';
	}
}
$ra_theme_stats = new RA_Theme_Stats();