<?php
/**
 * @package num-of-posts
 * @version 1.0
 */
/*
 Plugin Name: Number of Posts
 Plugin URI: http://www.shortdark.net/
 Description: Displays the number of posts per year and the posts per category in a comment after the main post content.
 Author: Neil Ludlow
 Version: 1.01
 Author URI: http://www.shortdark.net/
 */

defined('ABSPATH') or die('No script kiddies please!');

function number_of_posts_per_year() {
	// Start comment...
	$posts_per_year = "\n\n<!-- START OF NUMBER OF POSTS PLUGIN\n\nYears...\n";

	// get the currrent year
	$currentyear = date('Y');

	// get the number of posts over the past 15 years
	for ($i = 0; $i <= 14; $i++) {
		$searchyear = $currentyear - $i;
		$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('year' => $searchyear, ), );
		$the_query = new WP_Query($args);

		// if there are any posts return the volume for that year...
		if (0 < $the_query -> found_posts) {
			$posts_per_year .= "Number of posts in $searchyear: $the_query->found_posts\n";
		}
	}
	// End comment...
	$posts_per_year .= "\n\n//-->\n\n";
	return $posts_per_year;
}

function post_category_volumes() {
	// Start comment...
	$posts_per_category = "\n\n<!-- Categories...\n";

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
		$posts_per_category .= "$category->cat_name: $category->category_count posts, $cat_link\n";
	}
	// End Comment...
	$posts_per_category .= "\n\nEND OF NUMBER OF POSTS PLUGIN //-->\n\n";
	return $posts_per_category;
}

// This appends comments to the content of each "single post".
function grab_post_volume($content) {
	// Only put on "single posts"...
	if (is_single()) {
		// Locate plugins location on the page - for testing
		// $content .= "\n\nPLUGIN IS HERE!!";

		// first comment = posts per year
		$content .= number_of_posts_per_year();

		// second comment is posts per category
		$content .= post_category_volumes();

	}
	return $content;
}

// Now we set that function up to execute when the "the_content" is called
add_action('the_content', 'grab_post_volume');

/****************
 ** TODO
 ****************/

/*
 * 1) Display something on the page
 *
 */
?>
