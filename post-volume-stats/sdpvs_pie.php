<?php

class sdpvs_post_volume_stats_pie {
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

?>
