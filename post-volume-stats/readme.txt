
=== Post Volume Stats ===
Contributors: shortdark
Donate link: https://www.paypal.me/shortdark
Tags: posts, stats, graphs, charts, categories, tags, admin, year, month, day, hour, widget, author, taxonomy
Requires at least: 3.5
Tested up to: 4.7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows stats for the volume of posts per year, month, day-of-the-month, day-of-the-week, hour-of-the-day, author, categories, tags and custom taxonomy.

== Description ==

This plugin looks at the volume of posts in each category and tag, and also the volume 
of posts per year, month, day-of-the-month, day-of-the-week, hour and author. It works better the more posts 
you have, and if you use categories and tags. You can now specify a year and/or an author to just look at the post volume stats for
that year/author. The bar charts can be added to a sidebar with Post Volume Stats widget. Lists and line graphs 
can be exported to a new post to show the change in category, tag and custom taxonomy posts over the years. The latest features
are "words per post" stats and the "compare years" button allows all the data to be copy/pasted into a spreadsheet.

Please let me know if you like this plugin by leaving a review or [contacting me](http://www.shortdark.net/contact-me/).

Go to the [Shortdark Wordpress plugin page](http://www.shortdark.net/wordpress-plugin/) for more information.

= Translations =

You can translate Post Volume Stats on [__translate.wordpress.org__](https://translate.wordpress.org/projects/wp-plugins/post-volume-stats).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress 
plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. The menu item "Post Volume Stats" should now be in your admin menu and the "Post Volume Stats" widget should also be available to use.

== Screenshots ==

Here are the latest screenshots...

1. Shows the main page of "Post Volume Stats".
2. Shows the draggable AJAX data lists, and highlighted bar of a bar chart.
3. You can view the stats for each year individually. This shows the same blog with stats for one year selected.
4. The "Category", "Tag" and "Custom Taxonomy" pages allow the user to filter results and export the data into an HTML list and a line graph.
5. The export button adds the HTML list and line graph into a new blog post.
6. The results of exporting the line graph and list into a new post.
7. Shows the widget in the "live preview" area of the admin.
8. Settings page allows customization.

== Changelog ==

= 3.1.06 =

* Security fix made beta CSV download stop working, so this is a different fix.

= 3.1.05 =

* Security fix on beta CSV downloads.

= 3.1.04 =

* Updated PVS version number on the pages.

= 3.1.03 =

* "Words per post" should give a better distribution of the posts.
* Beta for CSV export added.

= 3.1.02 =

* Bug fixes on words per post.

= 3.1.01 =

* Minor bug fixes on the author and years bar charts.

= 3.1.00 =

* You can now compare years for some of the bar charts - shows data for all years in one table that can be copy/pasted into a spreadsheet.
* Words per post bar chart added.
* Custom Taxonomy bar chart added to the main page (must be selected on the Settings page).
* "Authors" data is now "Authors" or higher, "Contributors" are not included in the Authors data.
* Optional link in the Admin Toolbar, activated on the Settings page.
* Version number added to page footers.
* Bug fixing and streamlining.

= 3.0.29 =

* Custom taxonomy page added (must be selected on the Settings page).

= 3.0.28 =

* Updated description and POT file.

= 3.0.27 =

* You can now click a bar of the "Authors" barchart to filter the stats to that author.
* Settings page: turn off authors bar chart, turn off rainbow lists and week starts on.

= 3.0.26 =

* Updated description and POT file.

= 3.0.25 =

* Added more summary text stats to the bottom of the main page.
* Highlighted weekends on the "posts per day-of-the-week" bar chart.
* Added "Authors" bar chart.

= 3.0.24 =

* Added pie charts to Widget.
* Added links to the line graphs.

= 3.0.23 =

* Fixed bug on line graph for blogs with only one year of posts.
* Tidied and simplified tag/category pages.
* You can now choose whether to export line graph, list or both.

= 3.0.22 =

* Added plugin link to bottom of exported results.

= 3.0.21 =

* Line graphs improved and also able to be exported with the lists.
* Matching color applied to the export lists.
* Re-structured tag/category pages and removed the pie charts.

= 3.0.20 =

* Admin notices added.
* Line graph added to tags/categories pages.

= 3.0.19 =

* Improved the colors in the pie charts.

= 3.0.18 =

* Reverted back to having the preview, then from the preview you can "Export" into post.

= 3.0.17 =

* Changed from "Show HTML" to "Export" into post.
* One more debug notice fixed.

= 3.0.16 =

* Tidied debug notices.

= 3.0.15 =

* Fixed bug on exports.
* Added "load_plugin_textdomain".

= 3.0.14 =

* Tidied "export" method to reduce script time elapsed.

= 3.0.13 =

* Updated readme.txt with "translations" info.
* Widget screenshot.
* Minor changes.

= 3.0.12 =

* Wording fixed.
* Duplicate methods merged.
* Updated version of WordPress.

= 3.0.11 =

* Bug-fix.

= 3.0.10 =

* Widget added.

= 3.0.09 =

* Updated version numbers to re-load scripts and bug-fix.

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


