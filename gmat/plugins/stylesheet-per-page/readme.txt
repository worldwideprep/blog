=== Stylesheet Per Page ===
Contributors: jkohlbach
Donate link: http://www.codemyownroad.com
Tags: stylesheet, page styles, ie, ie stylesheet, ios stylesheet, ios, post stylesheet, post stylesheets, category stylesheet, tag stylesheet
Requires at least: 3.0
Tested up to: 4.2
Stable tag: trunk

Add custom stylesheets, IE override stylesheets, iOS stylesheets, to any page or post on your Wordpress website.

== Description ==

Adds a custom stylesheet/s all of the pages on your WordPress install with options to check for the existence of the stylesheet before adding.

Allows for adding IE specific stylesheets all the way back to version 6. Just enable in the plugin options and add the stylesheets to your theme directory.

Stylesheet Per Page now allows adding of an iOS specific stylesheet. Simply enable in the plugin options and add the stylesheet to your theme directory.

This plugin supports use with custom post types, category, and tag pages.

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

To use this plugin, just add stylesheets in the following formats to your theme's base directory or a "css" subdirectory inside your theme's base directory:

* For pages create a file called page-[page_name].css where "[page_name]" is replaced with the slug of the page. Eg: page-about.css.

* For author pages create a file called user.css and for individual user pages user-[username].css where "[username]" is replaced with the user's login name.

* For different post types user [post_type].css where "[post_type]" is replaced with the name of the custom post type. You can also use just 'post' for regular posts. Eg. post.css.

* For individual posts use [post_type]-[post_name].css where "[post_type]" is replaced with the name of the custom post type or just 'post' for regular posts, and "[post_name]" is the slug of the post. Eg. post-10-tips-for-baking-pies.css, or, say if you had a "recipes" post type use something like recipes-classic-french-cheesecake.css.

* For tag pages use tag.css or tag-[tag_name].css where "[tag_name]" is replaced with the slug of the tag

* For category pages use category.css or category-[category_name].css where "[category_name]" is replaced with the slug of the category

* For archive pages use archive.css

* For home and front pages use home.css and front-page.css respectively

* To activate IE specific stylesheets just create css files with the following naming: ie.css (covers all IE versions), ie8.css (covers IE 8 and below), ie7.css (covers IE 7 and below), ie6.css (covers IE 6 and below).

* To activate an iOS specific stylsheet create ios.css and enable in the plugin options.

* For targeting logged in users use stylesheet logged-in.css

* For targeting logged out users use stylesheet logged-out.css

NOTE: You can place your custom CSS files in either the base directory of your theme, or in a "css" subdirectory inside your theme, but try to be consistent with where you place them.

== Frequently Asked Questions ==

= How do I activate IE stylesheets? =
To activate stylesheets enable the option in the plugin options and create css files with the following naming convention:
ie.css (overrides for all IE versions), ie8.css (overrides for IE 8 and below), ie7.css (overrides for IE 7 and below), ie6.css (overrides for IE 6 and below).

= How do I get Stylesheet Per Page to check for the files first before trying to include them? =

Easy, just tick the option under the Stylesheet Per Page admin options under found under the Settings menu.

= How do I enable an iOS stylesheet? =

To activate an iOS specific stylsheet create ios.css and place it in your theme's base directory or css subdirectory inside your theme's base directory. You will also need to enable the checkbox in the plugin options.

== Screenshots ==

N/A

== Upgrade Notice ==

N/A

== Changelog ==

* 1.1.2
 - Adding user logged in/out stylesheets

* 1.1.1 Fixing warnings
 - Fixing some warnings on array checks that show up if user hasn't saved settings yet

* 1.1 Added Home and Front Page stylesheets
 - home.css and front-page.css now work to override default homepage styles

* 1.0 Added Tag, Category and Archive CSS files
 - Officially versioned as 1.0 as it's been pretty stable
 - Added tag.css and tag-[tag_name].css for tag pages
 - Added category.css and category-[category_name].css for category pages
 - Added archive.css for archive pages
 - When not selecting check for files first, now printing out files under css directory as well

* 0.6 Added IE8 stylesheet capabilities since IE9 was released
 - Added ability to add ie8.css file to cater for IE8 quirkyness
 - Tidied up installation instructions a bit

* 0.5 Added iOS specific stylesheet support and fixed option handling
 - Fixed option handling to use a single option for easy handling throughout the plugin
 - Added an iOS option for optionally printing ios.css for iOS devices.

* 0.4 Fixing a bug with stylesheet generation on custom post types
 - Bug fix for sheets with no name appearing when using with custom post types

* 0.3 Adding support for IE specific stylesheets
 - Added new functionality to allow optional generation of IE specific sheets

* 0.2 Initial code revisions
 - Structured the code better
 - Added a menu for the options page
 - Improved commenting
 - Added option to not print the stylesheet if the file doesn't exist

* 0.1 Initial version
 - Add a stylesheet for every page
