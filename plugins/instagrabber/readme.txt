=== Wordpress Instagrabber ===
Contributors: ferenyl, hypernode
Tags: Instagram importer, Instagram images, Instagram, instagram stream, Instagram flow, Instagram tags, Instagram embed, Instagram oEmbed
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 2.3.2
License: GPLv2 or later

Get images from Instagram and create posts automaticly and use multiple streams. Or just save images in your media library.

== Description ==

Get images from instagram with simple setup. Add multiple streams and get images by #tags or by users. This plugin does not require any signups or registrations (only a instragram account).

What this plugin can do for you:

* Display all your images from instagram.
* Only display your images with a certain tag (eg #instablog).
* Display images that you have liked.
* Publish images that contains a tag. This is ideal for special sites as "longboarding day" or "users makeup"
* Save images to media library instead of posting them.

You can choose to auto post or manually choose the images

This plugin supports:

* Custom Post types
* Post formats
* Taxonomies
* Tags

If you want to use cron without calling wordpress you can use the instagrabber-cron.php file. See settings page for more information. 

This plugin uses the Instagram(tm) API and is not endorsed or certified by Instagram or Instagram, Inc. All Instagram(tm) logos and trademarks displayed on this plugin are property of Instagram, Inc.

== Installation ==

1. Upload plugin folder to to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to settings page and follow instructions.
4. Create a stream
5. (optional) set up a automatic job for getting images (see under Other notes or on this link [Instagrabber Cron](http://johan-ahlback.com/?p=1163)).


== Screenshots ==

1. Instagrabber menu
2. Settings page
3. New stream
4. Streams
5. A stream

== Frequently Asked Questions ==

= I don't see any images =
Try to update the stream or check the stream settings.

= Where can i find help? =
There is a help page in WordPress when the plugin is activated. And there is a forum at WordPress.org.

= This plugin does not work = 
Feel free to contact me!

= What template functions can i use? =
There are several at the moment and if you want me to add one you can request it in the support forum. se the functions file to see them.

== Usage ==

= How to add a stream =

So now you will add a stream. This is a stream of images that follows a set of rules. There are two mayor types of streams, user streams and tag streams. A user stream only contains images from one user (the one who activates the stream) and a tag streams contains public images that contains a specific tag. Here is an explanation to the fields on the page:

* Name: the name of the stream
* Placeholder title for post: This will be the title. you can use these placeholders. %user% - the user that uploaded the image, %tag% - the tag for the stream and %caption% - tha caption for the image. So a title can look like this:  "%user%: %caption%". But when you publish the image it will look like this: "ferenyl: I love my job".
* Type: There are three different types now, user, tag and likes. The user type will get all images from a user, the tag type will get all images with a tag and likes type will get all images that a user has liked (the user is the one who has activated the stream).
* Tag: The tag to use in a tag stream. Or filter a user stream with a tag.
* Auto create post: Do you want to publish a image automaticly or do you want to do it manually?
* Post Type: Do you have more than one post type? choose a post type to save in.
* Post status: You can publish it right away or make it a draft if you want to edit them first.
* Post format: if your theme support post formats you can choose one here (Image is ideal).
* Taxonomy: Choose a taxonomy if you have more than one.
* Term: Choose a term in that taxonomy
* Taxonomy for tags: if you want to tag your image
* Auto set tag: Convert the image instagram tags to wordpress tags
* Attachment type: Choose how to attach the image to the post. Make sure your theme supports featured images before choosing that. Both is a great choice.

You will be directed to the stream page when you have clicked on save. You must authorize the stream before you can use it. The stream will be connected with the user who authorizes it.

== Changelog ==

= 2.3.2 =
* Fixed error when getting images.

= 2.3.1 =
* fixed uninstall
* deleted transient.

= 2.3 =
* Added fix button.

= 2.2.9 =
* Fixed database upgrade bug

= 2.2.8 =
* Fixed error with tags

= 2.2.7 =
* Changed unserialize to json_decode
* Added a random image option to widget.

= 2.2.6 =
* added custom link options to images
* fixed a bug in updating

= 2.2.5 =
* Fixed tags

= 2.2.3 =
* Bugfix

= 2.2.2 =

Fixed some bugs that was missed and added some features.
Big thanks to Martin Perreault for his feedback.

Here is the list:
* Fixed bug when trying to delete multiple streams
* Removed the every minute option for cron (to heavy for most servers, sorry)
* Image link was wrong when using small images. Should always point to the big image.
* Fixed stream auto update.
* Divided stream settings page into sections.
* Added support for setting your own tags.
* Added spinning spinner when fetching images.
* Fixed link in posts

= 2.2.1 =
* Fixed prepare error

= 2.2 =
You don't need to create a app on instagram anymore. You can use the built in option if you want. Just choose it on settings page. New users will have this as default.

* Fixed a error that interupted streams
* Fixed importing images. Images was imported multiple times.
* Fixed locks
* Added dashboard widget
* Added auth option for using a built in application so you don't have to create a app on instagram
* Added a date option that will be used the first time that you import images in a stream.
* Removed embed in favor for the built in embed in WordPress 3.5
* Changed creation of post to fire in the background so it wont slow down your experience


= 2.1.2 =
* added more error messages.

= 2.1.1 =
* Removed authorize from settingspage.

= 2.1=
* Added settings to choose link for image
* Added toolbar menu
* save more data from instagram: filter and location.
* Added lock when importing images to prevent a stream to import images twice
* added some more template functions. Developers can look in instagrabbber/includes/functions.php to see them

= 2.0 =
* Added oEmbed and shortcode see faq for more info
* Added setting for image size when saving in post
* Setting for allowing hastags in title
* added support for multiple administrating users for a stream with a default user

= 1.6.5 = 
* fixed some errors
* added filter for image size
* secrets: some 2.0 functions may have bleed trough....

= 1.6.3 =
* fixed errors

= 1.6.2 =
* added a new function for saving images in media library without having to save it as a post.
* added "save to media library" as a choise in stream setting and on stream page. (must be activated in settings)
* removes attachement id from image when a image is deleted from media library 

= 1.6.1 = 
* Fixes a problem when images are removed from instagram. The whole stream failed.

= 1.6 =

If you had problems with previous verions this should fix the errors. If the auo get images not working, go to settings page and reset the function. 

* fixed errors
* added a button to reset auto get images
* added a uninstall button

= 1.5.9 =
* bug fixes

= 1.5.8 =
* changed how wp cron uses the import function
* Fixed backup placeholder error
* fixed error message from intagram

= 1.5.7 =
* added function to get images by instagram image id
* added template functions

= 1.5.6 =
* changed functions to get images from curl to wp_http
* changed redirect function to use wp_http

= 1.5.5 =
* Fixed error with widget
* Changed function name

= 1.5.4 =
* Added a widget
* fixed db function

= 1.5.3 =
* fixed cron file

= 1.5.2 =
* Added support for publish and save as draft on stream page
* Changed 1 and 0 to yes and no in stream list.
* better structure in files
* Changed css on help page
* Added created by field at stream view
* Added extra placeholder as a backup if using %caption% placeholder and it's empty. If both empty use stream name.
* Added change user field
* Added a new placeholder: %date%

= 1.5.1 =
* Fixed unexpected output
* Added check before trying to create tables
* Added images to help page.

= 1.5 =
* changed max_tag_id to min_id for userstream. This is the correct way.
* Added like stream support
* Hide private images
* fix bug in auth function
* Added search in stream
* Added username in stream table
* Added images per page options in stream
* Do not import private images. Integrity is important!
* Added help page and help tab on pluginpage.
* Removed button from New stream page.

= 1.4 =
* Added support for post formats
* Fixed delete stream confirmation
* Change title on new stream page if editing a stream.

= 1.3 =
* added gettext
* Manual function for getting images

= 1.2 =
* Added the use of tags in user streams

= 1.1 = 
* Added checks for cron job. Mark as running and mark as done.

= 1.0 =
* Initial realese
