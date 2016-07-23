
=== Post Volume Stats ===
Contributors: shortdark
Donate link: http://www.shortdark.net/
Tags: posts, stats, categories, tags, simple, admin, year, month, day, hour, graph, graphs, charts
Requires at least: 3.5
Tested up to: 4.5.3
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows stats for the volume of posts per year, category, tag, day-of-the-week and hour-of-the-day.

== Description ==

This is a simple plugin to look at the volume of posts in each category and tag, and also the volume 
of posts per year, month, day-of-the-month, day-of-the-week and hour. It works better the more posts 
you have, and if you use categories and tags.

Go to [Shortdark](http://www.shortdark.net) for more information.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. The menu item "Post Volume Stats" should now be in your admin menu.

== Screenshots ==

Here are the latest screenshots...

1. Shows the stats on a very simple blog with not many posts.

2. Shows the draggable AJAX data lists, and highlighted bar of a bar chart.

== Changelog ==

= 2.2.2 =

* Added JQuery UI.
* AJAX DIVs are now draggable.
* Updated readme.txt description.
* More text changed to translatable strings.

= 2.2.1 =

* "lists" moved out of the page and into AJAX DIVs with loading animation
* CSS loaded as a .css file, instead of in-line
* Months and days-of-the-month added.

= 2.1.8 =

* Removed jddayofweek completely as it was not working properly.

= 2.1.7 =

* Removed PHP function jddayofweek for PHP versions below 5.3 as was not working on 5.2.17

= 2.1.6 =

* Removed the Day of the Week section for PHP vesions below 5.3 as that part was not working on a 5.2 version of PHP.

= 2.1.5 =

* Removed the magic variable __DIR__ that limited the plugin to PHP versions 5.3 and above.

= 2.1.4 =

* Changed the way the info is gathered, meaning that the year. Hour and day-of-week data should now be correct, whereas before it was incorrect.

= 2.1.3 =

* Re-ordered the data in the pie charts into size order.

= 2.1.2 =

* Added bar charts for day-of-the-week and hour-of-the-day.
* Simplified the CSS to allow for easy additional columns.
* Calculated the "requires at least" from the Wordpress functions used.

= 2.1.1 =

* Added the total number of posts in yearly column.

= 2.1.0 =

* More security.
* More OOP classes and split up into different files.
* Changed admin page type to "read" as it does not have any need for user input and does not do anything.

= 2.09 =

* Started changing to OOP.
* Tags added.


