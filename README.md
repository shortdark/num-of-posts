# post-volume-stats
My first WordPress plugin. I originally based it on the one file plugin, Hello Dolly. I modified the simple plugin to count the number of posts per year, and get the number of posts per category. Initially the plugin just put all this info in a comment beneath the post content on a single WordPress post. Then, it was adapted to be an admin plugin (back to Dolly), but instead of being an admin notice, as Dolly is, it has it's own page and displays the info there.

Because I based the plugin on "Hello Dolly" the PHP wasn't OOP to begin with. When I changed the code back to OOP (some of the code comes from another project that was OOP) it worked fine just needed some tidying up. There are probably better ways to do a lot of the functionality, and better ways to interact with WordPress so I aim to continually improve the plugin to get closer to perfection.

There are graphical representation of year, category, tag, day-of-the-week, hour-of-the-day, month, day-of-the-month, and days between posts stats. The lists have been moved to AJAX, to free up more space and so it works better on mobile devices. You are also able to select a year and just see the stats for that one year. 

The tags and categories subpages allow the ability to export the data as HTML lists and line graphs; a click of a button creates a new post with the selected list and line graph in it. The "Date Range" is also a new feature.

It's a work of constant evolution.

https://www.postvolumestats.com/

https://wordpress.org/plugins/post-volume-stats/
