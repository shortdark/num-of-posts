<?php

defined('ABSPATH') or die('No script kiddies please!');

/***************************
 ** USER INPUT AND SETTINGS
 ***************************/

/**
 * Register the settings
 */
function sdpvs_register_date_settings() {
    register_setting('sdpvs_year_option', // settings section
    'sdpvs_year_option' // setting name
    );
    add_settings_section( 'sdpvs_year_option', 'Filter By Year Or Date Range', 'sdpvs_sanitize', 'post-volume-stats-daterange' );
    add_settings_field('year_number', // ID
    'Select a Year', // Title
    'sdpvs_year_field_callback', 'post-volume-stats-daterange', 'sdpvs_year_option');
    add_settings_field('start_date', // ID
    'Date Range: start date', // Title
    'sdpvs_startdate_field_callback', 'post-volume-stats-daterange', 'sdpvs_year_option');
    add_settings_field('end_date', // ID
    'Date Range: end date', // Title
    'sdpvs_enddate_field_callback', 'post-volume-stats-daterange', 'sdpvs_year_option');
}
add_action('admin_init', 'sdpvs_register_date_settings');


function sdpvs_register_text_settings() {
    register_setting('sdpvs_text_option', // settings section
        'sdpvs_text_option' // setting name
    );
    add_settings_section( 'sdpvs_text_option', 'Filter By Text', 'sdpvs_sanitize', 'post-volume-stats-textfilter' );
    add_settings_field('search_text', // ID
        'Filter by Post Content', // Title
        'sdpvs_searchtext_field_callback', 'post-volume-stats-textfilter', 'sdpvs_text_option');

}
add_action('admin_init', 'sdpvs_register_text_settings');


function sdpvs_register_author_settings() {
    register_setting('sdpvs_author_option', // settings section
    'sdpvs_author_option', // setting name
    'sdpvs_sanitize');
    add_settings_field('author_number', // ID
    'Author Number', // Title
    '', SDPVS__PLUGIN_FOLDER);
}
add_action('admin_init', 'sdpvs_register_author_settings');


function sdpvs_register_general_settings() {
    register_setting( 'sdpvs_general_option', 'sdpvs_general_settings' );
    add_settings_section( 'sdpvs_general_settings', 'General Settings', 'sdpvs_sanitize_general', 'post-volume-stats-settings' );
    add_settings_field( 'startweekon', 'Start Week On', 'sdpvs_field_one_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'rainbow', 'Rainbow Lists', 'sdpvs_field_two_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'authoroff', 'Number of Users (Author and above) who Create Posts', 'sdpvs_field_three_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'customoff', 'Display Custom Taxonomy stats', 'sdpvs_field_four_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'customvalue', 'Select a Taxonomy to view', 'sdpvs_field_five_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'admintool', 'Put a link to Post Volume Stats in the Admin Toolbar', 'sdpvs_field_six_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'exportcsv', 'Allow export of CSV', 'sdpvs_field_seven_callback', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'showrange', 'Show Date Range and Text Filter pages', 'sdpvs_field_callback_date_range', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'maxinterval', 'Maximum post interval to show', 'sdpvs_field_callback_max_interval', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'showimage', 'Images per Post', 'sdpvs_field_callback_image', 'post-volume-stats-settings', 'sdpvs_general_settings' );
    add_settings_field( 'showcomment', 'Comments per Post', 'sdpvs_field_callback_comment', 'post-volume-stats-settings', 'sdpvs_general_settings' );
}

add_action('admin_init', 'sdpvs_register_general_settings');

function sdpvs_startdate_field_callback() {
    $options = get_option('sdpvs_year_option');
    $selected = '';
    if (!empty($options['start_date'])) {
        $selected = $options['start_date'];
    }
    echo "<div style='display: block; padding: 5px;'>";
    echo "<label><input name=\"sdpvs_year_option[start_date]\" id=\"start-date\" value=\"$selected\" placeholder=\"YYYY-MM-DD\">";
    echo "</label><br>";
    echo "</div>";
}

function sdpvs_enddate_field_callback() {
    $options = get_option('sdpvs_year_option');
    $selected = '';
    if (!empty($options['end_date'])) {
        $selected = $options['end_date'];
    }

    echo "<div style='display: block; padding: 5px;'>";
    echo "<label><input name=\"sdpvs_year_option[end_date]\" id=\"end-date\" value=\"$selected\" placeholder=\"YYYY-MM-DD\">";
    echo "</label><br>";
    echo "</div>";
}


