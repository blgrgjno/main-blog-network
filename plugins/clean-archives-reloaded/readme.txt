=== Clean Archives Reloaded ===
Contributors: Viper007Bond
Donate link: http://www.viper007bond.com/donate/
Tags: archive, archives, posts
Requires at least: 2.5
Tested up to: 3.3.1
Stable tag: trunk

A slick, Javascript enhanced post archive list generator.

== Description ==

Clean Archives Reloaded generates a list of all of your posts, sorted by month. It's enhanced with Javascript to allow collapsing and expanding of months.

It's highly efficient and won't kill your server with tons of MySQL queries.

= Demo =

Check out one of my sites' [archive page](http://www.finalgear.com/post-archives/).

== Installation ==

###Updgrading From A Previous Version###

To upgrade from a previous version of this plugin, delete the entire folder and files from the previous version of the plugin and then follow the installation instructions below.

###Installing The Plugin###

Extract all files from the ZIP file, making sure to keep the file structure intact, and then upload it to `/wp-content/plugins/`.

This should result in the following file structure:

`- wp-content
    - plugins
        - clean-archives-reloaded
            | readme.txt
            | clean-archives-reloaded.php`

Then just visit your admin area and activate the plugin.

**See Also:** ["Installing Plugins" article on the WP Codex](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

###Using The Plugin###

Just create/edit a post or page and type `[cleanarchivesreloaded]` where you would like the archives list to show up. You can also use `[cartotalposts]` to show your total post count.

Example page contents:

`Here is all [cartotalposts] of my posts:

[cleanarchivesreloaded]`

Configure options via Settings -> Clean Archives.

== Frequently Asked Questions ==

= Does this plugin support other languages? =

Yes, it does. See the [WordPress Codex](http://codex.wordpress.org/Translating_WordPress) for details on how to make a translation file. Then just place the translation file, named `car-[value in wp-config].mo`, into the plugin's folder.

= I love your plugin! Can I donate to you? =

Sure! I do this in my free time and I appreciate all donations that I get. It makes me want to continue to update this plugin. You can find more details on [my donate page](http://www.viper007bond.com/donate/).

== Shortcode Tag Parameters ==

You can customize the list options on a per-call basis if you wish.

* `usejs` -- (`1` or `0`) use Javascript or not to collapse the months
* `monthorder` -- (`new` or `old`) show newest months or oldest months first
* `postorder` -- (`new` or `old`) show newest posts or oldest posts first within months

= Examples =

No Javascript:

`[cleanarchivesreloaded usejs="0"]`

Oldest months first, oldest posts first:

`[cleanarchivesreloaded monthorder="old" postorder="old"]`

== ChangeLog ==

**June 29th, 2011 (No Version Bump)**

* Added Irish translation files by Ray S.

**March 31st, 2011 (No Version Bump)**

* Added Portuguese (Brazil) translation files by Marcos Freire.

**Version 3.2.0**

* Use a direct database query instead of `get_posts()` to fetch just the data we need. I had wrongly been thinking doing this was going to be a lot more complicated to accomplish. This should help a lot with memory requirements.

**Version 3.1.10**

* Added German translation thanks to [Thomas F?rster](http://thomas-foerster.com/).

**Version 3.1.9**

* Try to avoid the Javascript getting outputted twice due to people installing the plugin twice.

**Version 3.1.8**

* Add Hungarian translation thanks to [Fodor Bence](http://fodi.be/). Sorry it took so long, Fodor!

**Version 3.1.7**

* Add Danish translation thanks to [Georg S. Adamsen](http://wordpress.blogos.dk/2009/03/25/enkel-oversigt/).
* Add Polish translation thanks to Marcin.

**Version 3.1.6**

* Add Italian translation thanks to Roberto.

**Version 3.1.5**

* Try to further reduce memory usage. This is a bandaid fix and an alternate generation method (multi-SQL queries) is needed.
* Add Russian translation thanks to DennisBri.
* Add Swedish translation thanks to Mikael Quick.
* Add Chinese translation thanks to Patrick.

**Version 3.1.4**

* Turn dynamic load off by default. It was causing people problems with certain themes (probably due to theme hacks).

**Version 3.1.3**

* Added French translation file thanks to Luc Saint-Elie.

**Version 3.1.2**

* Dynamic load improvements.
* Imrovements for WordPress 2.6 (i.e. a moved `wp-content` folder).
* Spanish translation file thanks to [albertjh](http://diariodeunlinux3ro.es/).

**Version 3.1.1**

* If dynamic load is enabled (by default it is now), then dynamically load jQuery as well.

**Version 3.1.0**

* Add option to settings page that, if enabled, will check the posts to be disabled for the shortcode. If they aren't found, the JS/CSS for the plugin won't be outputted.
* Add parameter to post list shortcode to hide post counts. Do `[cleanarchivesreloaded postcount="0"]` to hide the posts per month.
* Add parameter to post list shortcode to hide comment counts. Do `[cleanarchivesreloaded commentcount="0"]` to hide the comments per post.

**Version 3.0.2**

* Make shortcode work in the text widget
* Switch the way posts are sorted within months to avoid an issue that would occur in the unlikely even that two posts had an identical post date.
* Change the default sort order for posts within months to newest posts first.
* Made language files be loaded from a subfolder for a cleaner plugin folder and renamed the plugin's language domain from "car" to "clean-archives-reloaded".
* Added Turkish translation by Baris Unver.

**Version 3.0.1**

* Fix `get_posts()` for WordPress 2.6
* jQuery improvements (use `find()` on a CSS class instead of `children()` by structure)

**Version 3.0.0**

* Recoded (mostly) from scratch once again. Now requires WordPress 2.5+ as it uses the shortcode functions.
* Greatly increased efficiency. Page generation times dropped 10% on my localhost development blog.
* Options page -- no more editing the file to configure options.
* If you have multiple listings per page (no clue why you would, but hey), the Expand/Collapse All links will only apply to it's own listing rather than all listings on the page.
* You can override the individual options set on the options page via the shortcode tag (see above).

**Version 2.0.0**

* Completely recoded from scratch. It now relies on no manual SQL queries which should make it future proof. It also no longer requires any internal caching as the plugin is queryless.

**Version 1.0.1**

* Forgot to tell WordPress to regenerate the cache when comments are made and deleted.

**Version 1.0.0**

* Initial release of my edition of the plugin.