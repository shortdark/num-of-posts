
=== Post Volume Stats ===
Contributors: shortdark
Donate link: http://www.shortdark.net/wordpress-plugin/
Tags: posts, stats, graphs, charts, categories, tags, admin, year, month, day, hour
Requires at least: 3.5
Tested up to: 4.6
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows stats for the volume of posts per year, month, day-of-the-month, day-of-the-week, hour-of-the-day, categories and tags.

== Description ==

This plugin to look at the volume of posts in each category and tag, and also the volume 
of posts per year, month, day-of-the-month, day-of-the-week and hour. It works better the more posts 
you have, and if you use categories and tags. You can now specify a year to look at the post volume stats for
that year.

Please let me know if you like this plugin by leaving a review or [contacting me](http://www.shortdark.net/contact-me/).

Go to the [Shortdark Wordpress plugin page](http://www.shortdark.net/wordpress-plugin/) for more information.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress 
plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. The menu item "Post Volume Stats" should now be in your admin menu.

== Screenshots ==

Here are the latest screenshots...

1. Shows the stats for a blog with 3 years of posts.
2. Shows the draggable AJAX data lists, and highlighted bar of a bar chart.
3. You can view the stats for each year individually. This shows the same blog with stats for one year selected.
4. The new "Tag" page allows the user to export the data to HTML, but soon it will allow the user 
to take a more indepth view.

== Changelog ==

= 3.0.08 =

* Export "Categories" data to HTML.

= 3.0.07 =

* Export "Tags" data to HTML.

= 3.0.06 =

* Bug fix - allowed top line of bar chart if it is on the boundary of the chart.
* I18n improvements.
* Added "Category" and "Tag" admin subpages.
* Tidied.

= 3.0.05 =

* Bug fixes.

= 3.0.04 =

* Added lines and legends to the bar charts.

= 3.0.03 =

* Brought the lists back for the bar charts.
* Tidied code.
* Cosmetic changes.

= 3.0.02 =

* Removed submit button from year dropdown used 'onchange' to submit instead.
* You can now also select a year by clicking a bar of the 'Years' graph.

= 3.0.01 =

* Updated the version number because some older versions were not updating.

= 2.3.05 =

* Made sure categories should be working correctly.
* Prevented direct access to class files.

= 2.3.04 =

* Fixed bug with yearly tags.

= 2.3.03 =

* When a year is selected it applies to all stats now, including tags and categories.
* Changed pie chart opacity rules.

= 2.3.02 =

* The year option setting moved from it's own page to the main plugin page.

= 2.3.01 =

* Settings page added for users to chose the year for all time-based stats.

= 2.2.6 =

* Modified the pie chart coloring
* Preparation for UI
* Modified layout

= 2.2.5 =

* Added posts per day info.

= 2.2.4 =

* Timed the script.

= 2.2.3 =

* Loaded external jQuery UI draggable the proper way using script-loader.php
* Limited the height of the lists to smaller than the height of the window
* Fixed the number of years bug on the years list

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


