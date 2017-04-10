<?php

defined('ABSPATH') or die('No script kiddies please!');

/***************************
 ** USER INPUT AND SETTINGS
 ***************************/

/**
 * Register the settings
 */
function sdpvs_register_settings() {
	register_setting('sdpvs_year_option', // settings section
	'sdpvs_year_option', // setting name
	'sdpvs_sanitize');
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
	'sdpvs_sanitize');
	add_settings_field('author_number', // ID
	'Author Number', // Title
	'', SDPVS__PLUGIN_FOLDER);
}

add_action('admin_init', 'sdpvs_register_author_settings');

function sdpvs_register_general_settings() {
	register_setting( 'sdpvs_general_option', 'sdpvs_general_settings' );
    add_settings_section( 'sdpvs_general_settings', 'General Settings', 'sdpvs_sanitize_general', SDPVS__PLUGIN_FOLDER );
    add_settings_field( 'startweekon', 'Start Week On', 'sdpvs_field_one_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'rainbow', 'Rainbow Lists', 'sdpvs_field_two_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'authoroff', 'Number of Users (Author and above) who Create Posts', 'sdpvs_field_three_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'customoff', 'Display Custom Taxonomy stats', 'sdpvs_field_four_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'customvalue', 'Select a Taxonomy to view', 'sdpvs_field_five_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'admintool', 'Put a link to Post Volume Stats in the Admin Toolbar', 'sdpvs_field_six_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
	add_settings_field( 'exportcsv', 'BETA - allow export of CSV', 'sdpvs_field_seven_callback', SDPVS__PLUGIN_FOLDER, 'sdpvs_general_settings' );
}

add_action('admin_init', 'sdpvs_register_general_settings');

function sdpvs_field_one_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$startweek = filter_var ( $genoptions['startweekon'], FILTER_SANITIZE_STRING);
	
	// This gives the integer the user has for their blog, 0=sunday, 1=monday, etc
	$blogstartweek = get_option( 'start_of_week' );
    
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

function sdpvs_field_two_callback() {
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

function sdpvs_field_three_callback() {
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

function sdpvs_field_four_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$customoff = filter_var ( $genoptions['customoff'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("no" == $customoff or !$customoff){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"yes\">Yes</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"no\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[customoff]\" value=\"yes\" checked=\"checked\">Yes</label>";
	}
	
	echo "</div>";
}

function sdpvs_field_five_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$customoff = filter_var ( $genoptions['customoff'], FILTER_SANITIZE_STRING);
	$customvalue = filter_var ( $genoptions['customvalue'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	
	// Custom Taxonomies
	$args = array(
		'public'   => true,
		'_builtin' => false
	); 
	$all_taxes = get_taxonomies( $args );
//	var_dump($all_taxes);
	$count_taxes = count( $all_taxes );
//	var_dump($count_taxes);
	if( 1 < $count_taxes ){
		echo  "<select name=\"sdpvs_general_settings[customvalue]\">";
		echo  "<option name=\"sdpvs_general_settings[customvalue]\" value=\"_all_taxonomies\">Display All</option>";
		foreach ( $all_taxes as $taxonomy ) {
			if("category" != $taxonomy and "post_tag" != $taxonomy){
				$tax_labels = get_taxonomy($taxonomy);
				if($taxonomy == $customvalue){
					echo  "<option name=\"sdpvs_general_settings[customvalue]\" value=\"$taxonomy\" selected=\"selected\">$tax_labels->label</option>";
				}elseif( $taxonomy and "" != $taxonomy ){
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
	}elseif( 1 > $count_taxes or !$count_taxes or "" == $count_taxes ){
		echo  "<p>No Custom Taxonomies found.</p>";
		echo  "<input type=\"hidden\" name=\"sdpvs_general_settings[customvalue]\" value=\"\">";
	}
	echo "</div>";
}


function sdpvs_field_six_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$admintool = filter_var ( $genoptions['admintool'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("no" == $admintool or !$admintool){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"yes\">Yes</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"no\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[admintool]\" value=\"yes\" checked=\"checked\">Yes</label>";
	}
	echo "</div>";
}

function sdpvs_field_seven_callback() {
	$genoptions = get_option('sdpvs_general_settings');
	$exportcsv = filter_var ( $genoptions['exportcsv'], FILTER_SANITIZE_STRING);
    echo "<div style='display: block; padding: 5px;'>";
	if("no" == $exportcsv or !$exportcsv){
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"no\" checked=\"checked\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"yes\">Yes</label>";
	}else{
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"no\">No (default)</label><br>";
		echo "<label><input type=\"radio\" name=\"sdpvs_general_settings[exportcsv]\" value=\"yes\" checked=\"checked\">Yes</label>";
	}
	echo "<p>This will only work if your admin directory is still called \"wp-admin\", it will not work if you have re-named it. The CSV output will be comma separated! Some security plugins block the ability for you to download files like this so please bear that in mind if this does not work for you.</p>";
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
	if (isset($input['author_number'])) {
		$new_input['author_number'] = absint($input['author_number']);
	}
	return $new_input;
}

function sdpvs_sanitize_general($input) {
	$new_input = array();
	if (isset($input['startweekon'])) {
		$new_input['startweekon'] = filter_var ( $input['startweekon'], FILTER_SANITIZE_STRING);
	}
	if (isset($input['rainbow'])) {
		$new_input['rainbow'] = filter_var ( $input['rainbow'], FILTER_SANITIZE_STRING);
	}
	return $new_input;
}

?>