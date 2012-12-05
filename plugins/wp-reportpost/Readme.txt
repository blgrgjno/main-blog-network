=== WP-REPORTPOST ===
Contributors: Rajeevan
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=rajeevanuk%40gmail%2ecom&item_name=WP%20Plugin&no_shipping=0&no_note=1&tax=0&currency_code=GBP&lc=GB&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: broken link, broken, report, report post, post report, notify, inform, abuse, wp-report, error report
Requires at least: 2.8
Tested up to: 2.9.1
Stable tag: 1.2.4

Simple and powerful plugin to make your life easier, Report post plugin to automate links / contents error reporting in your website.

== Description ==

Simple but powerful plugin to help your website keeping clean and simple. This plugin allows visitors to report posts / pages for broken links, misleading / abusive contents, copyrights infringements and etc simply and fast.

You have the full control on what to be reported and the areas to be reported. Once installed and activated, you will have the full control over where to display and what to display.

> Why So many Updates? Well, when tested, me and friends helped me testing used freshly installed wordpress! When we made release people started complaining files missing and errors! I;m working to Fix these issues and It seems to be coming to and End. This would be the Last Bug Release update.

> ** : See Other Notes for more details **

> If you find this Plugin conflicting with any other, please do let me know. I will try and fix it asap.

This plugin uses jQuery / Stack to send Report using Ajax, this prevent pages from reloading when user submitting report. User will get a feedback to their report.

This Plugin is Ideal for wordpress users who have links to external websites and blogs has writers and authors (Contents Piracy / Abusive etc.)

== Features ==

1. Restrict Reporting Option to registered and logged in users only. 
2. Opt-out attaching Report Form to Pages.
3. Select Automatic Attachments of Report Form or Attach Report Form by selecting option in POST when writing Post or Manually by Editing Theme.
4. Choose Categories to attach Report Form to its posts. When there is none chosen, plugin will take attach to all categories posts.
5. Customizable and HTML enabled Link to Show (When user Click on this text Link, Form will be Shown using jQuery)
6. Specify options to be listed. These options will be shown as Dropdown box to select what kind of error reporting…
7. Enable Send Email Feature and Specify different Email address than Wordpress default admin email address to send email when a post / page reported. Email will be send once per post / pages. When some other user reports the same post again, plugin will only log it in Database and no email will be sent again.
8. Display Number of New Reports in bubble like wordpress comments in the Navigation.
9. Display Number of Reports in Dashboard “Right Now”
10. Once you reviewed report, you could archive it or delete it forever.
11. No page Refresh when Reporting post / page. All done through Ajax.

== Changelog ==

There have been many updates in the Past, I'm only going to specify the main changes made to archive this new version 1.2.
= 1.2.4 =
* Fixed: When excerpt is invoked; Report option Show up broken. Now Report option won't be attache to 'the_excerpt' 
= 1.2.3 =
Bug Fixes Only
= 1.2.2 =
* Fixed: Category listing seems to be Missing in some Wodpress? I have No idea why but created backup functions to Tackle this Issue.
* Fixed: Missing CSS & JS files (Entire Assets folder in SVN!)
* Fixed: Plugin made Wordpress ignore `<br /> and <p>` tags!
= 1.2.1 =
* Fixed : Ajax Error when Selected Manual [Theme] Attachment Option.

= 1.2 =
* Added Category Filtering
* Added Bubble Notification and Dashboard Notification
* Added Opt-out option Pages Reporting
* Added Registered Users only Report Option
* Added Moderation comment when Archiving Reports and Track User Archived
* Added and Updated Security to prevent Spam (Using WordPress Nonces, Hope this helps)
* Improved and More Flexible Version released
* Database Structure is changed to Support New Improved Features

= 1.1 & 1.1.1 =
* Bug Fix : Attaching Report Form to RSS Feeds (thanks to `Navjot Singh`)
* Updated Coding and Improved satiability 

= 1.0 =
* Added Archive Feature
* Added Report Reason Support
* Added Comments Optional Textarea
* Updated to use jQuery / Stack to work with Ajax Post method; this prevents pages from reloading on post back.

= 0.1 Beta =
* First release with very few and fixed options.

== Installation ==

This plug in is tested only in WordPress version 2.8; It does have Backward compatibility up to 2.6 only. I do not guarantee that it will work with older version than 2.8.

1. Copy the `wp-reportpost` to the `wp-contents/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use `Report` Menu link to Access `Settings` and Reports

* PHP 5 is REQUIRED to run this plugin; Core of this plugin is developed and tested in PHP 5 environment, therefore I recommend using PHP version Higher than 5. I would work with PHP4 but I didn't Test!

* This Plugin will create 3 Tables on your WordPress database when Activating First time (Don't worry, it's unique names and will not affect anything else). If WordPress can be installed on your MySql Version, this plugin will without any issues.

** : See Frequently Asked Questions For Manually Calling Report Form into your Theme **

== Frequently Asked Questions ==

Firstly, What is Report Form?
> Report Form is a Form with a Dropdown box and Textarea Input to collect website visitors reporting. This is a normal HTML form.

= Automatically Attach to All Posts =
> By Default, this plugin will attach this Report Form to all posts. There is nothing you need to do other than installing and activating this plugin. You may have to do little Settings to make sure report form showing how you want it and where you want it.

> You can also choose categories, if you choose a Category; That category and all its child category posts will have Report Form attached.

= Show Only for Selected Posts =
> You can select this Option in Reports > Settings page. This will add an Option to POST add / Edit page with a Check Box to select. If you select this Check Box only Report Form will be shown.  This is good if you want to have control over which posts to show and starting now. If you already have tons of posts, you will have to edit one by one (which is not practical).

= Manually By Editing Theme =
> This Option will Disable Automatic attach function and will give you Full control over Where to Attach the Report Form.

> Simply call this PHP function to attach form:

> `<?php wprp([echo]); ?>`

>`[echo] = True / False` : If you pass true, It will echo the Form, otherwise it will Return and you will have to echo it.

= Warning =	
> You should call this INSIDE the [loop](http://codex.wordpress.org/The_Loop "Learn more about The Loop"). Outside loop will cause only error. It `requires loop functions` to function properly.

Need more help? visit [plugin page](http://www.rjeevan.com/en/projects/wordpress/plugins/wp-reportpost "Plugin Page")

== Screenshots ==
1. What User See and Reporting
2. Reports Settings
3. No of reports Shown in Dashboard and Report Menu
4. Listing Of New Reports. Archive has something similar but there is no Archive button. This version also support Pagination (20 Records per page)
5. When you click on Details, Pop-in will open to Show you Full details. If you select an Archived report, Archive details such as Whom archived and comments also shown here.
6. If you are Updating an Old version of WP-REPORTPOST, When you go to Settings, You will See this message telling you to Update first.
7. When user try to Archive Record(s), this comment box shown to collect some remarks
8. Sample of Email
9. If you select `Only Selected Posts` in setting, this option will be added to POST add / edit page.