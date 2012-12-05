=== Gurken Subscribe to Comments ===
Tags: comments, subscription, email, opt-in, abmahnung, kommentar, kommentar
benachrichtigung, benachrichtigung, double opt-in
Contributors: infogurke
Donate link: http://www.infogurke.de
Requires at least: 2.5
Tested up to: 3.0
Stable tag: 1.8

Subscribe to Comments with Double-Opt-In

== Description ==

Gurken StC is an extension of the version from Mark Jaquith (txfx.net). It
supports closed-loop authentication which means that you first have to confirm
your mail address, before you’re able to receive notifications about
subsequent comments.

Features:
*   All of the original Subscribe to Comments
*   Registration with Double-Opt-In
*   Multi-Language (English, German and Czech included)
*   You can define an own css file for the manager interface
*   Fixed many bugs that are still in the original plugin

== Changelog ==

= 1.8 =
* Added Czech translation (from Klimas, http://www.tourdebier.cz/francek)
* Changed the recipient of the confirmation email when you change an email
  address to the new address instead of the old address.

= 1.7 =
* This is only a bug fix release, you don't have to update if you don't have any issues.
* Fixed html syntax error (reported by Dieter Welzel, http://www.dieter-welzel.de/blog/)
* Fixed a E_NOTICE message (reported by Joe Hoyle, http://wordpress.org/support/topic/368772)

== Installation ==

1. Download & unpack into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: if your WordPress theme doesn't have the comment_form hook, or if you would like to manually determine where in your comments form the subscribe checkbox appears, enter this where you would like it: `<?php show_subscription_checkbox(); ?>`
4. Optional: If you would like to enable users to subscribe to comments without having to first leave a comment, place this somewhere in your template, but make sure it is **outside the comments form**.  A good place would be right after the ending `</form>` tag for the comments form: `<?php show_manual_subscription_form(); ?>`

== Frequently Asked Questions ==

Be free to ask ;-)

