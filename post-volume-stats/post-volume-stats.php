<?php
/**
 * @package post-volume-stats
 * @version 2.3.01
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 2.3.01
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

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);

		$sdpvs_info = new sdpvsInfo();

		$sdpvs_bar = new sdpvsBarChart();

		$sdpvs_pie = new sdpvsPieChart();

		$content = __("<h1 class='sdpvs'>Post Volume Stats</h1>", 'post-volume-stats');
		$content .= __("<p class='sdpvs'>These are the post volume stats for " . get_bloginfo('name') . ". You can now choose to modify the time-based stats to a specific year in the \"Post Volume Stats\" link in the \"Settings\" menu.</p>", 'post-volume-stats');

		// posts per category pie chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("category");
		$content .= "</div>";

		// posts per tag pie chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("tag");
		$content .= "</div>";

		$content .= "<hr>";

		// year bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("year", $searchyear);
		if ($searchyear) {
			$content .= "<em>Selected year: $searchyear, go to settings to change.</em>";
		}
		$content .= "</div>";

		// posts per day of the week bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofweek", $searchyear);
		$content .= "</div>";

		// posts per hour of the day bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("hour", $searchyear);
		$content .= "</div>";

		// posts per month bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("month", $searchyear);
		$content .= "</div>";

		// posts per day of the month bar chart
		$content .= "<div class='sdpvs_col'>";
		$content .= $sdpvs_bar -> sdpvs_draw_bar_chart_svg("dayofmonth", $searchyear);
		$content .= "</div>";

		// DIV for loading
		$content .= "<div id='sdpvs_loading'>";
		$content .= "</div>";

		// Div for ajax list box
		$content .= "<div id='sdpvs_listcontent'>";
		$content .= "</div>";
	}

	$content .= $sdpvs_info -> sdpvs_info();

	// Stop the timer
	$time_end = microtime(true);
	$elapsed_time = sprintf("%.5f", $time_end - $time_start);
	$content .= __("<p>	Script time elapsed: " . $elapsed_time . " seconds</p>", 'post-volume-stats');

	echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'post-volume-stats'), __('Post Volume Stats', 'post-volume-stats'), 'manage_options', dirname(__FILE__), 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/*************
 ** UI
 *************/

/**
 * Add an admin submenu link under Settings
 */
function sdpvs_add_options_submenu_page() {
	add_submenu_page('options-general.php', // admin page slug
	__('Post Volume Stats Settings', 'post-volume-stats'), // page title
	__('Post Volume Stats', 'post-volume-stats'), // menu title
	'manage_options', // capability required to see the page
	'post_volume_stats_options', // admin page slug, e.g. options-general.php?page=post_volume_stats_options
	'sdpvs_options_page' // callback function to display the options page
	);
}

add_action('admin_menu', 'sdpvs_add_options_submenu_page');

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
function sanitize( $input ){
	$new_input = array();
	if( isset( $input['year_number'] ) ){
		$new_input['year_number'] = absint( $input['year_number'] );
	}
	return $new_input;
}

/**
* Build the options page
*/
function sdpvs_options_page() {
$sdpvs_info = new sdpvsInfo();
$years = $sdpvs_info -> sdpvs_how_many_years_of_posts();
          ?>
 
     <div class="wrap">
            
          <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
           
          <div id="poststuff">
               <div id="post-body">
                    <div id="post-body-content">
                         <form method="post" action="options.php">
                              <?php settings_fields('sdpvs_year_option'); ?>
                              <?php $options = get_option('sdpvs_year_option'); ?>
                              <?php $selected = absint($options['year_number']); ?>
                              <table class="form-table">
                                   <tr valign="top"><th scope="row"><?php _e('Choose to limit the time-based stats to a particular year?', 'post-volume-stats'); ?></th>
                                        <td>
                                             <select name="sdpvs_year_option[year_number]" id="year-number">
                                                  
                                                  <option value="">All Years</option>
                                                  <?php
												for ($i = 0; $i <= $years; $i++) {
													$searchyear = date('Y') - $i;
													if ($searchyear == $selected) {
														echo "<option value=\"$searchyear\" SELECTED >$searchyear</option>";
													} else {
														echo "<option value=\"$searchyear\">$searchyear</option>";
													}

												}
                                                  	?>
                                             </select><br />
                                             <label class="description" for="sdpvs_year_option[year_number]"><?php _e('Select a year!', 'post-volume-stats'); ?></label>
                                             <?php submit_button(); ?>
                                        </td>
                                   </tr>
                              </table>
                         </form>
                    </div> <!-- end post-body-content -->
               </div> <!-- end post-body -->
          </div> <!-- end poststuff -->
     </div><?
	}

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
	echo $sdpvs_lists -> sdpvs_posts_per_category_list();
	} elseif ("tag" == $answer) {
	echo $sdpvs_lists -> sdpvs_posts_per_tag_list();
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
