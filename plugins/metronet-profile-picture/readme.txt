=== Metronet Profile Picture ===
Contributors: metronet, ronalfy
Tags: users, user, user profile
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Set a custom profile image for a user using the standard WordPress media upload tool.
== Description ==

Set a custom profile image for a user using the standard WordPress media upload tool.  A template tag is supplied for outputting to a theme and the option to override a user's default avatar is also available.

== Installation ==

1. Upload `metronet-profile-picture` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php mt_profile_img() ?>` in your templates (arguments and usage are below)

Arguments: 

`/**
* mt_profile_img
* 
* Adds a profile image
*
@param $user_id INT - The user ID for the user to retrieve the image for
@ param $args mixed
	size - string || array (see get_the_post_thumbnail)
	attr - string || array (see get_the_post_thumbnail)
	echo - bool (true or false) - whether to echo the image or return it
*/
`

Example Usage:
`
$avatar = mt_profile_img( $user_id, array( 
			'size' => 'thumbnail'), 
			'attr' => array( 'alt' => 'Alternative Text' ), 
			'echo' => false )
		);
`

== Frequently Asked Questions ==

= How do you set a user profile image? =

1.  Visit the profile page you would like to edit.
2.  Click "Upload or Change Profile Picture"
3.  Upload a new image and select "Use as featured image", which will save the image (ignore the "Insert Into Post" button).

To override an avatar, select the "Override Avatar?" checkbox and save the profile page.

= How do I create specific thumbnail sizes? =

Since the plugin uses the native uploader, you'll have to make use of <a href='http://codex.wordpress.org/Function_Reference/add_image_size'>add_image_size</a> in your theme.  You can then call `mt_profile_img` and pass in the custom image size.

= The image is cropped wrong.  How do I fix this? = 

We highly recommend the <a href='http://wordpress.org/extend/plugins/post-thumbnail-editor/'>Post Thumbnail Editor</a> plugin for cropping thumbnails, as you can custom-crop various image sizes without affecting other images.

== Screenshots ==

1. Profile page options.
2. Media upload dialog

== Changelog ==

= 1.0.3 = 
* Bug fix:  Avatar classes in the comment section

= 1.0.2 =
* Bug fix:  Error being shown in comment section

= 1.0.1 =
* Bug fix:  Not able to "uncheck" Override Avatar.
* Bug fix:  Deleting profile image and not reverting to normal avatar.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
Several important bug fixes including the ability to uncheck the avatar override, and the behavior when someone deletes their profile picture.

= 1.0.0 =
Initial release.