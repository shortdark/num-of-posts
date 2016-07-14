<?php
/**
 * @package post-volume-stats
 * @version 2.09
 */
/*
 Plugin Name: Post Volume Stats
 Plugin URI: https://github.com/shortdark/num-of-posts
 Description: Displays the post stats in a custom page in the admin area with graphical representations.
 Author: Neil Ludlow
 Version: 2.09
 Author URI: http://www.shortdark.net/
 */

defined('ABSPATH') or die('No script kiddies please!');

if (!defined('WPINC')) {
	die ;
}

// define( 'WP_DEBUG', true );

/*
 * NUMBER OF POSTS PER CATEGORY ARRAY
 */
function sdpvs_assemble_year_data_in_array() {
	// get the currrent year
	$currentyear = date('Y');
	$y = 0;
	// get the number of posts over the past 15 years
	for ($i = 0; $i <= 14; $i++) {
		$searchyear = $currentyear - $i;
		$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('year' => $searchyear, ), );
		$the_query = new WP_Query($args);
		$year_array[$y]['year'] = $searchyear;
		$year_array[$y]['volume'] = $the_query -> found_posts;
		$y++;
	}

	return $year_array;
}

/**
 * DISPLAY YEAR DATA IN A GRAPH
 */
function sdpvs_draw_year_svg() {
	$years_total = 0;
	$number_of_years = 0;
	$highest_val = 0;
	$graphwidth = 200;
	$graphheight = 200;
	$graph_color = "blue";

	$year_svg = "<h2>Year Graph</h2>";

	$year_array = sdpvs_assemble_year_data_in_array();

	$y = 14;
	while ($year_array[$y]['year']) {
		if ((0 < $year_array[$y]['volume'] or 0 == $y) and !$first_year) {
			$first_year = $y;
		}
		if (0 < $year_array[$y]['volume'] and $highest_val < $year_array[$y]['volume']) {
			$highest_val = $year_array[$y]['volume'];
		}
		$y--;
	}
	$years_total = $first_year + 1;

	$bar_width = intval($graphwidth / $years_total);
	if (17 > $bar_width) {
		$text_indent = 0;
	} elseif (26 > $bar_width) {
		$text_indent = 2;
	} else {
		$text_indent = ($bar_width / 2) - 2;
	}

	$year_svg .= "<p>Posts over the past $years_total years, including posts that are not public.</p>";
	$year_svg .= "<svg width=\"" . $graphwidth . "px\" height=\"" . $graphheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\">\n";

	for ($i = 0; $i <= $first_year; $i++) {
		if (0 < $year_array[$i]['volume']) {
			$x_start = $graphwidth - ($i * $bar_width);
			$text_x = $x_start - $bar_width + $text_indent;
			$text_y = $graphheight - 2;
			$year_text = substr($year_array[$i]['year'], 2, 2);
			$bar_height = intval($graphheight * ($year_array[$i]['volume'] / $highest_val));
			$year_svg .= "\t<a xlink:title=\"{$year_array[$i]['year']}, {$year_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $graphheight v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$graph_color\" class=\"sdpvs_bar\"></path></a>";
			// $year_svg .= "\t<text x=\"$text_x\" y=\"$text_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"black\">$year_text</text>\n";
		}

	}

	$year_svg .= "</svg>\n";
	return $year_svg;
}

/*
 * NUMBER OF POSTS PER YEAR TEXT
 */
