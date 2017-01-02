<?php
/**
 * @package post-volume-stats
 * @version 3.0.28
 */
/*
 * Plugin Name: Post Volume Stats
 * Plugin URI: https://github.com/shortdark/num-of-posts
 * Description: Displays the post stats in the admin area with pie and bar charts, also exports tag and category stats to detailed lists and line graphs that can be exported to posts.
 * Author: Neil Ludlow
 * Text Domain: post-volume-stats
 * Version: 3.0.28
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

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_subs.php');

require_once (SDPVS__PLUGIN_DIR . 'sdpvs_widget.php');

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

		// Admin notices can be used once they're properly dimissable
		// echo '<div class="notice notice-info is-dismissible"><p class="sdpvs"><strong>' . esc_html__('NEW: You can now export category and tag data directly into a new blog post at the click of a button. There is also a Post Volume Stats widget to add bar charts into your sidebar.', 'post-volume-stats') . '</strong></p></div>';

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
		$sdpvs_sub -> sdpvs_combined_page_content('category');

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
		
		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

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
		$sdpvs_sub -> sdpvs_combined_page_content('tag');

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
		
		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo "<p>" . __("Script time elapsed: " . $elapsed_time . " seconds", 'post-volume-stats') . "</p>";

	}
	return;
}

// Settings page
function sdpvs_settings_page() {
	if (is_admin()) {

		// Start the timer
		$time_start = microtime(true);

		// Create an instance of the required class

		// Content goes here
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Settings', 'post-volume-stats') . '</h1>';
		
		
		echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
		settings_fields( 'sdpvs_general_option' );
		
		//echo '<h2 class="sdpvs">' . esc_html__('Week starts on', 'post-volume-stats') . '</h2>';
		//echo "<div style='display: block; padding: 5px;'><label><input type=\"radio\" name=\"startweekon\" value=\"sunday\">Sunday (default)</label><br><label><input type=\"radio\" name=\"startweekon\" value=\"monday\">Monday</label></div>";
		do_settings_sections( SDPVS__PLUGIN_FOLDER );
		
		
//		echo '<h2 class="sdpvs">' . esc_html__('Rainbow-colored lists', 'post-volume-stats') . '</h2>';
//		echo '<p>' . esc_html__('This is the color of the text lists. Rainbow color means the text is the same color as the line of the line graph.', 'post-volume-stats') . '</p>';
//		echo "<div style='display: block; padding: 5px;'><label><input type=\"radio\" name=\"rainbow\" value=\"on\">On (default)</label><br><label><input type=\"radio\" name=\"rainbow\" value=\"off\">Off</label></div>";
		
//		echo "<div style='display: block; padding: 5px;'><input type='submit' name='all' class='button-primary' value='" . esc_html__('Save Settings') . "'></div>";
		submit_button('Save');
		echo "</form>";

		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		echo '<p>If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>. Thank you.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
		
		// DIV for loading
		echo "<div id='sdpvs_loading'>";
		echo "</div>";

		// Stop the timer
		$time_end = microtime(true);
		$elapsed_time = sprintf("%.5f", $time_end - $time_start);
		echo "<p>" . __("Script time elapsed: " . $elapsed_time . " seconds", 'post-volume-stats') . "</p>";

	}
	return;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(esc_html__('Post Volume Stats', 'post-volume-stats'), esc_html__('Post Volume Stats', 'post-volume-stats'), 'manage_options', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Categories', 'post-volume-stats'), esc_html__('Categories', 'post-volume-stats'), 'read', 'post-volume-stats-categories', 'sdpvs_category_page');
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Tags', 'post-volume-stats'), esc_html__('Tags', 'post-volume-stats'), 'read', 'post-volume-stats-tags', 'sdpvs_tag_page');
	add_submenu_page(dirname(__FILE__), esc_html__('Post Volume Stats: Settings', 'post-volume-stats'), esc_html__('Settings', 'post-volume-stats'), 'manage_options', 'post-volume-stats-settings', 'sdpvs_settings_page');
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/**
 * Load plugin textdomain.
 */
function sdpvs_load_textdomain() {
	load_plugin_textdomain('post-volume-stats', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'sdpvs_load_textdomain');

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
	'Year Number', // Title
	'', SDPVS__PLUGIN_FOLDER);
	add_settings_field('author_number', // ID
	'Author Number', // Title
	'', SDPVS__PLUGIN_FOLDER);
}

add_action('admin_init', 'sdpvs_register_settings');

