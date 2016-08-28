<?php
/**
 * @package post-volume-stats
 * @version 3.0.03
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 3.0.03
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

/******************
 ** SETUP THE PAGE
 ******************/

// Add the includes

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_arrays.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_info.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');

/**********************
 ** ASSEMBLE THE PAGE
 **********************/

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {
		// Start the timer
		$time_start = microtime(true);

		// $year = get_option('sdpvs_year_option');
		// $searchyear = absint($year['year_number']);

		$sdpvs_info = new sdpvsInfo();

		$sdpvs_bar = new sdpvsBarChart();

		$sdpvs_pie = new sdpvsPieChart();

		$years = $sdpvs_info -> sdpvs_how_many_years_of_posts();
		$options = get_option('sdpvs_year_option');
		$selected = absint($options['year_number']);

		echo __("<h1 class='sdpvs'>Post Volume Stats</h1>", 'post-volume-stats');
		echo "<div style=\"width: 500px; display: inline-block;\">";
		if (0 < $selected) {
			echo __("<p class='sdpvs'>These are the stats for " . get_bloginfo('name') . " for the selected year: $selected.</p>", 'post-volume-stats');
		} else {
			echo __("<p class='sdpvs'>These are the all time stats for " . get_bloginfo('name') . ".</p>", 'post-volume-stats');
		}
		echo "</div>";
		
		echo "<div style=\"display: inline-block;\">";

		echo "<form class='sdpvs_year_form' action='options.php' method='POST'>";
		settings_fields('sdpvs_year_option');
		echo "<select name=\"sdpvs_year_option[year_number]\" id=\"year-number\" onchange=\"this.form.submit()\">";
		echo "<option value=\"\">All Years</option>";

		for ($i = 0; $i <= $years; $i++) {
			$searchyear = date('Y') - $i;
			if ($searchyear == $selected) {
				echo "<option value=\"$searchyear\" SELECTED >$searchyear</option>";
			} else {
				echo "<option value=\"$searchyear\">$searchyear</option>";
			}

		}

		echo "</select>";
		echo "</form>";
		echo "</div>";
		
		echo "<hr>";

		// posts per category pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg("category", $selected);
		echo "</div>";

		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg("tag", $selected);
		echo "</div>";

		echo "<hr>";

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg("year", $selected);
		echo "</div>";

		// posts per day of the week bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofweek", $selected);
		echo "</div>";

		// posts per hour of the day bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg("hour", $selected);
		echo "</div>";

		// posts per month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg("month", $selected);
		echo "</div>";

		// posts per day of the month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofmonth", $selected);
		echo "</div>";

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Div for ajax list box
		echo "<div id='sdpvs_listcontent'>";
		echo "</div>";
	}

	echo $sdpvs_info -> sdpvs_info();

	// Stop the timer
	$time_end = microtime(true);
	$elapsed_time = sprintf("%.5f", $time_end - $time_start);
	echo __("<p>	Script time elapsed: " . $elapsed_time . " seconds</p>", 'post-volume-stats');

	// echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {

	add_menu_page(__('Post Volume Stats', 'post-volume-stats'), __('Post Volume Stats', 'post-volume-stats'), 'manage_options', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/***************
 ** USER INPUT
 ***************/

/**
 * Register the settings
 */
function sdpvs_register_settings() {
	register_setting('sdpvs_year_option', // settings section
	'sdpvs_year_option', // setting name
	'sanitize');
	add_settings_field('year_number', // ID
	'Year Number' // Title
	);

}

add_action('admin_init', 'sdpvs_register_settings');

/**
 * Sanitize the field
 */
function sanitize($input) {
	$new_input = array();
	if (isset($input['year_number'])) {
		$new_input['year_number'] = absint($input['year_number']);
	}
	return $new_input;
}

/*************
 ** AJAX...
 *************/

function sdpvs_load_ajax_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.3', 'screen');
	wp_enqueue_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__), array('jquery'), '1.0.0', true);

	// Importing external JQuery UI element using "wp-includes/script-loader.php"
	wp_enqueue_script("jquery-ui-draggable");

	//Here we create a javascript object variable called "sdpvs_vars". We can access any variable in the array using sdpvs_vars.name_of_sub_variable
	wp_localize_script('sdpvs_loader', 'sdpvs_vars', array(
	//To use this variable in javascript use "sdpvs_vars.ajaxurl"
	'ajaxurl' => admin_url('admin-ajax.php'),
	//To use this variable in javascript use "sdpvs_vars.whichdata"
	'whichdata' => $whichdata,
	// nonce...
	'ajax_nonce' => wp_create_nonce('num-of-posts'), ));
}

add_action('admin_enqueue_scripts', 'sdpvs_load_ajax_scripts');

function sdpvs_process_ajax() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	// create an instance of the list class
	$sdpvs_lists = new sdpvsTextLists();

	// Extract the variable from serialized string
	$gotit = filter_var($_POST['whichdata'], FILTER_SANITIZE_STRING);
	$after_equals = strpos($gotit, "=") + 1;
	$answer = substr($gotit, $after_equals);

	$year = get_option('sdpvs_year_option');
	$searchyear = absint($year['year_number']);

	if ("year" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_year_list();
	} elseif ("hour" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_hour_list($searchyear);
	} elseif ("dayofweek" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_dayofweek_list($searchyear);
	} elseif ("category" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_category_list($searchyear);
	} elseif ("tag" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_tag_list($searchyear);
	} elseif ("month" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_month_list($searchyear);
	} elseif ("dayofmonth" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_day_of_month_list($searchyear);
	}

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_get_results', 'sdpvs_process_ajax');
?>
