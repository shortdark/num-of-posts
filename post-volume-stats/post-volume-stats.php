<?php
/**
 * @package post-volume-stats
 * @version 2.2.3
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 2.2.3
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

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_bar.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_pie.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_lists.php');

/**********************
 ** ASSEMBLE THE PAGE
 **********************/

// This assembles the plugin page.
function sdpvs_post_volume_stats_assembled() {

	if (is_admin()) {

		$sdpvs_bar = new sdpvs_bar_chart();

		$sdpvs_pie = new sdpvs_post_volume_stats_pie();

		$content = __("<h1 class='sdpvs'>Post Volume Stats</h1>", 'post-volume-stats');
		$content .= __("<p class='sdpvs'>These are the post volume stats for " . get_bloginfo('name') . ".</p>", 'post-volume-stats');

		// year bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("year");
		$content .= "</div>";

		// posts per category pie chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("category");
		$content .= "</div>";

		// posts per tag pie chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("tag");
		$content .= "</div>";

		// posts per day of the week bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofweek");
		$content .= "</div>";

		// posts per hour of the day bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("hour");
		$content .= "</div>";

		// posts per month bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("month");
		$content .= "</div>";

		// posts per day of the month bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofmonth");
		$content .= "</div>";

		// DIV for loading
		$content .= "<div id='sdpvs_loading'>";
		$content .= "</div>";

		// Div for ajax list box
		$content .= "<div id='sdpvs_listcontent'>";
		$content .= "</div>";
	}

	echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'post-volume-stats'), __('Post Volume Stats', 'post-volume-stats'), 'read', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/*************
 ** AJAX...
 *************/

function sdpvs_load_ajax_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.2', 'screen');
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
	$sdpvs_lists = new sdpvs_text_lists();

	// Extract the variable from serialized string
	$gotit = filter_var($_POST['whichdata'], FILTER_SANITIZE_STRING);
	$after_equals = strpos($gotit, "=") + 1;
	$answer = substr($gotit, $after_equals);

	if ("year" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_year_list();
	} elseif ("hour" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_hour_list();
	} elseif ("dayofweek" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_dayofweek_list();
	} elseif ("category" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_category_list();
	} elseif ("tag" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_tag_list();
	} elseif ("month" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_month_list();
	} elseif ("dayofmonth" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_day_of_month_list();
	}

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_get_results', 'sdpvs_process_ajax');
?>
