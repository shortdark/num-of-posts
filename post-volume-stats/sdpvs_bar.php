<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsBarChart extends sdpvsArrays {

	protected $total_volume_of_posts;

	protected $first_val;

	protected $highest_val;

	/**
	 * DISPLAY DATA IN A BAR CHART
	 */
	public function sdpvs_draw_bar_chart_svg($which = "", $searchyear = "") {
		$searchyear = absint($searchyear);
		$years_total = 0;
		$number_of_years = 0;
		$highest_val = 0;
		$graphwidth = 200;
		$graphheight = 200;
		$graph_color = "blue";
		$highlight_color = "red";

		if ("year" == $which) {
			parent::sdpvs_number_of_posts_per_year();
			$chart_array = $this -> year_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = $this -> first_val + 1;
			$order = "desc";
			echo __("<h2>Years</h2>", 'post-volume-stats');
		} elseif ("dayofweek" == $which) {
			parent::sdpvs_number_of_posts_per_dayofweek($searchyear);
			$chart_array = $this -> dow_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 7;
			$order = "asc";
			echo __("<h2>Days of the Week</h2>", 'post-volume-stats');
		} elseif ("hour" == $which) {
			parent::sdpvs_number_of_posts_per_hour($searchyear);
			$chart_array = $this -> hour_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 24;
			$order = "asc";
			echo __("<h2>Hours</h2>", 'post-volume-stats');
		} elseif ("month" == $which) {
			parent::sdpvs_number_of_posts_per_month($searchyear);
			$chart_array = $this -> month_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 12;
			$order = "asc";
			echo __("<h2>Months</h2>", 'post-volume-stats');
		} elseif ("dayofmonth" == $which) {
			parent::sdpvs_number_of_posts_per_dayofmonth($searchyear);
			$chart_array = $this -> dom_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 31;
			$order = "asc";
			echo __("<h2>Days of the Month</h2>", 'post-volume-stats');
		}

		$bar_width = intval($graphwidth / $bars_total);
		if (17 > $bar_width) {
			$text_indent = 0;
		} elseif (26 > $bar_width) {
			$text_indent = 2;
		} else {
			$text_indent = ($bar_width / 2) - 2;
		}

		echo "<svg width=\"" . $graphwidth . "px\" height=\"" . $graphheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\">\n";
		for ($i = 0; $i <= $this -> first_val; $i++) {
			if (0 < $chart_array[$i]['volume']) {
				if ("desc" == $order) {
					$x_start = $graphwidth - ($i * $bar_width);
				} elseif ("asc" == $order) {
					$x_start = $bar_width + ($i * $bar_width);
				}
				if ($chart_array[$i]['title'] == $searchyear and "year" == $which) {
					$color = $highlight_color;
					$set_explicit_color = "background-color: $color;";
				} else {
					$color = $graph_color;
					$set_explicit_color = "";
				}
				$bar_height = intval($graphheight * ($chart_array[$i]['volume'] / $this -> highest_val));
				if ("year" == $which) {
					if ($chart_array[$i]['title'] == $searchyear) {
						$year_form_value = "";
					} else {
						$year_form_value = $chart_array[$i]['title'];
					}
					$form_y_offset = $graphheight - $bar_height;
					$form_x_offset = $x_start - $bar_width;
					echo "<path fill-opacity=\"0.5\" d=\"M$x_start $graphheight v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"white\"></path>";
					echo "<foreignObject x=\"$form_x_offset\" y=\"$form_y_offset\" width=\"$bar_width\" height=\"$bar_height\">";
					echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
					settings_fields('sdpvs_year_option');
					echo " <input type=\"hidden\" name=\"sdpvs_year_option[year_number]\" id=\"year-number\" value=\"$year_form_value\">
					<input type=\"submit\" style=\"height: " . $bar_height . "px; width: " . $bar_width . "px; $set_explicit_color\" class=\"sdpvs_year_bar\">
          			</form>
  					</foreignObject>";
				} else {
					echo "<a xlink:title=\"{$chart_array[$i]['title']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $graphheight v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
				}

			}

		}

		echo "</svg>\n";
		if ("year" == $which) {
			if(0 < $searchyear){
				echo __("<p>Click bar to change to that year. Click selected year (red) to view all stats.</p>", 'post-volume-stats');
			}else{
				echo __("<p>Click bar to change to that year.</p>", 'post-volume-stats');
			}
		}
		// $bar_svg .= "<form class='sdpvs_form' action='' method='POST'><input type='hidden' name='whichdata' value='$which'><input type='submit' class='button-primary sdpvs_load_content' value='Show Data'></form></p>";

		return;
	}

}
?>
