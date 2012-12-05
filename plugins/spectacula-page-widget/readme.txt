=== Plugin Name ===
Contributors: interconnectit, spectacula
Donate link: https://spectacu.la/signup/signup.php
Tags: widget, page, sidebar, plugin
Requires at least: 2.8.0
Tested up to: 3.0
Stable tag: 1.0.7

Widget that lets you output the content of a page in any place that'll accept a widget and allows you to hide said page from navigation (wp_list_pages).

== Description ==

With this page widget you can create a page in the normal WordPress editor and then drop it in any widget space - you can even embed shortcodes, videos and so on, all from the usual visual editor, rather than having to type HTML into a text widget.

And you can even control the widget to a significant degree, meaning it can be hidden and revealed as and when you require it.

This makes for a much easier to use process and has proven hugely popular with our users for the creation of profiles, sidebar videos and more.

And you no longer need admin access to be able to edit sidebar content belonging to you!

In a few rare cases, with poorly coded CSS in themes you may run into problems, so you should test this widget with your theme, across several browsers, before using it on a critical live site.

The widget will not show up on the page that is chosen to show in the widget. So if you click through to the page the widget will disappear from the sidebar.

If you want to translate the widget interface the files need for that are held in a sub-folder called lang.
Just copy the spec-page-widget-en-US.po file to match your language (spec-page-widget-xx-XX.po) then load it up in poedit and change what you need to change.

If you create a translation and you'd like your language file included with the plugin contact us at [Spectacu.la](http://spectacu.la/) and we'll see about adding it.


== Installation ==

## The install ##
1. Upload `spec-page-widget.php` and lang/*.* to `/wp-content/plugins/spec-page-widget/` or `/wp-content/mu-plugins/` directory. If the directory doesn't exist then create it.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You should now see the widget show up under 'widgets' menu. Drop that widget into a sidebar.
4. With the widget in the sidebar you should see the config for this widget.

## The config ##
1.  First select the page you'd like to show.
2.  If you want a title to show tick the show title option. This makes two more options available, 'link title' and 'alternative title'. If you want to link to source or use another title enter them in this new area.
3.  Next we have an option to use an excerpt rather than the full content.
    By default Wordpress won't let you create an excerpt for pages so one will be generated unless you've used another plugin to create an excerpt in which case that'll be used.
4.  Hide the page from the normal Wordpress list of pages.
    This lets you to remove links to that page from other parts of Wordpress that use the wp\_list\_pages() call.
    If you have two of these widgets both calling the same page then hiding the page in one will hide it in all.
5.  Finally hit save.

== Changelog ==

= 1.0.7 =
*	Added an option to the widget interface to allow you to add extra CSS classes to the widget.

= 1.0.6 =
*	Found a problem with some of my logic that resulted in the widget not showing up when it would otherwise be expected to. Fixed it.

= 1.0.5 =
*	Very minor change to bypass a problem I had where a page_id is passed to register_sidebar as part of another plug-in I'm working on and thus interrupts my page_id for this plug-in.

= 1.0.4 =
*	Fixed issue with wp_list_pages_excludes not respecting other plug-ins wishes.

= 1.0.3 =
*	Corrected links and improved description text.

= 1.0.2 =
*	Added an option to show the widget in the sidebar even if you're on the page selected to show in the widget. Made the widget title, as showing in the widget admin, a little more useful.

= 1.0.1 =
*	Added an option to include a clear block at the end of the content. Helpful when you have a page with some floated elements in it and quite short content.

= 1.0.0 =
*   Initial public release
*   Added a more user friendly interface on the widget
*   Added the option to hide the widget from wp_list_pages


== Frequently Asked Questions ==
= Why not just use a text widget? =
Because using pages gives you the visual editor, revisions, short codes, easier management and numerous other benefits.

= What's so important about shortcodes? =
Shortcodes are increasingly important in WordPress and are used to output certain types of content, such as galleries and video, but also by a lot of plugins which don't have widget functionality but which could effectively be widgetised by using this plugin.

= What does removing from wp_list_pages mean? =
That's the name of a WordPress function that lists all pages in your blog.  It's used by theme developers to build menus and navigation.  Our plugin can remove a page from this list, but it can't remove a page from navigation that's built using an alternative method - such as the new WP 3.0 menu builder, for example.

= I used this plugin to output a video in my sidebar, but it doesn't fit the space! =
Make sure that any content you place in a page to be output in the sidebar is small enough to fit in the sidebar - you may need to resize images and video embeds expressly for this purpose.

= I'm outputting from a plugin using a shortcode, but it looks wrong! =
Sometimes a plugin can't output items in a space as small as most widget areas or sidebars, so in this case you may need to restyle the plugin's output, or try a different plugin.  There's only so much we can do here.  Sorry!

= My theme's gone all screwy.  Help! =
A very small number of themes can't handle the output from this plugin very well.  Try the option to add a clear block at the end of your content - that may help in some cases.


== Screenshots ==

== Upgrade Notice ==

= 1.0.7 =
Added an option to the widget interface to allow you to add extra CSS classes to the widget. [JRW]

= 1.0.6 =
Found a problem with some of my logic that resulted in the widget not showing up when it would otherwise be expected to. Fixed it. [JRW]

= 1.0.5 =
Very minor change to bypass a problem I had where a page_id is passed to register_sidebar as part of another plug-in I'm working on and thus interrupts my page_id for this plug-in. [JRW]
This problem won't show itself under most situations, no really need to upgrade unless you're a bit bored.

= 1.0.4 =
Fixed issue with wp_list_pages_excludes not respecting other plug-ins wishes. [JRW]

= 1.0.3 =
Corrections to readme file and description, fixes two links. [DC]

= 1.0.2 =
Added an option to show the widget in the sidebar even if you're on the page selected to show in the widget. Made the widget title, as showing in the widget admin, a little more useful. [JRW]

= 1.0.1=
Extra option added to show the widget even when viewing the page selected to show in the widget. Sorted out the widget title as shown in the sidebar admin. [JRW]

= 1.0.1=
If you're having problems with floated content overflowing other items then upgrade to this and tick the option to have the clear block on. Otherwise this change isn't critical. [JRW]

= 1.0.0 =
If you have an older version of this I'd recommend you upgrade. [JRW]
