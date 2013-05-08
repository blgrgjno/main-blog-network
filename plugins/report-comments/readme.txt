=== AJAX Report Comments ===
Contributors: tierrainnovation
Donate link: http://tierra-innovation.com/wordpress-cms/plugins/report-comments/
Tags: comments,commenting,spam,report,notifications,notify
Requires at least: 2.9
Tested up to: 3.0-alpha
Stable tag: trunk

*** Please Note: If you have installed v.1, please deactivate, install, and then reactivate the plugin. ***

AJAX Report Comments is a simple yet powerful add-on for any Wordpress blog, particularly larger blogs with a higher volume of user comments. It provides blog visitors the ability to report an inappropriate comment to the blog's moderator with a single click using AJAX and email.

== Description ==

*** Please Note: If you have installed v.1, please deactivate, install, and then reactivate the plugin. ***

AJAX Report Comments is a simple yet powerful add-on for any Wordpress blog, particularly larger blogs with a higher volume of user comments. It provides blog visitors the ability to report an inappropriate comment to the blog's moderator with a single click using AJAX and email.

Through the Wordpress Admin you can modify many aspects of the plugin including the text of the "Report Comment" link, the resulting "Thank you" message, the email address to send the inappropriate comment to and the layout and content of the generated email itself. You can even modify the HTML surrounding the "Report Comment" link to suit your needs.

After a comment is reported, the email address you specify will receive a message including the text of the reported comment and a link to view the comment on the site. The next version of this plugin will include more admin options and variety of moderation options, such as placing the comment in a moderation queue if more than X number of visitors flag it as inappropriate.
	
== Changelog ==

= 2.0.3 = 

1. Fixed a layout issue where the footer appeared on top of the list of comments in some cases.
1. Resolved issue where multiple reports for the same comment from the same computer were not blocked.
1. Fixed an issue where the wrong IP address was displayed on the moderation screen under Commenter's IP

= 2.0.2 = 

1. Fixing syntax error in report-comments.php

= 2.0.1 = 

1. Fixing conflict with 'Role Scoper' plugin where pluggable.php was not required on plugin forward facing pages.

= 2.0 = 

1. Reported comments are available in a moderation system inside WP for site admins / moderators.
1. Added dashboard integration for moderation panel.
1. Moved plugin to own widget navigation.
1. Once a flagged comment is approved by a site administrator / moderator, it cannot be flagged again by front end users.
1. Can view a report on a single comment to see how many times it has been reported as well as details of original comment poster and users who flagged the comment.
1. Admins can set a threshold value where if a comment is reported more times then the number specified, it automatically hides from the site's front end.
1. Enable / Disable reported comment reason box where users can give a reason for reporting a comment.
1. Enable / Disable email moderator function.

== Installation ==

1. Download and unzip the above file
1. Upload the entire report-comments directory to your plugins directory
1. Activate the plugin from the WordPress admin panel
1. Configure the options under the admin panel: Options Ð AJAX Report Comments

== Screenshots ==

**[View Screen Shots](http://tierra-innovation.com/wordpress-cms/plugins/report-comments/)**

== Frequently Asked Questions ==

= Once the link is clicked, what happens? =

The email address that is on file within the plugin is sent a notification of a reported comment.  From there, that site administrator / editor can go and review the comment and handle it appropriately.  Need more help?  **[Support](http://tierra-innovation.com/wordpress-cms/plugins/report-comments/)**