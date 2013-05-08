=== URL Shortener ===
Contributors: geraldyeo
Donate link: http://wiki.fusedthought.com/contribute/
Tags: url-shortener, short url, url, shortlink, shorten, shortener, tinyurl, yourls, is.gd, su.pr, bit.ly, goo.gl, qr code, qr, snipurl, snurl, sn.im, cl.lk, cl.gs, chilp.it, smoosh, smsh.me, unfake.it, awe.sm, social, tweet, twitter, Voizle, tynie
Requires at least: 2.7
Tested up to: 3.1.3
Stable tag: trunk

This plugin allows you to generate shortlinks for post/pages using URL Shorteners (e.g. Bit.ly, Su.pr, YOURLS, Goo.gl and many others).

== Description ==

[URL Shortener](http://www.fusedthought.com/downloads#url-shortener-wordpress-plugin "URL Shortener") allows you to generate shortlinks for post/pages using URL Shorteners (e.g. Bit.ly, Su.pr and many others), with a few additional features.

**Please check your settings when upgrading to Version 4.0 from previous versions prior to it.**



**What's New with 4.0**

* QR Code Support (using Google Chart API)
* Additional Shorteners (Goo.gl, dlvr.it, yourls) 
* Nice ID links with QR Code (i.e. http://your_site/123.qr)
* Version 4.0 features completely refactored code once again. Now includes classes which allows developers to easily extend the plugin.

**Features:**

* Automatic generation of a Short URL/Shortlinks
* *Cached Shortlink* - thus generated only once. 
* Choose to generate shortlinks using permalinks or the posts ID (e.g. http://your_site/index.php?p=123).
* Relatively extensive shortlink support
* *Action Hooks available* for other plugins to utilize generated shortlinks (From Ver 3.0 Onwards)
* Nice ID links - http://your_site/123 instead of http://your_site/index.php?p=123
* Shortcode support (Ver 3.1): Place [shortlink] in your article where you want to display the shortened url.
* Append a link to short URL below your post content (Ver 3.1.1)


Refer to the documentation/wiki page at http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin for more information (eg. installation guide and known issues etc).


**Services currently supported are:**

* goo.gl (beta)
* bit.ly
* tinyurl
* is.gd
* Su.pr
* snipurl / Snurl / Snipr / Sn.im / Cl.lk
* cl.gs
* chilp.it
* smsh (aka sm00sh)
* urli.nl
* unfake.it 
* awe.sm
* Voizle 
* Interdose API
* dlvr.it


**Suspended**

* Ping.fm (Will be suspended until they reopen their API)




**Available Template Tags**

On-demand shortening function:

`<?php fts_shorturl('http://www.google.com', 'bitly'); ?>`

To show the generated links::

`<?php fts_show_shorturl($post); ?>`

Or if WordPress 3.0:

`<?php the_shortlink(); ?>`

http://codex.wordpress.org/Function_Reference/the_shortlink


**Available hooks and filters**

*  fts_use_shortlink (Action Hook)
*  fts_filter_shortlink (Filter)


**Future Versions and on:**

*  More services/features can be added upon request (http://code.google.com/p/url-shortener-plugin/issues/list)
*  Do note that due to my increasing need to concentrate on my studies, and a lack of financial contribution from such plugin development, I cannot possibly accede to all requests. 


**Support via:**

*  http://wordpress.org/tags/url-shortener
*  Contact me via my website ( http://www.fusedthought.com/contact/ )
*  Please check the FAQ 

== Installation ==

1. Upload files to your `/wp-content/plugins/` directory (preserve sub-directory structure if applicable)
1. Activate the plugin through the 'Plugins' menu in WordPress

Or

1. Within your WordPress admin, go to plugins > add new
1. Search for "URL Shortener". 
1. Click Install Now for the plugin named "URL Shortener"


== Frequently Asked Questions ==

For the most updated list, please check: http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/faq


= Service Support Levels =

* As I am increasingly busy with college work, I've classified services into Tiers... 
* Problems with Tier 1 services will be dealt with faster than group 2 and so on)
* Do note that the providers for beta services may change their API anytime... Do report if there are any issues.

**Tier 1**

* goo.gl (beta)
* bit.ly
* tinyurl
* is.gd
* Su.pr
* snipurl / Snurl / Snipr / Sn.im / Cl.lk


**Tier 2**


**Tier 3**

* cl.gs
* chilp.it
* smsh (aka sm00sh)
* urli.nl
* unfake.it 
* awe.sm
* Voizle 
* Interdose API
* dlvr.it


= Depreciated =

* Digg (They have stopped the service)
* soso.bz (Service no longer available)
* Cuthut (Service no longer available)
* short.ie (Service not available)

= Will deprciated services come back? =
Normally it's only depreciated when the shortening service is down for a prolong period of time. However, if there is a request, I will definitely consider it.


= Known Issues =
http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/known-issues


== Screenshots ==

http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin

== Changelog ==

Expanded list can be found at: http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/release-history

= 4.0.2 =
* *BUGFIX* Temp remove of jzsc interface

= 4.0.1 =
* *BUGFIX* missed out colon in parent declaration in component files


= 4.0 =
* Code refactoring (Shortening portion completely rewritten)
* *ADDED* QR Code output
* *COMPABILITY* Check with WordPress 3.1 series
* *Bugfix / Feature Request* for several issues from the forums / bugtracker
* *FOR DEVELOPERS* Includes a option wrapper class
* *FOR DEVELOPERS* Includes refactored shortening class
* *FOR DEVELOPERS* Includes a shortening PHP Interface definition for plugging in new services


= 3.1.2 =
* *BUGFIX* Bug in the On-Demand Shortening function preventing key/user retrieval (http://wordpress.org/support/topic/plugin-url-shortener-bug-with-api-username-api-key-and-service)


= 3.1.1 =
* Minor tweak to Admin Interface / categorization of options
* Minor tweak to generation of Short URL when scheduled posts are published.
* Ability to append a link to short URL to bottom of post/page content.

= 3.1 =
* *BUGFIX* Compatibility with Plugins using Services_JSON (http://code.google.com/p/url-shortener-plugin/issues/detail?id=6)
* *UPDATED* class.FTShorten to version 2.3 (Support for interdose API - http://code.google.com/p/url-shortener-plugin/issues/detail?id=7)
* *REMOVED* u.nu service has been discountinued. Service thus removed.
* *ADDED* Shortcode [shortlink] support

= 3.0 =
* Plugin completely rewritten from gound up.
* *UPDATED* to use WordPress 3.0 Hooks
* *UPDATED* class.FTShorten to version 2.2 (with support for new services)
* *ADDED* support for Digg Short URLs
* *ADDED* Action hooks to easily add code to utilize shortlink after generation
* *ADDED* option to use permalinks or Post/Page ID to generate shortlinks
* *REMOVED* services that no longer available.
* *REMOVED* wp_rewrite redirect
* Addon module support halted temporarily
* Admin Interface redesigned.

= 2.1.1 =
* Bugfix: Twitter User/Pass handling
* Bugfix: Cli.gs non-authenticated processing

= 2.1 =
* AJAX-ed entire Bulk Short URL code.
* Improved Security of Bulk Short URL Request.
* Included updated class.FTShorten (v2.0)
* Added support for auto-updating twitter (post name / url)

= 2.0.1 =
* Bugfix: Minor error posting to ping.fm

= 2.0 =
* Short URLs now generated using Post/Page IDs instead of Permalinks ensuring correct redirection even if post title/permalink changes.
* Bugfix: Blank screen when Shorten2Ping plugin is also activated. If Shorten2Ping is activated, URL Shortener will now detect and use the URL generated by Shorten2Ping. (http://code.google.com/p/url-shortener-plugin/issues/detail?id=2)
* Added support (via Addon Module) for Digg
* Added support request (via Addon Module) for Voizle (http://code.google.com/p/url-shortener-plugin/issues/detail?id=1)
* Added ability to generate and delete Short URL in bulk.
* Ported the URL Generation functions into a class, allowing re-use in other plugins.
* Sn.im service is found to be down and marked to be removed in the next version of this plugin.

= 1.7 =

* Included an Addon module option
* Addon functions for additional service display
* Added support (via Addon Module) for Goo.gl
* Directory structure cleanup


= 1.6.3 =
* WordPress 2.9 Compatibility check.
* If WordPress.com stats plugin enabled, "Show Short URL" button in edit page beside "view" is removed.

= 1.6.2 =
* Added prefix choosing support for Sn.im / Snipr / Snipurl / Snurl

= 1.6.1 =
* Bugfix: future/scheduled posts not generating Short URL.

= 1.6 =
* Added support for Awe.sm (user request)
* Changed URL Generation method hook for future/scheduled posts

= 1.5.2 =
* Bugfix: Pingfm key not saving.

= 1.5.1 =
* Bugfix: Short URL generated was the same as post URL

= 1.5 =
* Added on-demand shortening function: fts_shorturl()
* Added supported for ping.fm, chilp.it, short.to, sm00sh, u.nu, unfake.it  
* Added personal shortening service using post id (http://yoursite/POST-ID)
* Added Prefix option for personal shortening service (http://yoursite/prefix/POST-ID)
* Added template redirection and WP_Rewrite redirection methods
* Updated administration options page

= 1.4 =
* First Public Release
* Added simple validation to options page

= 1.3 =
* Added support for snipurl, cl.gs, Short.ie

= 1.2 =
* Added support for su.pr

= 1.1 =	
* Added support for bit.ly, tr.im
* Added "Remove buttons" in post/page edit.
* Added option for automatic shorturl generation.
* Changed Custom Field name from fts_shorturl to shorturl

= 1.0 =
* Initial Private release.
* supports TinyURL, is.gd

== Upgrade Notice ==

For those upgrading from a version prior to 4.0, please check your settings as Version 4.0 has options code that was re-written.

Read More: http://wiki.fusedthought.com/docs/url-shortener-wordpress-plugin/upgrade-notes
