<?php

class sdpvs_bar_chart extends sdpvs_arrays {

	private $total_volume_of_posts;

	private $first_val;

	private $highest_val;

	/*
	 * FIND HIGHEST, FIRST AND TOTAL VOLUME VALUES
	 */
	function find_highest_first_and_total($testarray) {
		$this -> highest_val = 0;
		$this -> first_val = 0;
		$this -> total_volume_of_posts = 0;
		$i = 0;
		while ($testarray[$i]['title']) {
			$this -> total_volume_of_posts += $testarray[$i]['volume'];
			if (0 < $testarray[$i]['volume'] and $this -> highest_val < $testarray[$i]['volume']) {
				$this -> highest_val = $testarray[$i]['volume'];
			}
			if (0 < $testarray[$i]['volume']) {
				$this -> first_val = $i;
			}
			$i++;
		}

		return;
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
			parent::sdpvs_number_of_posts_per_year();
			$chart_array = $this -> year_array;
			$this -> find_highest_first_and_total($chart_array);
			$bars_total = $this -> first_val + 1;
			$order = "desc";
			$bar_svg = "<h2>Year Bar Chart</h2>";
			$bar_svg .= "<p>$this->total_volume_of_posts posts over the past $bars_total years.</p>";
		} elseif ("dayofweek" == $which) {
			parent::sdpvs_number_of_posts_per_dayofweek();
			$chart_array = $this -> dow_array;
			$this -> find_highest_first_and_total($chart_array);
			$bars_total = 7;
			$order = "asc";
			$bar_svg = "<h2>Days of the Week</h2>";
			$bar_svg .= "<p>Which day of the week the $this->total_volume_of_posts posts were made on.</p>";
		} elseif ("hour" == $which) {
			parent::sdpvs_number_of_posts_per_hour();
			$chart_array = $this -> hour_array;
			$this -> find_highest_first_and_total($chart_array);
			$bars_total = 24;
			$order = "asc";
			$bar_svg = "<h2>Hours</h2>";
			$bar_svg .= "<p>Which hour of the day the $this->total_volume_of_posts posts were made on.</p>";
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
				if ("desc" == $order) {
					$x_start = $graphwidth - ($i * $bar_width);
				} elseif ("asc" == $order) {
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
