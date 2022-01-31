<?php
/**
 * @package post-volume-stats
 * @version 3.3.08
 */
/*
 * Plugin Name: Post Volume Stats
 * Plugin URI: https://www.postvolumestats.com/
 * Description: Displays the post stats in the admin area with pie and bar charts, also exports tag and category stats to detailed lists and line graphs that can be exported to posts.
 * Author: Neil Ludlow
 * Text Domain: post-volume-stats
 * Version: 3.3.08
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
define('SDPVS__PLUGIN_SETTINGS', 'post-volume-stats-settings');
define('SDPVS__FILTER_RESULTS', 'post-volume-stats-daterange');
define('SDPVS__VERSION_NUMBER', '3.3.08');

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
require_once (SDPVS__PLUGIN_DIR . 'sdpvs_settings.php');

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
        $sdpvs_content = new sdpvsMainContent();

        // Load content for the main page
        $sdpvs_content->sdpvs_page_content();

        // DIV for loading
        echo "<div id='sdpvs_loading'>";
        echo "</div>";

        // Div for ajax list box
        echo "<div id='sdpvs_listcontent'>";
        echo "</div>";

        // Div for ajax compare years box
        echo "<div id='sdpvs_listcompare'>";
        echo "</div>";
    }

    $sdpvs_info->sdpvs_info();


    echo '<div style="display:block"><span class="dashicons dashicons-chart-pie"></span></div>';

    // Stop the timer
    $time_end = microtime(true);

    // Footer
    $sdpvs_info->drawFooter($time_start,$time_end);

}

// Category page
function sdpvs_category_page() {
    if (is_admin()) {

        // Start the timer
        $time_start = microtime(true);

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();
        $sdpvs_sub = new sdpvsSubPages();

        // Call the method
        $sdpvs_sub->sdpvs_combined_page_content('category');

        // DIV for loading
        echo "<div id='sdpvs_loading'>";
        echo "</div>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);

    }
}

// Tag page
function sdpvs_tag_page() {
    if (is_admin()) {

        // Start the timer
        $time_start = microtime(true);

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();
        $sdpvs_sub = new sdpvsSubPages();

        // Call the method
        $sdpvs_sub->sdpvs_combined_page_content('tag');

        // DIV for loading
        echo "<div id='sdpvs_loading'>";
        echo "</div>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);

    }
}

// Custom page
function sdpvs_custom_page() {
    if (is_admin()) {

        // Start the timer
        $time_start = microtime(true);

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();
        $sdpvs_sub = new sdpvsSubPages();

        $sdpvs_page_value = htmlspecialchars ( $_SERVER['QUERY_STRING'], ENT_QUOTES);
        // When changing year the "&settings-updated=true" string is added to the URL!
        preg_match('/^page=post-volume-stats-([^&]*)(&settings-updated=true)?/',$sdpvs_page_value,$matches);
        $customvalue = htmlspecialchars ( $matches[1], ENT_QUOTES);

        // Call the method
        $sdpvs_sub->sdpvs_combined_page_content($customvalue);

        // DIV for loading
        echo "<div id='sdpvs_loading'>";
        echo "</div>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);

    }
}




// Settings page
function sdpvs_settings_page() {
    if (is_admin()) {

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();

        // Start the timer
        $time_start = microtime(true);

        // Content goes here
        echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Settings', 'post-volume-stats') . '</h1>';

        echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
        settings_fields( 'sdpvs_general_option' );
        do_settings_sections( 'post-volume-stats-settings' );
        submit_button('Save');
        echo "</form>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);
    }
}

// Date Range page
function sdpvs_date_range_select_page() {
    if (is_admin()) {

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();

        // Start the timer
        $time_start = microtime(true);

        // Content goes here
        echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Date Range', 'post-volume-stats') . '</h1>';
        echo '<p>On this page you can filter the results on the main Post Volume Stats page by a year/date range.</p>';
        echo '<p>'  . esc_html__('
You can either select a year or you can select a date range and filter the results to only search for posts which have a certain text string within them.
Selecting a year on this page is an alternative to clicking the bars of the "Year" bar chart on the other pages. To de-select the year and view all years together select the blank option at the top.
Only if the "Year" is blank will the date range be used. 
You must enter both a start date and an end date. 
If a date range is entered (with no year selected) it will be applied to the main page, but not the Tag/Category/Custom pages.
There is a bug where any posts on the "end date" are not counted. To fix this, add an extra day onto the "end date" to get the desired range.', 'post-volume-stats') . '</p>';

        echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
        settings_fields( 'sdpvs_year_option' );
        do_settings_sections( 'post-volume-stats-daterange' );
        submit_button('Save');
        echo "</form>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);
    }
}

// Text Filter page
function sdpvs_text_filter_page() {
    if (is_admin()) {

        // Create an instance of the required classes
        $sdpvs_info = new sdpvsInfo();

        // Start the timer
        $time_start = microtime(true);

        // Content goes here
        echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Text Filter', 'post-volume-stats') . '</h1>';
        echo '<p>On this page you can filter the results on the main Post Volume Stats page by a word.</p>';
        echo '<p>' . esc_html__('
Filtering by "Post Content" allows you to only display posts which contain a certain word in the text.
This should work for the main Post Volume Stats bar charts, pie charts, and "Show Data" lists.
The data may look incorrect for tags and categories because if a post has multiple tags and categories the post will 
appear more than once in the pie charts.', 'post-volume-stats') . '</p>';

        echo "<form action='" . esc_url(admin_url('options.php')) . "' method='POST'>";
        settings_fields( 'sdpvs_text_option' );
        do_settings_sections( 'post-volume-stats-textfilter' );
        submit_button('Save');
        echo "</form>";

        // Stop the timer
        $time_end = microtime(true);

        // Footer
        $sdpvs_info->drawFooter($time_start,$time_end);
    }
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'sdpvs_admin_plugin_settings_link' );
function sdpvs_admin_plugin_settings_link( $links ) { 
    $settings_link = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=post-volume-stats-settings') ) .'">'.__('Settings', 'post-volume-stats').'</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'sdpvs_admin_plugin_pvs_link' );
function sdpvs_admin_plugin_pvs_link( $links ) { 
    $pvs_link = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=post-volume-stats') ) .'">'.__('Post Volume Stats', 'post-volume-stats').'</a>';
    array_unshift( $links, $pvs_link );
    return $links;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
    $genoptions = get_option('sdpvs_general_settings');
    $customoff = 'no';
    $customvalue = '';
    $showrange = 'no';
    if (false !== $genoptions) {
        $customoff = htmlspecialchars ( $genoptions['customoff'], ENT_QUOTES);
        $customvalue = htmlspecialchars ( $genoptions['customvalue'], ENT_QUOTES);
        $showrange = htmlspecialchars ( $genoptions['showrange'], ENT_QUOTES);
    }

    add_menu_page(esc_html__('Post Volume Stats', 'post-volume-stats'), esc_html__('Post Volume Stats', 'post-volume-stats'), 'manage_options', __DIR__, 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
    add_submenu_page(__DIR__, esc_html__('Post Volume Stats: Categories', 'post-volume-stats'), esc_html__('Categories', 'post-volume-stats'), 'read', 'post-volume-stats-categories', 'sdpvs_category_page');
    add_submenu_page(__DIR__, esc_html__('Post Volume Stats: Tags', 'post-volume-stats'), esc_html__('Tags', 'post-volume-stats'), 'read', 'post-volume-stats-tags', 'sdpvs_tag_page');
    if( "yes" === $customoff && "_all_taxonomies" === $customvalue ){
        // Custom Taxonomies
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $all_taxes = get_taxonomies( $args );
        $count_taxes = count( $all_taxes );
        if( 1 < $count_taxes ){
            foreach ( $all_taxes as $taxonomy ) {
                if("category" != $taxonomy && "post_tag" != $taxonomy){
                    $tax_labels = get_taxonomy($taxonomy);
                    add_submenu_page(__DIR__, esc_html__('Post Volume Stats: ' . $tax_labels->label, 'post-volume-stats'), $tax_labels->label, 'read', 'post-volume-stats-' . $tax_labels->name, 'sdpvs_custom_page');
                }
            }
        }
    }elseif( "yes" === $customoff && "" !== $customvalue ){
        $customvalue = htmlspecialchars ( $genoptions['customvalue'], ENT_QUOTES);
        $tax_labels = get_taxonomy($customvalue);
        if (!empty($tax_labels)) {
            add_submenu_page(__DIR__, esc_html__('Post Volume Stats: ' . $tax_labels->label, 'post-volume-stats'), $tax_labels->label, 'read', 'post-volume-stats-' . $customvalue, 'sdpvs_custom_page');
        }
    }
    if( "yes" === $showrange ){
        add_submenu_page(__DIR__, esc_html__('Post Volume Stats: Date Range', 'post-volume-stats'), esc_html__('Date Range', 'post-volume-stats'), 'manage_options', 'post-volume-stats-daterange', 'sdpvs_date_range_select_page');
        add_submenu_page(__DIR__, esc_html__('Post Volume Stats: Text Filter', 'post-volume-stats'), esc_html__('Text Filter', 'post-volume-stats'), 'manage_options', 'post-volume-stats-textfilter', 'sdpvs_text_filter_page');
    }
    add_submenu_page(__DIR__, esc_html__('Post Volume Stats: Settings', 'post-volume-stats'), esc_html__('Settings', 'post-volume-stats'), 'manage_options', 'post-volume-stats-settings', 'sdpvs_settings_page');
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/**
 * Load plugin textdomain.
 */
