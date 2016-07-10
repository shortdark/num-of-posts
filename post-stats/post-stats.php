<?php
/**
 * @package post-stats
 * @version 2.04
 */
/*
 Plugin Name: Post Stats
 Plugin URI: http://www.shortdark.net/
 Description: Displays the number of posts per year and the volume of posts per category in a custom page in the admin area.
 Author: Neil Ludlow
 Version: 2.04
 Author URI: http://www.shortdark.net/
 */

defined('ABSPATH') or die('No script kiddies please!');

/*
 * NUMBER OF POSTS PER YEAR TEXT
 */
function number_of_posts_per_year() {

	$posts_per_year = "<h2>Post Volumes per Year</h2>";

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

/*
 * NUMBER OF POSTS PER CATEGORY TEXT
 */
function post_category_volumes() {
	$posts_per_category = "<h2>Post Volumes per Category!</h2>";
	// get all categories, ordered by name ascending
	$catargs = array('orderby' => 'name', 'order' => 'ASC');
	$catlist = get_categories($catargs);
	// return each one with the category admin URL
	foreach ($catlist as $category) {
		$posts_per_category .= "<a href='" . admin_url('edit.php?category_name=' . $category -> slug) . "'>$category->cat_name</a>: $category->category_count posts<br>\n";
	}
	return $posts_per_category;
}

/*
 * COUNT NUMBER OF POSTS PER CATEGORY IN TOTAL, some posts might have multiple cats
 */
function count_number_of_posts_category() {
	$catlist = get_categories();

	// return each one with the category URL
	foreach ($catlist as $category) {
		$count_category_posts += $category -> category_count;
	}
	return $count_category_posts;
}

/*
 * NUMBER OF POSTS PER CATEGORY ARRAY
 */
function assemble_category_data_in_array() {
	// get all categories, ordered by name ascending
	$catargs = array('orderby' => 'name', 'order' => 'ASC');
	$catlist = get_categories($catargs);

	$total_volume = count_number_of_posts_category();

	$c = 0;
	// return each one with the category URL
	foreach ($catlist as $category) {
		$category_array[$c]['catid'] = $category -> term_id;
		$category_array[$c]['catname'] = $category -> name;
		$category_array[$c]['volume'] = $category -> category_count;
		$category_array[$c]['angle'] = $category -> category_count / $total_volume * 360;
		$c++;
	}
	return $category_array;
}

/**
 * DISPLAY CATEGORY DATA IN A PIE CHART
 */
function draw_cat_pie_svg() {
	$testangle_orig = 0;
	$radius = 100;
	$prev_angle = 0;
	$remaining = 0;
	$newx = 0;
	$newy = 0;

	$css_bit = ".pie{ width: 200px; height: 200px; } a .land:hover{ stroke:white; fill: green; }";
	$total_volume = count_number_of_posts_category();
	$cat_pie_svg = "<h2>Category Pie chart</h2>";
	$cat_pie_svg .= "<p>Pie chart, total = $total_volume</p>";

	$cat_pie_svg .= "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\" class=\"pie\"><circle cx=\"$radius\" cy=\"$radius\" r=\"$radius\" stroke=\"black\" stroke-width=\"1\" />\n";
	$cat_pie_svg .= "<style type=\"text/css\">" . $css_bit . "</style>";

	$category_array = assemble_category_data_in_array();

	$c = 0;
	while ($category_array[$c]['catname']) {
		if (0 < $category_array[$c]['volume']) {
			if (0 < $category_array[$c]['angle']) {
				// $testangle = $category_array[$c]['angle'];
				$prev_angle = $testangle_orig;
				$testangle_orig += $category_array[$c]['angle'];
				$quadrant = specify_starting_quadrant($testangle_orig);
				$testangle = specify_testangle($testangle_orig);
				$large = is_it_a_large_angle($testangle_orig, $prev_angle);
				$startingline = draw_starting_line($prev_angle, $newx, $newy);
				$newx = get_absolute_x_coordinates_from_angle($quadrant, $radius, $testangle);
				$newy = get_absolute_y_coordinates_from_angle($quadrant, $radius, $testangle);

				$opacity = $category_array[$c]['angle'] / 180;
				$opacity = sprintf("%.1f", $opacity);
				$color = "rgba(255,0,0,$opacity)";

				$display_angle_as = sprintf("%.1f", $category_array[$c]['angle']);

				$cat_pie_svg .= "  <a $link xlink:title=\"{$category_array[$c]['catname']}, {$category_array[$c]['volume']} posts, $display_angle_as degrees\"><path id=\"{$category_array[$c]['catname']}\" class=\"land\" d=\"M$radius,$radius $startingline A$radius,$radius 0 $large,1 $newx,$newy z\" fill=\"$color\" stroke=\"black\" stroke-width=\"1\"  /></a>\n";
			}
		}
		$c++;
	}
	$cat_pie_svg .= "</svg>\n";
	return $cat_pie_svg;
}

/**
 * WHICH QUADRANT OF THE CIRCLE ARE WE IN ?
 */
function specify_starting_quadrant($testangle_orig) {
	if (270 < $testangle_orig) {
		$quadrant = 4;
	} elseif (180 < $testangle_orig) {
		$quadrant = 3;
	} elseif (90 < $testangle_orig) {
		$quadrant = 2;
	} else {
		$quadrant = 1;
	}
	return $quadrant;
}

/**
 * MAKE AN ACUTE ANGLE
 */
function specify_testangle($testangle_orig) {
	if (270 < $testangle_orig) {
		$testangle = $testangle_orig - 270;
	} elseif (180 < $testangle_orig) {
		$testangle = $testangle_orig - 180;
	} elseif (90 < $testangle_orig) {
		$testangle = $testangle_orig - 90;
	} else {
		$testangle = $testangle_orig;
	}
	return $testangle;
}

/**
 * IS THE ANGLE MORE THAN 180 DEGREES ?
 */
function is_it_a_large_angle($testangle_orig, $prev_angle) {
	if (180 < $testangle_orig - $prev_angle) {
		$large = 1;
	} else {
		$large = 0;
	}
	return $large;
}

/**
 * THIS GRABS THE INFO FROM THE PREVIOUS POINT AND STARTS OFF THE PIE SEGMENT
 */
function draw_starting_line($prev_angle, $newx, $newy) {
	if (0 < $prev_angle) {
		$startingline = "L$newx,$newy";
	} else {
		$startingline = "V0";
	}
	return $startingline;
}

/**
 * GET NEW X CO-ORDINATES
 */
function get_absolute_x_coordinates_from_angle($quadrant, $radius, $testangle) {
	if (1 == $quadrant) {
		$newx = $radius + ($radius * sin(deg2rad($testangle)));
	} elseif (2 == $quadrant) {
		$newx = $radius + ($radius * cos(deg2rad($testangle)));
	} elseif (3 == $quadrant) {
		$newx = $radius - ($radius * sin(deg2rad($testangle)));
	} elseif (4 == $quadrant) {
		$newx = $radius - ($radius * cos(deg2rad($testangle)));
	}
	return $newx;
}

/**
 * GET NEW Y CO-ORDINATES
 */
function get_absolute_y_coordinates_from_angle($quadrant, $radius, $testangle) {
	if (1 == $quadrant) {
		$newy = $radius - ($radius * cos(deg2rad($testangle)));
	} elseif (2 == $quadrant) {
		$newy = $radius + ($radius * sin(deg2rad($testangle)));
	} elseif (3 == $quadrant) {
		$newy = $radius + ($radius * cos(deg2rad($testangle)));
	} elseif (4 == $quadrant) {
		$newy = $radius - ($radius * sin(deg2rad($testangle)));
	}
	return $newy;
}

// This appends comments to the content of each "single post".
function post_stats_assembled() {

	$content = "<h1>Post Stats</h1>";
	// posts per year
	$content .= number_of_posts_per_year();

	// posts per category
	$content .= post_category_volumes();
	
	// posts per category pie chart
	$content .= draw_cat_pie_svg();

	echo $content;
}

// Register a custom menu page in the admin.
function wpdocs_register_my_custom_menu_page() {
	add_menu_page(__('Post Stats', 'textdomain'), 'Post Stats', 'manage_options', 'post_stats', 'post_stats_assembled', plugins_url('post-stats/images/post-stats-16x16.png'), '');
}

add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');

/****************
 ** TODO
 ****************/

/*
 * 1) Make a posts per year graph
 * 2) Present the data better
 * 3) Split the methods up into different files
 *
 */
?>
