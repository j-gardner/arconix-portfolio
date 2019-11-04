=== Arconix Portfolio ===
Contributors: jgardner03, tychesoftwares
Donate link: http://arcnx.co/acpdonation
Tags: arconix, portfolio, quicksand, portfolio gallery
Requires at least: 3.8
Tested up to: 5.2.4
Stable tag: 1.6.0
License: GPLv2 or later

A powerful, easy and attractive way to display your recent work on your website

== Description ==

= Overview =

Let's face it, if you need to watch a detailed video or read a full-blown user manual in order to use a portfolio plugin, something's gone terribly wrong. A plugin like that should be simple to setup and use and integrate tightly with WordPress providing the smallest learning curve possible to get up and running quickly -- and that's exactly what Arconix Portfolio offers.

Get your basic portfolio up in 5 easy steps:

1. Install and activate this plugin, of course
2. Under the new Portfolio menu item click "Add New"
3. Enter your Portfolio details including uploading an image, assigning features (if desired), and selecting what you want to have happen when the portfolio image is clicked.
4. Create a new Page, place `[portfolio]` into the main editor box and click Publish
5. Sit back and marvel at the awesomeness you've just unleashed upon the internets.

Like all good software there are additional ways to customize the look and behavior of your portfolio. I suggest you take a peek at the [documentation](http://arcnx.co/apwiki) which lays out the various ways you can make your portfolio more personal.

> <strong>Great plugin with such kind people behind it</strong> - I have had an absolutely lovely experience with the support of the people behind this plugin. Highly recommend it! :)
> [lassechills](https://wordpress.org/support/topic/great-plugin-with-such-kind-people-behind-it/)

= Features =
* Custom Post Type-driven portfolio showcases your work, keeping it separate from posts, pages and other content types.
* Custom Taxonomy called "Features" allows you to "tag" your portfolio items which creates a filter list for a unique and practical way of presenting that info to your visitors
* jQuery Quicksand for filtering animation (when using the 'features' taxonomy) shows and hides your different portfolio items
* Individually set hyperlinks on portfolio items -- link to the portfolio item's page, a different/larger version of the portfolio image or even link to another web site entirely (perfect for client sites, YouTube or Vimeo videos, etc...). Mix and match to meet your individual needs.
* Supports upgrade-safe CSS changes -- Copy the CSS file from the plugin directory to your theme and the plugin will load your file instead, ensuring no conflicts and no worrying about future updates overwriting customizations.

= Use =

Add the [portfolio] shortcode to your post, page or other content area. Customizations are available and can be access via shortcode options which you can read about in the [plugin documentation](http://arcnx.co/apwiki)

= Demo =
[Documentation](https://www.tychesoftwares.com/docs/docs/portfolio/)

= Help =
WordPress [support forum](http://arcnx.co/aphelp) or feel free to open an [issue on GitHub](http://arcnx.co/apissues).

= Also Recommended =

* Display your portfolio items in a rotating slider or multi-image carousel by installing [Arconix Flexslider](http://wordpress.org/plugins/arconix-flexslider) and give your portfolio a boost!
* Showcase the great things people have to say about you or your business by installing [Arconix Testimonials](http://wordpress.org/plugins/arconix-testimonials)

== Installation ==

1. Download and install Arconix Portfolio using the built in WordPress plugin installer, or if you download the plugin manually, make sure the files are uploaded to `/wp-content/plugins/arconix-portfolio/`.
1. Activate Arconix-Portfolio in the "Plugins" admin panel using the "Activate" link.
1. Create your portfolio items from the WordPress admin section
1. Use the shortcode `[portfolio]` on a post, page or widget to display your items

== Upgrade Notice ==

Upgrade normally via your WordPress admin -> Plugins panel.

== Frequently Asked Questions ==

= How do I display my created portfolio items =

Create a WordPress Page and use the `[portfolio]` shortcode. See the [Documentation](https://www.tychesoftwares.com/docs/docs/portfolio/) for more details and available options

= Where can I find more information on how to use the plugin?  =

* Visit the plugin's [Documentation site](https://www.tychesoftwares.com/docs/docs/portfolio/) for assistance

= The filtering isn't working. What can I do? =

While you can certainly start a thread in the support forum, there are some troubleshooting steps you can take beforehand to help speed up the process.

1. Check to make sure the javascripts are loading correctly. Load the portfolio page in your browser and view your page's source. Look for jQuery, Quicksand and jQuery Easing files there. If you don't see Quicksand or the Easing scripts, then your theme's `footer.php` file is likely missing `<?php wp_footer(); ?>`, which is neccessary for the operation of mine and many other plugins.
2. Check to make sure only one copy of jQuery is being loaded. Many times conflicts arise when themes or plugins load jQuery incorrectly, causing the script to be loaded multiple times in multiple versions. In order to find the offending item, start by disabling your plugins one by one until you find the problem. If you've disabled all your plugins, try switching to a different them, such as twentyten or twentytwelve to see if the problem is with your theme. Once you've found the problem, contact the developer for assistance getting the issue resolved.

= I need help =

Check out the WordPress [support forum](https://wordpress.org/support/plugin/arconix-portfolio/)

= I have a great idea for your plugin! =

That's fantastic! Feel free to submit a pull request over at [Github](https://github.com/TycheSoftwares/arconix-portfolio), or you can contact me through [Twitter](https://twitter.com/tychesoftwares), [Facebook](https://www.facebook.com/tychesoftwares/) or my [Website](https://www.tychesoftwares.com)

== Screenshots ==
1. Portfolio Custom Post Type listed in the WordPress Menu
2. Portfolio list in the admin
3. Portfolio settings

== Changelog ==
= 1.6.0 =
* Updated the CMB library to its latest version.
* Optimized code with respect to WordPress Coding Standards

= 1.5.0 =
* You can now edit the Link Type of the portfolio in the Quick Edit option.
* A new Link type 'None' is added to the portfolios. If this option is selected, the image of the portfolio will not be linked to any image or page.

= 1.4.0 =
* The portfolio item title can now be linked. Set the `title_link` param to "true"
* Fixed the link type column to properly display the link data
* Integrate with Arconix Flexslider so the link type is respected
* Improved the Metabox experience by hiding the external link text box until "external" is selected as the hyperlink destination.
* Added additional filters to be more extensible and developer friendly
* Updated to the latest version of the Quicksand library
* Code cleanup and optimization for easier maintenance

= 1.3.2 =
Resolves a conflict with themes from WooThemes that prevented users from inserting media into the editor

= 1.3.1 =
 Fix HTML validation bug

= 1.3.0 =
* Portfolio items can now be individually configured to link to an Image, Page, or External Link. See the documentation for more details
* Adding a security check on the dashboard widget (filterable) so it only loads for admins
* Added a filter to the return content for the portfolio data for additional developer flexibility

= 1.2.2 =
* Minified javascript support removed from arconix-portfolio.js until a better solution can be implemented

= 1.2.1 =
* Fixed a bug in the taxonomy query that would prevent the portfolio items from displaying

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
