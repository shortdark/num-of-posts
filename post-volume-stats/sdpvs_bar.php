<?php

class sdpvs_bar_chart {

	private $total_volume_of_posts = 0;
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
			$this -> total_volume_of_posts += $the_query -> found_posts;
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

		$year_svg = "<h2>Year Bar Chart</h2>";

		$year_array = $this -> sdpvs_assemble_year_data_in_array();

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

		$year_svg .= "<p>$this->total_volume_of_posts posts over the past $years_total years, including posts that are not public.</p>";
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

}
?>
