<?php
/**
 * @package num-of-posts
 * @version 2.02
 */
/*
 Plugin Name: Number of Posts
 Plugin URI: http://www.shortdark.net/
 Description: Displays the number of posts per year and the volume of posts per category in a custom page in the admin area.
 Author: Neil Ludlow
 Version: 2.02
 Author URI: http://www.shortdark.net/
 */

defined('ABSPATH') or die('No script kiddies please!');

function number_of_posts_per_year() {

	$posts_per_year = "<h2>Post Volume per Year</h2>";

	// get the currrent year
	$currentyear = date('Y');

	// get the number of posts over the past 15 years
	for ($i = 0; $i <= 14; $i++) {
		$searchyear = $currentyear - $i;
		$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('year' => $searchyear, ), );
		$the_query = new WP_Query($args);

		// if there are any posts return the volume for that year...
		if (0 < $the_query -> found_posts) {
			$posts_per_year .= "Number of posts in $searchyear: $the_query->found_posts<br>\n";
		}
	}
	return $posts_per_year;
}

function post_category_volumes() {

	$posts_per_category = "<h2>Post Volume per Category</h2>";

	// get all categories, ordered by name ascending
	$website_url = get_site_url();
	$catargs = array('orderby' => 'name', );
	$catlist = get_categories($catargs);

	// return each one with the category URL (unless it's a child of a parent category)
	foreach ($catlist as $category) {

		if (0 == $category -> category_parent) {
			$cat_link = "$website_url/category/$category->slug/";
		} else {
			$cat_link = "";
		}

		$posts_per_category .= "<a href='" . admin_url('edit.php?category_name='.$category->slug) . "'>$category->cat_name</a>: $category->category_count posts<br>\n";
	}
	return $posts_per_category;
}

// This appends comments to the content of each "single post".
function grab_post_volume() {

	$content = "<h1>Post Volume</h1>";
	// first comment = posts per year
	$content .= number_of_posts_per_year();

	// second comment is posts per category
	$content .= post_category_volumes();

	echo $content;
}

// Register a custom menu page in the admin.
function wpdocs_register_my_custom_menu_page() {
	add_menu_page(__('Post Volumes', 'textdomain'), 'Post Volumes', 'manage_options', 'num_posts_display', 'grab_post_volume', '', '');
}

add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');

/****************
 ** TODO
 ****************/

/*
 * 1) Package plugin as a directory and include a menu image
 * 2) Make some graphs
 *
 */
?>