function sdpvs_year_field_callback() {
    $selected = 0;
    $author = 0;

    $options = get_option('sdpvs_year_option');
    if (false !== $options) {
        $selected = absint($options['year_number']);
    }

    $authoroptions = get_option('sdpvs_author_option');
    if (false !== $authoroptions) {
        $author = absint($authoroptions['author_number']);
    }

    // Create an instance of the required class
    $sdpvs_info = new sdpvsInfo();
    $years = $sdpvs_info->sdpvs_first_year($author);

    echo "<div style='display: block; padding: 5px;'>";

    echo "<label><select name=\"sdpvs_year_option[year_number]\" id=\"year-number\">";
    
    if(empty($selected)){
        echo "<option value=\"\" selected=\"selected\"></option>";
    }else{
        echo "<option value=\"\"></option>";
    }

    $i=0;
    while(isset($years[$i]['name'])){
        if(0 < $years[$i]['volume']){
            if($years[$i]['name'] == $selected){
                echo "<option value=\"{$years[$i]['name']}\" selected=\"selected\">{$years[$i]['name']}</option>";
            }else{
                echo "<option value=\"{$years[$i]['name']}\">{$years[$i]['name']}</option>";
            }
        }
        $i++;
    }

    echo "</select></label><br>";
    echo "</div>";
}

function sdpvs_searchtext_field_callback() {
    $options = get_option('sdpvs_text_option');
    $selected = '';
    if (!empty($options['search_text'])) {
        $selected = $options['search_text'];
    }

    echo "<div style='display: block; padding: 5px;'>";
    echo "<label><input name=\"sdpvs_text_option[search_text]\" id=\"search-text\" value=\"$selected\" placeholder=\"Filter text...\">";
    echo " (i.e. only show the posts which contain a keyword on the main Post Volume Stats page)";
    echo "</label><br>";
    echo "</div>";
}


function sdpvs_field_one_callback() {
    $startweek = 'sunday';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $startweek = htmlspecialchars( $genoptions['startweekon'], ENT_QUOTES);
    }

    
    // This gives the integer the user has for their blog, 0=sunday, 1=monday, etc
    // $blogstartweek = get_option( 'start_of_week' );
    
    echo "<div style='display: block; padding: 5px;'>";
    if('sunday' === $startweek){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"sunday\" checked=\"checked\">Sunday (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"monday\">Monday</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"sunday\">Sunday (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[startweekon]\" value=\"monday\" checked=\"checked\">Monday</label>";
    }
    echo "</div>";
}

