<?php
/**
 * @package post-volume-stats
 * @version 2.1.0
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 2.1.0
 Author URI: http://www.shortdark.net/
 */

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

define('SDPVS__PLUGIN_DIR', plugin_dir_path(__FILE__));

/****************
 ** DEBUG
 ****************/

// Turns WordPress debugging on
define('WP_DEBUG', true);

// Tells WordPress to log everything to the /wp-content/debug.log file
define('WP_DEBUG_LOG', true);

// Doesn't force the PHP 'display_errors' variable to be on
define('WP_DEBUG_DISPLAY', false);

// Hides errors from being displayed on-screen
@ini_set('display_errors', 0);



// Add the CSS
require_once (SDPVS__PLUGIN_DIR . 'sdpvs_css.php');

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');
		$sdpvs_bar = new sdpvs_bar_chart();

		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');
		$sdpvs_pie = new sdpvs_post_volume_stats_pie();
		
		require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');
		$sdpvs_lists = new sdpvs_text_lists();

		$content = "<h1 class='sdpvs'>Post Volume Stats</h1>\n";
		$content .= "<p class='sdpvs'>These are your post stats.</p>\n";

		$content .= "<div id='sdpvs_leftcol'>";
		// graph
		$content .= $sdpvs_bar -> sdpvs_draw_year_svg();
		// posts per year
		$content .= $sdpvs_lists->sdpvs_number_of_posts_per_year();

		$content .= "</div>";

		$content .= "<div id='sdpvs_rightcol1'>";
		// posts per category pie chart
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("category");

		// posts per category
		$content .= $sdpvs_lists->sdpvs_post_category_volumes();

		$content .= "</div>";

		$content .= "<div id='sdpvs_rightcol2'>";
		// posts per tag pie chart
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("tag");

		// posts per tag
		$content .= $sdpvs_lists->sdpvs_post_tag_volumes();

		$content .= "</div>";
	}

	echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'textdomain'), 'Post Volume Stats', 'read', __DIR__, 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/****************
 ** TODO
 ****************/

/*
 * 1) Better use of classes to neaten up and minimize duplication of code
 * 2) Present the data better
 * 3) I18n, write to the page using "translatable strings" in a __() function
 * 4) Plugin info, figure out how to improve the look and add images
 *
 */
?>