function sdpvs_register_author_settings() {
	register_setting('sdpvs_author_option', // settings section
	'sdpvs_author_option', // setting name
	'sanitize');
	add_settings_field('author_number', // ID
	'Author Number', // Title
	'', SDPVS__PLUGIN_FOLDER);
}

add_action('admin_init', 'sdpvs_register_author_settings');

function sdpvs_register_general_settings() {
	register_setting( 'sdpvs_general_option', 'sdpvs_general_settings' );
    add_settings_section( 'sdpvs_general_settings', 'General Settings', 'sanitize_general', SDPVS__PLUGIN_FOLDER );
    add_settings_field( 'startweekon', 'Start Week On', 'field_one_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'rainbow', 'Rainbow Lists', 'field_two_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'authoroff', 'Number of Contributors', 'field_three_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
}

add_action('admin_init', 'sdpvs_register_general_settings');

function field_one_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$startweek = filter_var ( $genoptions['startweekon'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("sunday" == $startweek or !$startweek){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"sunday\" checked=\"checked\">Sunday (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"monday\">Monday</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"sunday\">Sunday (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"monday\" checked=\"checked\">Monday</label>";
	}
	echo "</div>";
}

function field_two_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$listcolors = filter_var ( $genoptions['rainbow'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("on" == $listcolors or !$listcolors){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"on\" checked=\"checked\">On (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"off\">Off</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"on\">On (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"off\" checked=\"checked\">Off</label>";
	}
	
	echo "</div>";
}

function field_three_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$authoroff = filter_var ( $genoptions['authoroff'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("multiple" == $authoroff or !$authoroff){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"multiple\" checked=\"checked\">More than one (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"one\">One</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"multiple\">More than one (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"one\" checked=\"checked\">One</label>";
	}
	
	echo "</div>";
}

/**
 * Sanitize the field
 */
function sanitize($input) {
	$new_input = array();
	if (isset($input['year_number'])) {
		$new_input['year_number'] = absint($input['year_number']);
	}
	if (isset($input['author_number'])) {
		$new_input['author_number'] = absint($input['author_number']);
	}
	return $new_input;
}

function sanitize_general($input) {
	$new_input = array();
	if (isset($input['startweekon'])) {
		$new_input['startweekon'] = filter_var ( $input['startweekon'], FILTER_SANITIZE_STRING);
	}
	if (isset($input['rainbow'])) {
		$new_input['rainbow'] = filter_var ( $input['rainbow'], FILTER_SANITIZE_STRING);
	}
	return $new_input;
}

/*************
 ** AJAX...
 *************/

function sdpvs_load_all_admin_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.5', 'screen');

	// Importing external JQuery UI element using "wp-includes/script-loader.php"
	wp_enqueue_script("jquery-ui-draggable");

	wp_register_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__));
	wp_enqueue_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__), array('jquery'), '1.0.2', true);

	$whichdata = "";
	$whichcats = "";
	$whichtags = "";

	//Here we create a javascript object variable called "sdpvs_vars". We can access any variable in the array using sdpvs_vars.name_of_sub_variable
	wp_localize_script('sdpvs_loader', 'sdpvs_vars', array(
	//To use this variable in javascript use "sdpvs_vars.ajaxurl"
	'ajaxurl' => admin_url('admin-ajax.php'),
	//To use this variable in javascript use "sdpvs_vars.whichdata"
	'whichdata' => $whichdata,
	//To use this variable in javascript use "sdpvs_vars.whichcats"
	'whichcats' => $whichcats,
	//To use this variable in javascript use "sdpvs_vars.whichtags"
	'whichtags' => $whichtags,
	// nonce...
	'ajax_nonce' => wp_create_nonce('num-of-posts'), ));

}

add_action('admin_enqueue_scripts', 'sdpvs_load_all_admin_scripts');

// Add scripts and stylesheets to the public-facing blog
function sdpvs_load_all_public_scripts() {
	wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.5', 'screen');
}

add_action('wp_enqueue_scripts', 'sdpvs_load_all_public_scripts');

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
	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);

	if ("year" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_year_list($searchauthor);
	} elseif ("hour" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_hour_list($searchyear, $searchauthor);
	} elseif ("dayofweek" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_dayofweek_list($searchyear, $searchauthor);
	} elseif ("category" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($answer, $searchyear, $searchauthor, 'admin', '');
	} elseif ("tag" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($answer, $searchyear, $searchauthor, 'admin', '');
	} elseif ("month" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_month_list($searchyear, $searchauthor);
	} elseif ("dayofmonth" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_day_of_month_list($searchyear, $searchauthor);
	} elseif ("author" == $answer) {
		echo $sdpvs_lists -> sdpvs_posts_per_author_list($searchyear, $searchauthor);
	}

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_get_results', 'sdpvs_process_ajax');

