=== Instagrate Pro ===
Contributors: polevaultweb
Plugin URI: http://www.instagrate.co.uk/
Author URI: http://www.polevaultweb.com/
Tags: instagram, posts, integration, automatic, post, wordpress, posting, images
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.1.3

The best plugin to automatically integrate Instagram images with WordPress.

== Description ==

Instagrate Pro is a powerful WordPress plugin that allows you to automatically integrate Instagram images into your WordPress site.

== Installation ==

This section describes how to install the plugin and get it working.

1. Delete any existing `instagrate-pro` folder from the `/wp-content/plugins/` directory.
2. Upload `instagrate-pro` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the Instagrate Pro menu item under the 'Settings' menu and connect your Instagram accounts and set the rest of configuration you want.

To update the plugin either use the automatic updater or manually follow these steps:

1. Deactivate the plugin through the 'Plugins' menu in WordPress.
2. Delete any existing `instagrate-pro` folder from the `/wp-content/plugins/` directory.
3. Upload `instagrate-pro` folder to the `/wp-content/plugins/` directory.
4. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 1.1.3 =

* Fix - Images table converted to utf8 for chinese character support
* Fix - Resetting posting status when plugin jumps out of main posting function

= 1.1.2 =

* Added - Specific position of image location in the same post or page. Using the shortcode [igp-image-position position="above"]
* Added - Instagram user profile image url as a template tag
* Added - Instagram image tags as a template tag
* Fix - Improvements added to stop occasional duplicates
* Fix - Location posting position fix
* Fix - Custom post meta now being set if not using template tags as meta value
* Fix - Strip emoticons from bio when authorising Instagram account
* Fix - Custom placeholder text for title
* Fix - Location distance bug
* Improvement - Admin UI tweaks
* Improvement - Debug data tweaks

= 1.1.1 =

* Fix - Custom taxonomy issues
* Fix - Scheduling issues
* Improvement - Manual posting image count update
* Fix - Removed Media from post type select as not relevant to plugin
* Fix - Case issues with hashtag filtering

= 1.1.0 =

* New - Duplicate accounts
* Fix - Remove accent characters from filename to stop saving errors for some users
* Fix - sslverify issues on some hosts
* Fix - API notice for issues with SSL certificate

= 1.0.5 =

* Fix - Catching WP error during Instagram connect
* Fix - Catching WP error during API check
* Fix - Image saving errors - % in filename
* Fix - Setting status on an individual image
* Fix - General bug fixes and interface improvements

= 1.0.4 =

* Improvement - Map size choice of px or %
* Fix - Image saving errors
* Fix - Tag and caption issues
* Fix - Stop non published accounts being posted by a schedule
* Fix - Map bug when adding a custom class
* Fix - Schedules deactivated and unistalled correctly
* Fix - API down notice for some users
* Fix - General bug fixes and interface improvements

= 1.0.3 =

* Fix - Featured images not working
* Fix - Duplicate galleries on group posts

= 1.0.2 =

* Fix - Map shortcode for maps created pre v1.0.1

= 1.0.1 =

* Major release with a change of admin interface
* New - WordPress 3.5 compatible
* New - Multisite compatible
* New - Location image posting
* New - Post another users photos
* Improvement - Post all photos, no limit. Ability to get all older images
* Improvement - When a new batch of images are posted, all images between the last one posted and the newest images are posted
* New - Performance issues fixed when using a large number of accounts
* New - View images for account in tne settings. Bulk and single control those to be posted or ignored. Easy to repost
* New - Custom featured image posts to use a selected image instead of one from Instagram.
* New - Full control of posting to taxonomies and setting image hashtags to a taxonomy not just tags. Support for custom post type taxonomies
* New - Google map setting for map style
* Improvement - Better scheduling. Set time and day of the schedule start
* Improvement - Images saved to media library now with image name as file name
* New - Multiple hashtag filtering on all types of images
* New - New post content system with lots of template tags. Full control of the post content created
* New - Add custom post meta with template tags
* New - Can choose the order of images posted to grouped post or same post
* New - Can choose the placement of new images when posting to same post

= 0.3.1 = 

* Fix - Reinstated post meta for location data.
* Fix - Custom post text with %%title%% now showing correctly if custom title was also set.

= 0.3 =

* New - Continual posting of images to the same post, page or custom post type.
* New - Image link option to open in new window.
* New - New template tag for Instagram image taken date - %%date%%.
* New - New template tag for Instagram location name for geotagged images - %%location%%.
* Improvement - Custom body text now allows HTML content.
* Improvement - Warning if cURL extension not installed. This is a prerequisite of the plugin.
* Fix - The plugin's settings are now only visible to administrators.
* Fix - Post is only published once image is set. This is a fix for users with auto social posting plugins who weren't seeing images in their social posts.
* Fix - Small warning in updater.
* Fix - Removed post meta for location data as this is stored in shortcode in the post body.

= 0.2.2 =

* New - New template tag for Instagram username - %%username%%
* New - Added better custom body text support for mulitple images in single posts.
* Fix - Removes error message if hashtagging selected but no tag inputted, or tag has no photos.
* Fix - Changed the way the Google Maps are saved and displayed using shortcodes to avoid stripping of HTML tags by WordPress.
* Misc - Amends code comment before post to remove links, leaving just plugin name and version for troubleshooting.

= 0.2.1 =

* Fix - Strips emojis but keeps foreign characters in the Instagram image title.
* Fix - Last image Id not updating correctly.
* Fix - Debug.txt was getting deleted on check to see if write permissions existed.

= 0.2 =

* New - Automatic plugin updates!
* New - Option to override is_home() check setting on automatic posting if themes do not have a set blog page.
* New - Option to allow duplicate posting of images by separate accounts. By default images will only ever get posted once.
* New - Accounts display info if the Instagram servers are down.
* Fix - Posting issues if Instagram server is down.
* Fix - PHP notices on has_cap and get_theme.
* Fix - Removed nested form bugs for browser compatibility.
* Fix - Checks for write permissions on debug.txt file and displays message if not writeable.

= 0.1.1 =

* Fix - extended schedules (weekly and longer) not being run.
* Fix - custom / default post title for grouped image posts.
* Fix - removed special characters from post titles.

= 0.1 =

* First release, bugs expected.

== Frequently Asked Questions ==

= I have an issue with the plugin =

Please visit the [Support Forum](http://www.polevaultweb.com/support/forum/instagrate-pro-plugin/) and see what has been raised before, if not raise a new topic.

== Disclaimer ==

This plugin uses the Instagram(tm) API and is not endorsed or certified by Instagram or Burbn, inc. All Instagram(tm) logoes and trademarks displayed on this website are property of Burbn, inc.