function sdpvs_field_two_callback() {
    $listcolors = 'on';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $listcolors = htmlspecialchars( $genoptions['rainbow'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('on' === $listcolors){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"on\" checked=\"checked\">On (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"off\">Off</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"on\">On (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[rainbow]\" value=\"off\" checked=\"checked\">Off</label>";
    }
    
    echo "</div>";
}

function sdpvs_field_three_callback() {
    $authoroff = 'multiple';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $authoroff = htmlspecialchars( $genoptions['authoroff'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('multiple' === $authoroff){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"multiple\" checked=\"checked\">More than one (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"one\">One</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"multiple\">More than one (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[authoroff]\" value=\"one\" checked=\"checked\">One</label>";
    }
    
    echo "</div>";
}

function sdpvs_field_four_callback() {
    $customoff = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $customoff = htmlspecialchars( $genoptions['customoff'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $customoff){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    
    echo "</div>";
}

function sdpvs_field_five_callback() {
    $customvalue = '';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $customoff = htmlspecialchars( $genoptions['customoff'], ENT_QUOTES);
        $customvalue = htmlspecialchars( $genoptions['customvalue'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    
    // Custom Taxonomies
    $args = array(
        'public'   => true,
        '_builtin' => false
    ); 
    $all_taxes = get_taxonomies( $args );
    $count_taxes = count( $all_taxes );
    if( 1 < $count_taxes ){
        echo  "<select name=\"sdpvs_general_settings[customvalue]\">";
        echo  "<option name=\"sdpvs_general_settings[customvalue]\" value=\"_all_taxonomies\">Display All</option>";
        foreach ( $all_taxes as $taxonomy ) {
            if("category" != $taxonomy && "post_tag" != $taxonomy){
                $tax_labels = get_taxonomy($taxonomy);
                if($taxonomy == $customvalue){
                    echo  "<option name=\"sdpvs_general_settings[customvalue]\" value=\"$taxonomy\" selected=\"selected\">$tax_labels->label</option>";
                }elseif( $taxonomy && "" != $taxonomy ){
                    echo  "<option name=\"sdpvs_general_settings[customvalue]\" value=\"$taxonomy\">$tax_labels->label</option>";
                }
            }
        }
        echo  "</select>";
        echo "<p>Selecting \"Display All\" may cause the stats to load more slowly, especially if you have a lot of posts and/or a lot of custom taxonomies.</p>";
    }elseif( 1 == $count_taxes ){
        $short_tax = array_values($all_taxes);
        echo 'Only one custom taxonomy found: ' . $short_tax[0];
        echo  "<input type=\"hidden\" name=\"sdpvs_general_settings[customvalue]\" value=\"{$short_tax[0]}\">";
    }elseif( 1 > $count_taxes || !$count_taxes ){
        echo  "<p>No Custom Taxonomies found.</p>";
        echo  "<input type=\"hidden\" name=\"sdpvs_general_settings[customvalue]\" value=\"\">";
    }
    echo "</div>";
}


function sdpvs_field_six_callback() {
    $admintool = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $admintool = htmlspecialchars( $genoptions['admintool'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $admintool){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    echo "</div>";
}

function sdpvs_field_seven_callback() {
    $exportcsv = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $exportcsv = htmlspecialchars( $genoptions['exportcsv'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $exportcsv){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    echo "<p>This will only work if your admin directory is still called \"wp-admin\", it will not work if you have re-named it. The CSV output will be comma separated! Some security plugins block the ability for you to download files like this so please bear that in mind if this does not work for you. Please leave feedback at the <a href=\"https://wordpress.org/plugins/post-volume-stats/\" target=\"_blank\">PVS WordPress Plugin page</a>.</p>";
    echo "</div>";
}

function sdpvs_field_callback_date_range() {
    $showrange = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $showrange = htmlspecialchars( $genoptions['showrange'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $showrange){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showrange]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showrange]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showrange]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showrange]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    echo "</div>";
}

function sdpvs_field_callback_max_interval() {
    $maxinterval = 30;
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $maxinterval = absint( $genoptions['maxinterval']);
    }

    echo "<div style='display: block; padding: 5px;'>";
    echo  "<select name=\"sdpvs_general_settings[maxinterval]\">";
    for ( $i=0; $i <= 14; $i++ ) {
        $interval = 10 + ($i * 5);
        if($interval == $maxinterval){
            echo  "<option name=\"sdpvs_general_settings[maxinterval]\" value=\"$interval\" selected=\"selected\">$interval days</option>";
        }else{
            echo  "<option name=\"sdpvs_general_settings[maxinterval]\" value=\"$interval\">$interval days</option>";
        }
    }
    echo  "</select>";
    echo "<p>Default is 30 days. Bigger interval = longer time to load.</p>";
    echo "</div>";
}

function sdpvs_field_callback_image() {
    $showimage = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $showimage = htmlspecialchars( $genoptions['showimage'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $showimage){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showimage]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showimage]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showimage]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showimage]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    echo '<p>';
    echo __('The new <strong>"Images per Post"</strong> bar chart counts the number of images in the content of published posts, also the number of gallery images in the post. '). '<br /><span style="color:red;">'. __('If you create image galleries in a way that is not the built-in WordPress method (such as using a gallery plugin) those images may not show up on the chart. ') . '</span>' . __('If you use a gallery plugin and it is not included in the images per post count please let me know which plugin you are using. ');
    echo '</p>';
    echo "</div>";
}

function sdpvs_field_callback_comment() {
    $showcomment = 'no';
    $genoptions = get_option('sdpvs_general_settings');
    if (false !== $genoptions) {
        $showcomment = htmlspecialchars( $genoptions['showcomment'], ENT_QUOTES);
    }

    echo "<div style='display: block; padding: 5px;'>";
    if('no' === $showcomment){
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showcomment]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showcomment]\" value=\"yes\">Yes</label>";
    }else{
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showcomment]\" value=\"no\">No (default)</label><br>";
        echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[showcomment]\" value=\"yes\" checked=\"checked\">Yes</label>";
    }
    echo '<p>';
    echo __('The new <strong>"Comments per Post"</strong> bar chart counts the number of approved comments for each published post. ');
    echo '</p>';
    echo "</div>";
}


/**
 * Sanitize the field
 */
function sdpvs_sanitize($input) {
    $new_input = array();
    if (isset($input['year_number'])) {
        $new_input['year_number'] = absint($input['year_number']);
    }
    if (isset($input['start_date'])) {
        $new_input['start_date'] = $input['start_date'];
    }
    if (isset($input['end_date'])) {
        $new_input['end_date'] = $input['end_date'];
    }
    if (isset($input['author_number'])) {
        $new_input['author_number'] = absint($input['author_number']);
    }
    return $new_input;
}

function sdpvs_sanitize_general($input) {
    $new_input = array();
    if (isset($input['startweekon'])) {
        $new_input['startweekon'] = htmlspecialchars( $input['startweekon'], ENT_QUOTES);
    }
    if (isset($input['rainbow'])) {
        $new_input['rainbow'] = htmlspecialchars( $input['rainbow'], ENT_QUOTES);
    }
    return $new_input;
}

