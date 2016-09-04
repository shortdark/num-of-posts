<?php
/**
 * @package post-volume-stats
 * @version 3.0.06
 */
/*
 * Plugin Name: Post Volume Stats
 * Plugin URI: https://github.com/shortdark/num-of-posts
 * Description: Displays the post stats in the admin area with graphical representations and detailed lists.
 * Author: Neil Ludlow
 * Text Domain: post-volume-stats
 * Version: 3.0.06
 * Author URI: http://www.shortdark.net/
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
define('SDPVS__PLUGIN_FOLDER', 'post-volume-stats');

/******************
 ** SETUP THE PAGE
 ******************/

// Add the includes

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_arrays.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_info.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_main.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs-subs.php');

/**********************
 ** ASSEMBLE THE PAGE
 **********************/

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {
		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_info = new sdpvsInfo();

		// Call the method
		$sdpvs_content = new sdpvsMainContent();

		// Title
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats', 'post-volume-stats') . '</h1>';

		// Load content for the main page
		$sdpvs_content -> sdpvs_page_content();

		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Div for ajax list box
		echo "<div id='sdpvs_listcontent'>";
		echo "</div>";
	}

	$sdpvs_info -> sdpvs_info();

	// Stop the timer and show the results
	$time_end = microtime(true);
	$elapsed_time = sprintf("%.5f", $time_end - $time_start);
	echo '<p>' . sprintf(esc_html__('Script time elapsed: %f seconds', 'post-volume-stats'), $elapsed_time) . '</p>';

}

// Category page
function sdpvs_category_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_sub = new sdpvsSubPages();

		// Call the method
		$sdpvs_sub -> sdpvs_category_page_content();

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo "<p>" . __("Script time elapsed: " . $elapsed_time . " seconds", 'post-volume-stats') . "</p>";

	}
	return;
}

// Tag page
function sdpvs_tag_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class
		$sdpvs_sub = new sdpvsSubPages();

		// Call the method
		$sdpvs_sub -> sdpvs_tag_page_content();

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo "<p>" . __("Script time elapsed: " . $elapsed_time . " seconds", 'post-volume-stats') . "</p>";

	}
	return;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'post-volume-stats'), __('Post Volume Stats', 'post-volume-stats'), 'manage_options', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
	add_submenu_page(dirname(__FILE__), 'Post Volume Stats: Categories', 'Categories', 'read', 'post-volume-stats-categories', 'sdpvs_category_page');
	add_submenu_page(dirname(__FILE__), 'Post Volume Stats: Tags', 'Tags', 'read', 'post-volume-stats-tags', 'sdpvs_tag_page');
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
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.4', 'screen');
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
