=== Subscribe Me ===
Contributors: Denis-de-Bernardy
Donate link: http://www.semiologic.com/partners/
Tags: subscribe-me, feed, rss, atom, subscribe-button, subscribe, semiologic
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: trunk

Adds widgets that let you display subscribe links to RSS readers such as Google Reader.


== Description ==

The Subscribe Me plugin will add buttons that let your visitors share your content on [social media sites](http://www.semiologic.com/resources/blogging/help-with-feeds/) such as Bloglines or Google Reader.

Hovering the big RSS buttons will reveal the subscription services. Only major services are included, alongside a Desktop subscription link for those who have the relevant software.

Users of themes that do not support widgets will need to add the following call in their template:

    <php the_subscribe_links(); ?>

The call accepts an optional argument, which sets the widget's title.

= Help Me! =

The [Semiologic forum](http://forum.semiologic.com) is the best place to report issues. Please note, however, that while community members and I do our best to answer all queries, we're assisting you on a voluntary basis.

If you require more dedicated assistance, consider using [Semiologic Pro](http://www.getsemiologic.com).


== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Change Log ==

= 5.0.2 =

- Fix occasional invalid HTML on manual calls

= 5.0.1 =

- Apply filters to permalinks
- Fix cache flushing

= 5.0 =

- Complete rewrite
- WP_Widget class
- Drop all options except title (nofollow is always enabled)
- Smaller, better list of services
- Use jQuery, insert script in footer
- Localization
- Code enhancements and optimizations
