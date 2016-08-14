<?php

class sdpvsPieChart extends sdpvsArrays {
	private $number_of_categories = 0;
	private $total_category_posts = 0;
	private $number_of_tags = 0;
	private $total_tag_posts = 0;
	private $newx;
	private $newy;

	/*
	 * COUNT NUMBER OF POSTS PER CATEGORY IN TOTAL, some posts might have multiple cats
	 */
	private function sdpvs_count_post_categories($year="") {
		$c = 0;
		while ($this -> category_array[$c]['id']) {
			if (0 < $this -> category_array[$c]['volume']) {
				$this -> number_of_categories++;
				$this -> total_category_posts += $this -> category_array[$c]['volume'];
			}
			$c++;
		}
		return;
	}

	/*
	 * ADD THE ANGLE TO THE EXISTING CATEGORY ARRAY
	 */
	private function sdpvs_add_to_category_array($year="") {
		parent::sdpvs_post_category_volumes($year);
		$this -> sdpvs_count_post_categories($year);
		$c = 0;
		while ($this -> category_array[$c]['id']) {
			if (0 < $this -> category_array[$c]['volume']) {
				$this -> category_array[$c]['angle'] = ($this -> category_array[$c]['volume'] / $this -> total_category_posts) * 360;
			}
			$c++;
		}
		return;
	}

	/*
	 * COUNT NUMBER OF POSTS PER TAG IN TOTAL, some posts might have multiple tags
	 */
	private function sdpvs_count_post_tags($year="") {
		$t = 0;
		while ($this -> tag_array[$t]['id']) {
			if (0 < $this -> tag_array[$t]['volume']) {
				$this -> number_of_tags++;
				$this -> total_tag_posts += $this -> tag_array[$t]['volume'];
			}
			$t++;
		}
		return;
	}

	/*
	 * ADD THE ANGLE TO THE EXISTING TAG ARRAY
	 */
	private function sdpvs_add_to_tag_array($year="") {
		parent::sdpvs_post_tag_volumes($year);

		$this -> sdpvs_count_post_tags($year);

		$t = 0;
		while ($this -> tag_array[$t]['id']) {
			if (0 < $this -> tag_array[$t]['volume']) {
				$this -> tag_array[$t]['angle'] = ($this -> tag_array[$t]['volume'] / $this -> total_tag_posts) * 360;
			}
			$t++;
		}
		return;
	}

	/**
	 * DISPLAY CATEGORY DATA IN A PIE CHART
	 */
	public function sdpvs_draw_pie_svg($type = "", $year="") {
		$testangle_orig = 0;
		$radius = 100;
		$prev_angle = 0;
		$remaining = 0;
		$this -> newx = 0;
		$this -> newy = 0;

		if ("category" == $type) {
			$this -> sdpvs_add_to_category_array($year);
			$pie_array = $this -> category_array;
			$total_volume = $this -> total_category_posts;
			$number_of_containers = $this -> number_of_categories;
			$pie_svg = __("<h2>Categories</h2>", 'post-volume-stats');
			// $pie_svg .= "<p>$number_of_containers categories. Total volume of active posts (posts with multiple categories are counted multiple times) = $total_volume</p>";
			$link_part = "category_name";
		} elseif ("tag" == $type) {
			$this -> sdpvs_add_to_tag_array($year);
			$total_volume = $this -> total_tag_posts;
			$pie_array = $this -> tag_array;
			$number_of_containers = $this -> number_of_tags;
			$pie_svg = __("<h2>Tags</h2>", 'post-volume-stats');;
			// $pie_svg .= "<p>$number_of_containers tags. Total volume of active posts (posts with multiple tags are counted multiple times) = $total_volume</p>";
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

					if (90 > $number_of_containers) {
						$opacity = $pie_array[$c]['angle'] / 180;
					} elseif (1000 > $number_of_containers) {
						$opacity = $pie_array[$c]['angle'] / 30;
					} else {
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
		$pie_svg .= "<form class='sdpvs_form' action='' method='POST'><input type='hidden' name='whichdata' value='$type'><input type='submit' class='button-primary sdpvs_load_content' value='Show Data'></form></p>";
		
		return $pie_svg;
	}

	/**
	 * WHICH QUADRANT OF THE CIRCLE ARE WE IN ?
	 */
	private function sdpvs_specify_starting_quadrant($testangle_orig) {
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
	private function sdpvs_specify_testangle($testangle_orig) {
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
	private function sdpvs_is_it_a_large_angle($testangle_orig, $prev_angle) {
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
	private function sdpvs_draw_starting_line($prev_angle, $newx, $newy) {
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
	private function sdpvs_get_absolute_coordinates_from_angle($quadrant, $radius, $testangle) {
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
