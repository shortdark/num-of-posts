<?php
/**
 * @package post-volume-stats
 * @version 2.1.8
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 2.1.8
 Author URI: http://www.shortdark.net/
 */

/**************************
 ** PREVENT DIRECT ACCESS
 **************************/

defined('ABSPATH') or die('No script kiddies please!');

if (!defined('WPINC')) {
	die ;
}

// Avoid direct calls to this file.
if (!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/****************
 ** DEFINE
 ****************/

define('SDPVS__PLUGIN_DIR', plugin_dir_path(__FILE__));

/****************
 ** DEBUG
 ****************/

/*
 // Turns WordPress debugging on
 define('WP_DEBUG', true);

 // Tells WordPress to log everything to the /wp-content/debug.log file
 define('WP_DEBUG_LOG', true);

 // Doesn't force the PHP 'display_errors' variable to be on
 define('WP_DEBUG_DISPLAY', false);

 // Hides errors from being displayed on-screen
 @ini_set('display_errors', 0);
 */

/******************
 ** SETUP THE PAGE
 ******************/

// Add the CSS
require_once (SDPVS__PLUGIN_DIR . 'sdpvs_css.php');

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_arrays.php');

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');
		$sdpvs_bar = new sdpvs_bar_chart();

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');
		$sdpvs_pie = new sdpvs_post_volume_stats_pie();

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');
		$sdpvs_lists = new sdpvs_text_lists();

		$content = "<h1 class='sdpvs'>Post Volume Stats</h1>\n";
		$content .= "<p class='sdpvs'>These are the post volume stats for " . get_bloginfo('name') . ".</p>\n";

		$content .= "<div class='sdpvs_col'>";
		// graph
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("year");
		// posts per year
		$content .= $sdpvs_lists -> sdpvs_posts_per_year_list();

		$content .= "</div>";

		$content .= "<div class='sdpvs_col'>";
		// posts per category pie chart
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("category");

		// posts per category
		$content .= $sdpvs_lists -> sdpvs_posts_per_category_list();

		$content .= "</div>";

		$content .= "<div class='sdpvs_col'>";
		// posts per tag pie chart
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("tag");

		// posts per tag
		$content .= $sdpvs_lists -> sdpvs_posts_per_tag_list();

		$content .= "</div>";
		$content .= "<div class='sdpvs_col'>";

		// posts per day of the week bar chart
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofweek");

		// posts per day of the week
		$content .= $sdpvs_lists -> sdpvs_posts_per_dayofweek_list();
		$content .= "</div>";

		$content .= "<div class='sdpvs_col'>";
		// posts per hour of the day pie chart
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("hour");

		// posts per hour of the day
		$content .= $sdpvs_lists -> sdpvs_posts_per_hour_list();

		$content .= "</div>";

	}

	echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'textdomain'), 'Post Volume Stats', 'read', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');
?>
