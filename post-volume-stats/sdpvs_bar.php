<?php

class sdpvs_bar_chart {

	private $total_volume_of_posts = 0;

	private $first_val;

	private $highest_val;

	private $order;

	/*
	 * NUMBER OF POSTS PER YEAR ARRAY
	 */
	function sdpvs_assemble_year_data_in_array() {
		$this -> highest_val = 0;
		$this -> first_val = 0;
		$this -> order = "desc";
		// get the currrent year
		$currentyear = date('Y');
		$y = 0;
		// get the number of posts over the past 31 years
		for ($i = 0; $i <= 30; $i++) {
			$searchyear = $currentyear - $i;
			$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('year' => $searchyear, ), );
			$the_query = new WP_Query($args);
			$year_array[$y]['title'] = $searchyear;
			$year_array[$y]['volume'] = $the_query -> found_posts;
			$this -> total_volume_of_posts += $the_query -> found_posts;
			if (0 < $the_query -> found_posts and $this -> highest_val < $the_query -> found_posts) {
				// find the bar with the highest value
				$this -> highest_val = $the_query -> found_posts;
			}
			if (0 < $the_query -> found_posts) {
				$this -> first_val = $i;
			}
			$y++;
		}

		return $year_array;
	}

	/*
	 * NUMBER OF POSTS PER DAY OF WEEK ARRAY
	 */
	function sdpvs_assemble_dayofweek_data_in_array() {
		$this -> first_val = 7;
		$this -> highest_val = 0;
		$this -> order = "asc";
		$y = 0;
		// get the number of posts per day of the week
		for ($i = 1; $i <= 7; $i++) {
			$searchyear = $currentyear - $i;
			$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('dayofweek' => $i));
			$the_query = new WP_Query($args);
			$dow_array[$y]['title'] = jddayofweek($i - 1, 2);
			$dow_array[$y]['volume'] = $the_query -> found_posts;
			if (0 < $the_query -> found_posts and $this -> highest_val < $the_query -> found_posts) {
				// find the bar with the highest value
				$this -> highest_val = $the_query -> found_posts;
			}
			$y++;
		}

		return $dow_array;
	}

	/*
	 * NUMBER OF POSTS PER HOUR ARRAY
	 */
	function sdpvs_assemble_hour_data_in_array() {
		$this -> first_val = 23;
		$this -> highest_val = 0;
		$this -> order = "asc";
		$y = 0;
		// get the number of posts per day of the week
		for ($i = 0; $i <= 23; $i++) {
			$searchyear = $currentyear - $i;
			$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('hour' => $i));
			$the_query = new WP_Query($args);
			$j = sprintf("%02s", $i);
			$hour_array[$y]['title'] = "$j:00-$j:59";
			$hour_array[$y]['volume'] = $the_query -> found_posts;
			if (0 < $the_query -> found_posts and $this -> highest_val < $the_query -> found_posts) {
				// find the bar with the highest value
				$this -> highest_val = $the_query -> found_posts;
			}
			$y++;
		}

		return $hour_array;
	}

	/**
	 * DISPLAY DATA IN A BAR CHART
	 */
	function sdpvs_draw_bar_chart_svg($which = "") {
		$years_total = 0;
		$number_of_years = 0;
		$highest_val = 0;
		$graphwidth = 200;
		$graphheight = 200;
		$graph_color = "blue";

		if ("year" == $which) {
			$chart_array = $this -> sdpvs_assemble_year_data_in_array();
			$bars_total = $this -> first_val + 1;
			$bar_svg = "<h2>Year Bar Chart</h2>";
			$bar_svg .= "<p>$this->total_volume_of_posts posts over the past $bars_total years, including posts that are not public.</p>";
		} elseif ("dayofweek" == $which) {
			$chart_array = $this -> sdpvs_assemble_dayofweek_data_in_array();
			$bars_total = 7;
			$bar_svg = "<h2>Days of the Week</h2>";
			$bar_svg .= "<p>Which day of the week posts were made on.</p>";
		} elseif ("hour" == $which) {
			$chart_array = $this -> sdpvs_assemble_hour_data_in_array();
			$bars_total = 24;
			$bar_svg = "<h2>Hours</h2>";
			$bar_svg .= "<p>Which hour of the day posts were made on.</p>";
		}

		$bar_width = intval($graphwidth / $bars_total);
		if (17 > $bar_width) {
			$text_indent = 0;
		} elseif (26 > $bar_width) {
			$text_indent = 2;
		} else {
			$text_indent = ($bar_width / 2) - 2;
		}

		$bar_svg .= "<svg width=\"" . $graphwidth . "px\" height=\"" . $graphheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\">\n";
		for ($i = 0; $i <= $this -> first_val; $i++) {
			if (0 < $chart_array[$i]['volume']) {
				if ("desc" == $this -> order) {
					$x_start = $graphwidth - ($i * $bar_width);
				} elseif ("asc" == $this -> order) {
					$x_start = $bar_width + ($i * $bar_width);
				}
				$bar_height = intval($graphheight * ($chart_array[$i]['volume'] / $this -> highest_val));
				$bar_svg .= "\t<a xlink:title=\"{$chart_array[$i]['title']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $graphheight v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$graph_color\" class=\"sdpvs_bar\"></path></a>";
			}

		}

		$bar_svg .= "</svg>\n";
		return $bar_svg;
	}

}
?>