function sdpvs_cats_lists() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	$sdpvs_sub = new sdpvsSubPages();

	// Extract the variables from serialized string
	$gotit = filter_var($_POST['whichcats'], FILTER_SANITIZE_STRING);
	preg_match_all('/=([0-9]*)/', $gotit, $matches);

	echo $sdpvs_sub -> update_ajax_lists('category', $matches);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_select_cats', 'sdpvs_cats_lists');

function sdpvs_tags_lists() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	$sdpvs_sub = new sdpvsSubPages();

	// Extract the variables from serialized string
	$gotit = filter_var($_POST['whichtags'], FILTER_SANITIZE_STRING);
	preg_match_all('/=([0-9]*)/', $gotit, $matches);

	echo $sdpvs_sub -> update_ajax_lists('tag', $matches);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_select_tags', 'sdpvs_tags_lists');

function sdpvs_remove_admin_notice() {
	// Security check
	check_ajax_referer('num-of-posts', 'security');

	// activate this with AJAX, #sdpvs-notice
	set_transient('sdpvs-admin-notice-004', true, 0);

	// Always die() AJAX
	die();
}

add_action('wp_ajax_sdpvs_admin_notice', 'sdpvs_remove_admin_notice');

/*************
 ** EXPORT...
 *************/

function sdpvs_admin_export_lists() {
	$sdpvs_lists = new sdpvsTextLists();
	$sdpvs_bar = new sdpvsBarChart();

	// Extract the variables from encoded string
	$matches_string = filter_var($_POST['matches'], FILTER_SANITIZE_STRING);
	preg_match_all('/\[([0-9]*)\]/', $matches_string, $matches);

	$year = get_option('sdpvs_year_option');
	$searchyear = absint($year['year_number']);
	$authoroptions = get_option('sdpvs_author_option');
	$searchauthor = absint($authoroptions['author_number']);
	
	$whichlist = filter_var($_POST['whichlist'], FILTER_SANITIZE_STRING);
	// $howmuch = filter_var($_POST['howmuch'], FILTER_SANITIZE_STRING);

	if (isset($_POST['all'])) {
		$howmuch = "all";
	} elseif (isset($_POST['graph'])) {
		$howmuch = "graph";
	} elseif (isset($_POST['list'])) {
		$howmuch = "list";
	}
	
	if("" != $searchauthor){
		$user = get_user_by( 'id', $searchauthor );
		$extradesc = ": $user->display_name";
	}else{
		$extradesc = "";
	}

	if ($searchyear)
		$title = ucfirst($whichlist) . ' Stats' . $extradesc;
	else
		$title = ucfirst($whichlist) . ' Stats'. $extradesc;

	$color = $sdpvs_lists -> sdpvs_color_list();
	$link = "https://wordpress.org/plugins/post-volume-stats/";
	$linkdesc = "Post Volume Stats";

	if ("all" == $howmuch or "graph" == $howmuch) {
		$post_content = $sdpvs_bar -> sdpvs_comparison_line_graph($whichlist, $matches, $searchauthor, $color,"y");
	}

	if ("all" == $howmuch or "list" == $howmuch) {
		$post_content .= $sdpvs_lists -> sdpvs_posts_per_cat_tag_list($whichlist, $searchyear, $searchauthor, 'export', $matches, $color);
	}
	$post_content .= '<p class="alignright">Stats presented with ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';

	$my_post = array('post_title' => $title, 'post_content' => $post_content, 'post_status' => 'draft');
	// Insert the post into the database and get the post ID.
	$post_id = wp_insert_post($my_post, $wp_error);

	$url = admin_url("post.php?post=$post_id&action=edit");

	if (wp_redirect($url)) {
		exit ;
	}
}

add_action('admin_post_export_lists', 'sdpvs_admin_export_lists');

/*****************
 ** ADMIN NOTICE...
 *****************/

add_action('admin_notices', 'sdpvs_check_activation_notice');
function sdpvs_check_activation_notice() {
	if (!get_transient('sdpvs-admin-notice-004')) {
		$sdpvs_link = admin_url('admin.php?page=' . SDPVS__PLUGIN_FOLDER);
		echo '<div id="sdpvs-notice" class="notice notice-info is-dismissible"><p class="sdpvs"><strong>' . sprintf(wp_kses(__('NEW to <a href="%1$s">Post Volume Stats</a>: Compare tags and categories in line graphs.', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_link)) . '</strong></p></div>';
	}
}
?>