function sdpvs_load_textdomain() {
    load_plugin_textdomain('post-volume-stats', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'sdpvs_load_textdomain');







/*****************
 ** ADMIN TOOLBAR
 *****************/

// Add Toolbar Menus
function sdpvs_custom_toolbar() {
    global $wp_admin_bar;

    $admintool = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $admintool = htmlspecialchars ( $genoptions['admintool'], ENT_QUOTES);
    }


    if("yes" === $admintool){
        $url = admin_url("admin.php?page=" . SDPVS__PLUGIN_FOLDER);
        $args = array(
            'id'     => 'sdpvs_link',
            'title'  => __( 'PVS', 'text_domain' ),
            'href'   => $url,
            'group'  => false
        );
        $wp_admin_bar->add_node( $args );
    }

}
add_action( 'wp_before_admin_bar_render', 'sdpvs_custom_toolbar' );





/*************
 ** AJAX...
 *************/

function sdpvs_load_all_admin_scripts() {

    // Load Boostrap CSS
    // wp_enqueue_style( 'bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' );

    // Load plugin CSS
    wp_enqueue_style('sdpvs_css', plugins_url('sdpvs_css.css', __FILE__), '', '1.0.6', 'screen');

    // Importing external JQuery UI element using "wp-includes/script-loader.php"
    wp_enqueue_script("jquery-ui-draggable");

    wp_register_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__));
    wp_enqueue_script('sdpvs_loader', plugins_url('sdpvs_loader.js', __FILE__), array('jquery'), '1.0.3', true);

    $whichdata = "";
    $whichcats = "";
    $whichtags = "";
    $whichcustom = "";
    $comparedata = "";

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
    //To use this variable in javascript use "sdpvs_vars.whichcustom"
    'whichcustom' => $whichcustom,
    //To use this variable in javascript use "sdpvs_vars.comparedata"
    'comparedata' => $comparedata,
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
    $gotit = htmlspecialchars($_POST['whichdata'], ENT_QUOTES);
    $after_equals = strpos($gotit, "=") + 1;
    $answer = substr($gotit, $after_equals);

    $searchyear = 0;
    $year = get_option('sdpvs_year_option');
    if (false !== $year) {
        $searchyear = absint($year['year_number']);
    }

    $start_date='';
    if(isset($year['start_date'])){
        $start_date = htmlspecialchars ( $year['start_date'], ENT_QUOTES);
    }
    $end_date='';
    if(isset($year['end_date'])){
        $end_date = htmlspecialchars ( $year['end_date'], ENT_QUOTES);
    }
    $textoption = get_option('sdpvs_text_option');
    $search_text='';
    if(isset($textoption['search_text'])){
        $search_text = htmlspecialchars ( $textoption['search_text'], ENT_QUOTES);
    }

    $searchauthor = 0;
    $authoroptions = get_option('sdpvs_author_option');
    if (false !== $authoroptions) {
        $searchauthor = absint($authoroptions['author_number']);
    }


    if ("year" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_year_list($searchauthor, $search_text);
    } elseif ("hour" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_hour_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("dayofweek" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_dayofweek_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("month" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_month_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("dayofmonth" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_day_of_month_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("author" === $answer) {
        echo $sdpvs_lists->sdpvs_posts_per_author_list($searchyear, $start_date, $end_date, $search_text);
    } elseif ("words" === $answer){
        echo $sdpvs_lists->sdpvs_words_per_post_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("images" === $answer){
        echo $sdpvs_lists->sdpvs_images_per_post_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("comments" === $answer){
        echo $sdpvs_lists->sdpvs_comments_per_post_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } elseif ("interval" === $answer){
        echo $sdpvs_lists->sdpvs_interval_between_posts_list($searchyear, $searchauthor, $start_date, $end_date, $search_text);
    } else {
        echo $sdpvs_lists->sdpvs_posts_per_cat_tag_list($answer, $searchyear, $searchauthor, $start_date, $end_date, 'admin', '', '', $search_text);
    }

    // Always die() AJAX
    die();
}

add_action('wp_ajax_sdpvs_get_results', 'sdpvs_process_ajax');

function sdpvs_compare_data_over_years() {
    // Security check
    check_ajax_referer('num-of-posts', 'security');

    // create an instance of the list class
    $sdpvs_lists = new sdpvsTextLists();

    // Extract the variable from serialized string
    $gotit = htmlspecialchars($_POST['comparedata'], ENT_QUOTES);
    $after_equals = strpos($gotit, "=") + 1;
    $answer = substr($gotit, $after_equals);

    $searchauthor = 0;
    $authoroptions = get_option('sdpvs_author_option');
    if (false !== $authoroptions) {
        $searchauthor = absint($authoroptions['author_number']);
    }

    $textoption = get_option('sdpvs_text_option');
    $search_text='';
    if(isset($textoption['search_text'])){
        $search_text = htmlspecialchars ( $textoption['search_text'], ENT_QUOTES);
    }

    echo $sdpvs_lists->sdpvs_compare_years_rows($answer, $searchauthor,$search_text);

    // Always die() AJAX
    die();
}

add_action('wp_ajax_sdpvs_compare_years', 'sdpvs_compare_data_over_years');

function sdpvs_cats_lists() {
    // Security check
    check_ajax_referer('num-of-posts', 'security');

    $sdpvs_sub = new sdpvsSubPages();

    // Extract the variables from serialized string
    $gotit = htmlspecialchars($_POST['whichcats'], ENT_QUOTES);
    preg_match_all('/=([0-9]*)/', $gotit, $matches);

    echo $sdpvs_sub->update_ajax_lists('category', $matches);

    // Always die() AJAX
    die();
}

add_action('wp_ajax_sdpvs_select_cats', 'sdpvs_cats_lists');

function sdpvs_tags_lists() {
    // Security check
    check_ajax_referer('num-of-posts', 'security');

    $sdpvs_sub = new sdpvsSubPages();

    // Extract the variables from serialized string
    $gotit = htmlspecialchars($_POST['whichtags'], ENT_QUOTES);
    preg_match_all('/=([0-9]*)/', $gotit, $matches);

    echo $sdpvs_sub->update_ajax_lists('tag', $matches);

    // Always die() AJAX
    die();
}

add_action('wp_ajax_sdpvs_select_tags', 'sdpvs_tags_lists');

function sdpvs_custom_lists() {
    // Security check
    check_ajax_referer('num-of-posts', 'security');

    $customvalue = '';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $customvalue = htmlspecialchars( $genoptions['customvalue'], ENT_QUOTES);
    }

    $sdpvs_sub = new sdpvsSubPages();

    // Extract the variables from serialized string
    $gotit = htmlspecialchars($_POST['whichcustom'], ENT_QUOTES);
    preg_match_all('/=([0-9]*)/', $gotit, $matches);

    if("_all_taxonomies" === $customvalue){
        preg_match('/customname=([0-9a-zA-Z\-_]*)&/', $gotit, $filtertype);
        $customvalue = $filtertype[1];
    }

    echo $sdpvs_sub->update_ajax_lists($customvalue, $matches);

    // Always die() AJAX
    die();
}

add_action('wp_ajax_sdpvs_select_custom', 'sdpvs_custom_lists');

function sdpvs_remove_admin_notice() {
    // Security check
    check_ajax_referer('num-of-posts', 'security');

    // activate this with AJAX, #sdpvs-notice
    set_transient('sdpvs-admin-notice-006', true, 0);

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
    $matches_string = htmlspecialchars($_POST['matches'], ENT_QUOTES);
    preg_match_all('/\[([0-9]*)\]/', $matches_string, $matches);

    $searchyear = 0;
    $year = get_option('sdpvs_year_option');
    if (false !== $year) {
        $searchyear = absint($year['year_number']);
    }

    $searchauthor = 0;
    $authoroptions = get_option('sdpvs_author_option');
    if (false !== $authoroptions) {
        $searchauthor = absint($authoroptions['author_number']);
    }

    $whichlist = htmlspecialchars($_POST['whichlist'], ENT_QUOTES);

    if (isset($_POST['all'])) {
        $howmuch = "all";
    } elseif (isset($_POST['graph'])) {
        $howmuch = "graph";
    } elseif (isset($_POST['list'])) {
        $howmuch = "list";
    }

    if(0 != $searchauthor){
        $user = get_user_by( 'id', $searchauthor );
        $extradesc = ": $user->display_name";
    }else{
        $extradesc = "";
    }

    $title = ucfirst($whichlist) . ' Stats' . $extradesc;


    $color = $sdpvs_lists->sdpvs_color_list();
    $link = "https://wordpress.org/plugins/post-volume-stats/";
    $linkdesc = "Post Volume Stats";

    if ("all" === $howmuch || "graph" === $howmuch) {
        $post_content = $sdpvs_bar->sdpvs_comparison_line_graph($whichlist, $matches, $searchauthor, $color,"y");
    }

    if ("all" === $howmuch || "list" === $howmuch) {
        $post_content .= $sdpvs_lists->sdpvs_posts_per_cat_tag_list($whichlist, $searchyear, $searchauthor, '','','export', $matches, $color);
    }

    if ("all" === $howmuch || "graph" === $howmuch || "list" === $howmuch) {
        $post_content .= '<p class="alignright">Stats presented with ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
        $my_post = array('post_title' => $title, 'post_content' => $post_content, 'post_status' => 'draft');
        // Insert the post into the database and get the post ID.
        $post_id = wp_insert_post($my_post);

        $url = admin_url("post.php?post=$post_id&action=edit");

        if (wp_redirect($url)) {
            exit ;
        }
    }

}

add_action('admin_post_export_lists', 'sdpvs_admin_export_lists');



/*****************
 ** CSV DOWNLOAD...
 *****************/

function add_endpoint() {
    add_rewrite_endpoint( 'download-csv', EP_NONE );
}
add_action( 'init', 'add_endpoint' );

function sdpvs_download_redirect() {
    $answer = "";
    $exportcsv = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $exportcsv = htmlspecialchars( $genoptions['exportcsv'], ENT_QUOTES);
    }

    $authoroptions = get_option('sdpvs_author_option');
    if (!empty($authoroptions['author_number'])) {
        $searchauthor = absint($authoroptions['author_number']);
    } else {
        $searchauthor = null;
    }

    $textoption = get_option('sdpvs_text_option');
    $search_text='';
    if(isset($textoption['search_text'])){
        $search_text = htmlspecialchars( $textoption['search_text'], ENT_QUOTES);
    }

    if("yes"===$exportcsv && is_user_logged_in() ){

        $searchstring = $_SERVER['REQUEST_URI'];
        $pattern = "/\/wp-content\/plugins\/post-volume-stats\/download-csv\/([0-9a-zA-Z-]+)\.csv/";
        preg_match($pattern, $searchstring, $matches);
        if( isset($matches[1]) ){
            $answer = $matches[1];
        }

        if("words"!=$answer && "images"!=$answer && "comments"!=$answer && "hour"!=$answer && "dayofweek"!=$answer && "month"!=$answer && "dayofmonth"!=$answer && "tag"!=$answer && "category"!=$answer && "interval"!=$answer){
                #check that the taxonomy exists
                $foundit = 0;
                $args = array(
                    'public'   => true,
                    '_builtin' => false
                );
                $all_taxes = get_taxonomies( $args );
                foreach ( $all_taxes as $taxonomy ) {
                    if("category" != $taxonomy && "post_tag" != $taxonomy){
                        $tax_labels = get_taxonomy($taxonomy);
                        if($taxonomy == $answer || $tax_labels->label == $answer || $tax_labels->name == $answer){
                            $foundit = 1;
                        }
                    }
                }
                if(0 === $foundit){
                    return false;
                }
        }

        if($answer){
            // create an instance of the list class
            $sdpvs_lists = new sdpvsTextLists();
            $csv = $sdpvs_lists->sdpvs_create_csv_output($answer, $searchauthor,$search_text);
            $length = strlen($csv);

            // Download the file.
            ob_clean(); //clear buffer
            header('Content-Disposition: attachment; filename="' . $answer . '.csv"');
            header("Content-Type: application/force-download",true,200);
            header("Content-Length: " . $length);
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Connection: close");
            echo $csv;
            exit();
        }

    }
}
add_action( 'template_redirect', 'sdpvs_download_redirect' );

/*****************
 ** ADMIN NOTICE...
 *****************/

add_action('admin_notices', 'sdpvs_check_activation_notice');
function sdpvs_check_activation_notice() {
    if (!get_transient('sdpvs-admin-notice-006')) {
        # When a new admin notice is added, make sure to change "sdpvs-admin-notice-005" to the next number.
        # Also, remember to update the AJAX to remove the notice with the new number.

        $sdpvs_link = admin_url('admin.php?page=' . SDPVS__PLUGIN_FOLDER);
        $sdpvs_settings_link = admin_url('admin.php?page=' . SDPVS__PLUGIN_SETTINGS);
        echo '<div id="sdpvs-notice" class="notice notice-info is-dismissible"><p class="sdpvs">' . sprintf(wp_kses(__('NEW to <a href="%1$s">Post Volume Stats</a>: ', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_link)) . __('<strong>"Comments per post"</strong> bar chart added and <strong>"Images per post"</strong> bugfix. ') . sprintf(wp_kses(__('The new charts must be enabled in <a href="%1$s">PVS settings</a>. ', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_settings_link)) . '</p></div>';
    }
}

