<?php
/*
Plugin Name: Shortcodes in Sidebar Widgets
Plugin URI: http://resultzdigital.com/wordpress-plugins/
Description: This plugin allows shortcodes to properly execute when they are placed in sidebars.  
Version: 1.1
Author: Marc Fuller
Author URI: http://resultzdigital.com/wordpress-plugins/
/*  Copyright 2011  Marc Fuller (email : marc@marcfuller.com)
     (Credit to English Mike for the function)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	// Shortcodes in sidebar (text) widgets
	//
	add_filter('widget_text', 'do_shortcode', 11);

?>