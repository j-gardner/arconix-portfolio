=== Arconix Portfolio ===
Contributors: jgardner03
Tags: arconix, portfolio, quicksand
Donate link: http://arcnx.co/acpdonation
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 1.2

Arconix Portfolio allows you to easily display your portfolio on your website.

== Description ==

With this plugin you can easily showcase your portfolio on your WordPress website. Utilizing Custom Post Types keeps those items separate from posts and pages, supporting individual titles, images, features and descriptions.

= Features =
* Custom Post Type-driven portfolio showcases your work
* Custom Taxonomy called "Features" allows you to group your portfolio items
* jQuery Quicksand for slick filtering animation (when using the 'features' taxonomy)

== Installation ==

1. Download and install Arconix Portfolio using the built in WordPress plugin installer, or if you download the plugin manually, make sure the files are uploaded to `/wp-content/plugins/arconix-portfolio/`.
1. Activate Arconix-Portfolio in the "Plugins" admin panel using the "Activate" link.
1. Create your portfolio items from the WordPress admin section
1. Use the shortcode `[portfolio]` on a post, page or widget to display your items

== Upgrade Notice ==


== Frequently Asked Questions ==

= How do I display my created portfolio items =

Create a WordPress Page and use the `[portfolio]` shortcode. See the [Documentation](http://http;//arcnx.co/apwiki) for more details and available options

= Where can I find more information on how to use the plugin?  =

* Visit the plugin's [Wiki Page](http://arcnx.co/apwiki) for documentation
* Tutorials on advanced plugin usage can be found at [Arconix Computers](http://arconixpc.com/tag/arconix-portfolio)

= The filtering isn't working. What can I do? =

While you can certainly start a thread in the [support forum](http://arcnx.co/aphelp), there are some troubleshooting steps you can take beforehand to help speed up the process.
1. Check to make sure the javascripts are loading correctly. Load the portfolio page in your browser and view your page's source. Look for jQuery, Quicksand and jQuery Easing files there. If you don't see Quicksand or the Easing scripts, then your theme's `footer.php` file is likely missing `<?php wp_footer(); ?>`, which is neccessary for the operation of mine and many other plugins.
2. Check to make sure only one copy of jQuery is being loaded. Many times conflicts arise when themes or plugins load jQuery incorrectly, causing the script to be loaded multiple times in multiple versions. In order to find the offending item, start by disabling your plugins one by one until you find the problem. If you've disabled all your plugins, try switching to a different them, such as twentyten or twentytwelve to see if the problem is with your theme. Once you've found the problem, contact the developer for assistance getting the issue resolved.

= I need help =

Check out the WordPress [support forum](http://arcnx.co/aphelp)

= I have a great idea for your plugin! =

That's fantastic! Feel free to submit a pull request over at [Github](http://arcnx.co/apsource), add an idea to the [Trello Board](http://arcnx.co/aptrello), or you can contact me through [Twitter](http://arcnx.co/twitter), [Facebook](http://arcnx.co/facebook) or my [Website](http://arcnx.co/1)

== Screenshots ==
1. Portfolio Custom Post Type listed on the WP backend
2. Creating a Portfolio Item

== Changelog ==

= 1.2.0 =
* Prevent the associated html from loading if the taxonomy heading is blank
* Fixed a missing translation string
* When using the features taxonomy, users can now pass `terms_orderby` and `terms_order` as shortcode parameters which will allow you to set the order of the terms to any of the [available options](http://codex.wordpress.org/Function_Reference/get_terms#Possible_Arguments)
* Added a pre_register filter for the CSS and Javascript which will allow for additional customization options for advanced users
* The JS files are now minified, which significantly reduces the payload. Translation: smaller files = faster load times
* Improved the plugin defaults and added a filter so they could be changed easily

= 1.1.1 =
* Fixed a php error when not using any "features"
* Fixed some display inconsistencies when using the taxonomy filter

= 1.1.0 =
* Added ability to choose which feature term is displayed or hidden through a shortcode argument
* Users can now choose whether the item title is displayed above or below the featured image
* The feature heading text can now be customized
* Shortcode will now output wherever it is called on a page, and not just at the top
* Plugin will no longer produce an error when using a custom javascript file

= 1.0 =
* Added jQuery Quicksand support for animating the "features" taxonomy filtering
* Added ability to enable/disable showing of portfolio title

= 0.9.1 =
* Initial Release