function sdpvs_number_of_posts_per_year() {

	$posts_per_year = "<h2>Post Volumes per Year</h2>";
	$posts_per_year .= "<p>Goes back up to 15 years.</p>";

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
function sdpvs_post_category_volumes() {
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
 * NUMBER OF POSTS PER TAG TEXT
 */
function sdpvs_post_tag_volumes() {
	$posts_per_tag = "<h2>Post Volumes per Tag!</h2>";
	// get all categories, ordered by name ascending
	$tagargs = array('orderby' => 'name', 'order' => 'ASC');
	$taglist = get_tags($tagargs);
	// return each one with the category admin URL
	foreach ($taglist as $tag) {
		$posts_per_tag .= "<a href='" . admin_url('edit.php?tag=' . $tag -> slug) . "'>$tag->name</a>: $tag->count posts<br>\n";
	}
	return $posts_per_tag;
}

class post_volume_stats_pie {
	private $number_of_categories = 0;
	private $number_of_tags = 0;
	private $newx;
	private $newy;

	/*
	 * COUNT NUMBER OF POSTS PER CATEGORY IN TOTAL, some posts might have multiple cats
	 */
	function sdpvs_count_number_of_posts_category() {
		$catlist = get_categories();

		// return each one with the category URL
		foreach ($catlist as $category) {
			$count_category_posts += $category -> category_count;
			$this -> number_of_categories++;
		}
		return $count_category_posts;
	}

	/*
	 * NUMBER OF POSTS PER CATEGORY ARRAY
	 */
	function sdpvs_assemble_category_data_in_array() {
		// get all categories, ordered by name ascending
		$catargs = array('orderby' => 'name', 'order' => 'ASC');
		$catlist = get_categories($catargs);

		$total_cat_volume = $this -> sdpvs_count_number_of_posts_category();

		$c = 0;
		// return each one with the category URL
		foreach ($catlist as $category) {
			$category_array[$c]['id'] = $category -> term_id;
			$category_array[$c]['name'] = $category -> name;
			$category_array[$c]['slug'] = $category -> slug;
			$category_array[$c]['volume'] = $category -> category_count;
			$category_array[$c]['angle'] = ($category -> category_count / $total_cat_volume) * 360;
			$c++;
		}
		return $category_array;
	}

	/*
	 * COUNT NUMBER OF POSTS PER TAG IN TOTAL, some posts might have multiple tags
	 */
	function sdpvs_count_number_of_posts_tag() {
		$taglist = get_tags();

		// return each one with the category URL
		foreach ($taglist as $tag) {
			$count_tag_posts += $tag -> count;
			$this -> number_of_tags++;
		}
		return $count_tag_posts;
	}

	/*
	 * NUMBER OF POSTS PER TAG ARRAY
	 */
	function sdpvs_assemble_tag_data_in_array() {
		// get all categories, ordered by name ascending
		$tagargs = array('orderby' => 'name', 'order' => 'ASC');
		$taglist = get_tags($tagargs);

		$total_tag_volume = $this -> sdpvs_count_number_of_posts_tag();

		$c = 0;
		// return each one with the category URL
		foreach ($taglist as $tag) {
			$tag_array[$c]['id'] = $tag -> term_id;
			$tag_array[$c]['name'] = $tag -> name;
			$tag_array[$c]['slug'] = $tag -> slug;
			$tag_array[$c]['volume'] = $tag -> count;
			$tag_array[$c]['angle'] = ($tag -> count / $total_tag_volume) * 360;
			$c++;
		}
		return $tag_array;
	}

	/**
	 * DISPLAY CATEGORY DATA IN A PIE CHART
	 */
	function sdpvs_draw_pie_svg($type = "") {
		$testangle_orig = 0;
		$radius = 100;
		$prev_angle = 0;
		$remaining = 0;
		$this -> newx = 0;
		$this -> newy = 0;

		if ("category" == $type) {
			$total_volume = $this -> sdpvs_count_number_of_posts_category();
			$pie_array = $this -> sdpvs_assemble_category_data_in_array();
			$number_of_containers = $this->number_of_categories;
			$pie_svg = "<h2>Category Pie Chart</h2>";
			$pie_svg .= "<p>$number_of_containers categories. Total volume of active posts (posts with multiple categories are counted multiple times) = $total_volume</p>";
			$link_part = "category_name";
		} elseif ("tag" == $type) {
			$total_volume = $this -> sdpvs_count_number_of_posts_tag();
			$pie_array = $this -> sdpvs_assemble_tag_data_in_array();
			$number_of_containers = $this->number_of_tags;
			$pie_svg = "<h2>Tag Pie Chart</h2>";
			$pie_svg .= "<p>$number_of_containers tags. Total volume of active posts (posts with multiple tags are counted multiple times) = $total_volume</p>";
			$link_part = "tag";
		}

		$pie_svg .= "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\" class=\"sdpvs_pie\"><circle cx=\"$radius\" cy=\"$radius\" r=\"$radius\" stroke=\"black\" stroke-width=\"1\" />\n";

		$c = 0;
		while ($pie_array[$c]['name']) {
			if (0 < $pie_array[$c]['volume']) {
				if (0 < $pie_array[$c]['angle']) {
					$prev_angle = $testangle_orig;
					$testangle_orig += $pie_array[$c]['angle'];
					$quadrant = $this -> sdpvs_specify_starting_quadrant($testangle_orig);
					$testangle = $this -> sdpvs_specify_testangle($testangle_orig);
					$large = $this -> sdpvs_is_it_a_large_angle($testangle_orig, $prev_angle);
					$startingline = $this -> sdpvs_draw_starting_line($prev_angle, $this -> newx, $this -> newy);
					$this -> sdpvs_get_absolute_coordinates_from_angle($quadrant, $radius, $testangle);
					
					if(360 > $number_of_containers){
						$opacity = $pie_array[$c]['angle'] / 180;
					}elseif(1000 > $number_of_containers){
						$opacity = $pie_array[$c]['angle'] / 30;
					}else{
						$opacity = $pie_array[$c]['angle'] / 15;
					}

					
					if (1 < $opacity) {
						$opacity = 1;
					}
					$opacity = sprintf("%.1f", $opacity);
					$color = "rgba(255,0,0,$opacity)";

					$display_angle_as = sprintf("%.1f", $pie_array[$c]['angle']);

					$link = admin_url("edit.php?$link_part=" . $pie_array[$c]['slug']);

					if (360 == $pie_array[$c]['angle']) {
						// If only one category exists make sure there is a green solid circle
						$pie_svg .= "<a href='$link' xlink:title=\"{$pie_array[$c]['name']}, {$pie_array[$c]['volume']} posts\"><circle class=\"sdpvs_segment\" cx='100' cy='100' r='100' fill='red'/></a>\n";
					} else {
						$pie_svg .= "  <a href='$link' xlink:title=\"{$pie_array[$c]['name']}, {$pie_array[$c]['volume']} posts\"><path id=\"{$pie_array[$c]['name']}\" class=\"sdpvs_segment\" d=\"M$radius,$radius $startingline A$radius,$radius 0 $large,1 $this->newx,$this->newy z\" fill=\"$color\" stroke=\"black\" stroke-width=\"1\"  /></a>\n";
					}
				}
			}
			$c++;
		}
		$pie_svg .= "</svg>\n";
		return $pie_svg;
	}

	/**
	 * WHICH QUADRANT OF THE CIRCLE ARE WE IN ?
	 */
	function sdpvs_specify_starting_quadrant($testangle_orig) {
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
	function sdpvs_specify_testangle($testangle_orig) {
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
	function sdpvs_is_it_a_large_angle($testangle_orig, $prev_angle) {
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
	function sdpvs_draw_starting_line($prev_angle, $newx, $newy) {
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
	function sdpvs_get_absolute_coordinates_from_angle($quadrant, $radius, $testangle) {
		if (1 == $quadrant) {
			$this -> newx = $radius + ($radius * sin(deg2rad($testangle)));
			$this -> newy = $radius - ($radius * cos(deg2rad($testangle)));
		} elseif (2 == $quadrant) {
			$this -> newx = $radius + ($radius * cos(deg2rad($testangle)));
			$this -> newy = $radius + ($radius * sin(deg2rad($testangle)));
		} elseif (3 == $quadrant) {
			$this -> newx = $radius - ($radius * sin(deg2rad($testangle)));
			$this -> newy = $radius + ($radius * cos(deg2rad($testangle)));
		} elseif (4 == $quadrant) {
			$this -> newx = $radius - ($radius * cos(deg2rad($testangle)));
			$this -> newy = $radius - ($radius * sin(deg2rad($testangle)));
		}
		return;
	}

}

// Some CSS to format the plugin page
function sdpvs_post_volume_stats_css() {
	echo "
	<style type='text/css'>
	
#sdpvs_leftcol, #sdpvs_rightcol1, #sdpvs_rightcol2 {
	width: 250px; 
	display: inline-block; 
	vertical-align: top;
}

#sdpvs_leftcol p , #sdpvs_rightcol1 p, #sdpvs_rightcol2 p  {
	text-align: left;
}

@media (max-width: 520px) {
  #sdpvs_leftcol , #sdpvs_rightcol1 {
    width: 100%;
    width: 100vw;
    display: block;
  }
}
.sdpvs_pie{ 
	width: 200px; 
	height: 200px; 
} 
a .sdpvs_segment:hover, a .sdpvs_bar:hover{ 
	stroke:white; 
	fill: green; 
}

	</style>
	";
}

add_action('admin_head', 'sdpvs_post_volume_stats_css');

// This appends comments to the content of each "single post".
function sdpvs_post_volume_stats_assembled() {

	$sdpvs_pie = new post_volume_stats_pie();

	$content = "<h1 class='sdpvs'>Post Volume Stats</h1>\n";
	$content .= "<p class='sdpvs'>These are your post stats.</p>\n";

	$content .= "<div id='sdpvs_leftcol'>";
	// graph
	$content .= sdpvs_draw_year_svg();
	// posts per year
	$content .= sdpvs_number_of_posts_per_year();

	$content .= "</div>";

	$content .= "<div id='sdpvs_rightcol1'>";
	// posts per category pie chart
	$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("category");

	// posts per category
	$content .= sdpvs_post_category_volumes();

	$content .= "</div>";

	$content .= "<div id='sdpvs_rightcol2'>";
	// posts per tag pie chart
	$content .= $sdpvs_pie -> sdpvs_draw_pie_svg("tag");

	// posts per tag
	$content .= sdpvs_post_tag_volumes();

	$content .= "</div>";

	echo $content;
}

// Register a custom menu page in the admin.
function sdpvs_register_custom_page_in_menu() {
	add_menu_page(__('Post Volume Stats', 'textdomain'), 'Post Volume Stats', 'manage_options', __DIR__, 'sdpvs_post_volume_stats_assembled', plugins_url('images/post-volume-stats-16x16.png', __FILE__), 1000);
}

add_action('admin_menu', 'sdpvs_register_custom_page_in_menu');

/****************
 ** TODO
 ****************/

/*
 * 1) Better use of classes to neaten up and minimize duplication of code
 * 2) Present the data better
 * 3) Split the classes up into different files for clarity
 * 4) Make pie segments clickable
 * 5) I18n, write to the page using translatable strings in a __() function
 * 6) Plugin info, figure out how to improve the look and add images
 * 7) Separate CSS into a CSS file, if needed (?)
 *
 */
?>
