<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsBarChart extends sdpvsArrays {

	protected $total_volume_of_posts;

	protected $first_val;

	protected $highest_val;

	/**
	 * DISPLAY DATA IN A BAR CHART
	 */
	public function sdpvs_draw_bar_chart_svg($which = "", $searchyear = "", $subpage = "", $public = "") {
		$searchyear = absint($searchyear);
		$years_total = 0;
		$number_of_years = 0;
		$highest_val = 0;
		$graphwidth = 200;
		$graphheight = 200;
		$graphtop = 10;
		$graphbottom = 30;
		$graphleft = '';
		$graph_color = "blue";
		$highlight_color = "red";

		if ("year" == $which) {
			parent::sdpvs_number_of_posts_per_year();
			$chart_array = $this -> year_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = $this -> first_val + 1;
			$order = "desc";
			if ("y" != $public) {
				echo '<h2>' . esc_html__('Years', 'post-volume-stats') . '</h2>';
			} else {
				echo '<h2>' . esc_html__('Posts per Year', 'post-volume-stats') . '</h2>';
			}
		} elseif ("dayofweek" == $which) {
			parent::sdpvs_number_of_posts_per_dayofweek($searchyear);
			$chart_array = $this -> dow_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 7;
			$order = "asc";
			if ("y" != $public) {
				echo '<h2>' . esc_html__('Days of the Week', 'post-volume-stats') . '</h2>';
			} else {
				echo '<h2>' . esc_html__('Posts per Day of the Week', 'post-volume-stats') . '</h2>';
			}
		} elseif ("hour" == $which) {
			parent::sdpvs_number_of_posts_per_hour($searchyear);
			$chart_array = $this -> hour_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 24;
			$order = "asc";
			if ("y" != $public) {
				echo '<h2>' . esc_html__('Hours', 'post-volume-stats') . '</h2>';
			} else {
				echo '<h2>' . esc_html__('Posts per Hour', 'post-volume-stats') . '</h2>';
			}
		} elseif ("month" == $which) {
			parent::sdpvs_number_of_posts_per_month($searchyear);
			$chart_array = $this -> month_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 12;
			$order = "asc";
			if ("y" != $public) {
				echo '<h2>' . esc_html__('Months', 'post-volume-stats') . '</h2>';
			} else {
				echo '<h2>' . esc_html__('Posts per Month', 'post-volume-stats') . '</h2>';
			}
		} elseif ("dayofmonth" == $which) {
			parent::sdpvs_number_of_posts_per_dayofmonth($searchyear);
			$chart_array = $this -> dom_array;
			parent::find_highest_first_and_total($chart_array);
			$bars_total = 31;
			$order = "asc";
			if ("y" != $public) {
				echo '<h2>' . esc_html__('Days of the Month', 'post-volume-stats') . '</h2>';
			} else {
				echo '<h2>' . esc_html__('Posts per Day of the Month', 'post-volume-stats') . '</h2>';
			}
		}
		if ("year" != $which and "y" == $public) {
			if (0 < $searchyear) {
				echo '<h3>' . sprintf(esc_html__('%d', 'post-volume-stats'), $searchyear) . '</h3>';
			} else {
				echo '<h3>' . esc_html__('All-time', 'post-volume-stats') . '</h3>';
			}
		}

		// specify the margin width on the left of the bar chart
		$graphleft = (strlen($this -> highest_val) * 7) + 5;

		$bar_width = $graphwidth / $bars_total;
		if (17 > $bar_width) {
			$text_indent = 0;
		} elseif (26 > $bar_width) {
			$text_indent = 2;
		} else {
			$text_indent = ($bar_width / 2) - 2;
		}
		$svgwidth = $graphwidth + $graphleft;
		$svgheight = $graphheight + $graphtop + $graphbottom;

		echo "<svg width=\"" . $svgwidth . "px\" height=\"" . $svgheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" class=\"sdpvs_bar\">\n";
		echo "<path stroke=\"black\" stroke-width=\"1\" d=\"M$graphleft $graphtop v $graphheight\"></path>";

		$number_per_increment = ceil($this -> highest_val / 5);
		// If an increment is a strange number, like 39, round it up or down to 40 or 35.
		if (5 < $number_per_increment) {
			$inc_mod = $number_per_increment % 5;
			if (0 == $inc_mod) {
			} elseif (0.5 < $inc_mod) {
				while (0 != $number_per_increment % 5) {
					$number_per_increment++;
				}
			} elseif (0.5 >= $inc_mod) {
				while (0 != $number_per_increment % 5) {
					$number_per_increment--;
				}
			}
		}
		$horiz_line_increment = $graphheight * ($number_per_increment / $this -> highest_val);

		for ($j = 0; $j <= 5; $j++) {
			$depth = $graphtop + $graphheight - ($j * $horiz_line_increment);
			if ($graphtop <= $depth) {
				$value = $j * $number_per_increment;
				if (0 == $j) {
					echo "<path stroke=\"black\" stroke-width=\"1\" d=\"M$graphleft $depth h $graphheight\"></path>";
				} else {
					echo "<path stroke=\"black\" stroke-width=\"0.2\" d=\"M$graphleft $depth h $graphheight\"></path>";
				}
				$text_x = $graphleft - (strlen($value) * 7) - 5;
				$text_y = $depth + 4;
				echo "<text x=\"$text_x\" y=\"$text_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"black\">$value</text>";
			}
		}
		$y_start = $graphheight + $graphtop;
		for ($i = 0; $i <= $this -> first_val; $i++) {
			if (0 < $chart_array[$i]['volume']) {
				if ("desc" == $order) {
					$x_start = $svgwidth - ($i * $bar_width);
				} elseif ("asc" == $order) {
					$x_start = $bar_width + $graphleft + ($i * $bar_width);
				}
				if ($chart_array[$i]['name'] == $searchyear and "year" == $which) {
					$color = $highlight_color;
					$set_explicit_color = "background-color: $color;";
				} else {
					$color = $graph_color;
					$set_explicit_color = "";
				}
				$bar_height = intval($graphheight * ($chart_array[$i]['volume'] / $this -> highest_val));

				if ("year" == $which) {
					if ($chart_array[$i]['name'] == $searchyear) {
						$year_form_value = "";
					} else {
						$year_form_value = $chart_array[$i]['name'];
					}
					$legend = $chart_array[$i]['name'];
					if (strlen($legend) * 7 < $bar_width) {
						$legend_x = $x_start - ($bar_width / 2) - (strlen($legend) * 7) / 2;
						$legend_y = $y_start + 17;
						echo "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"black\">" . sprintf(esc_html__('%d', 'my-text-domain'), $legend) . "</text>";
					}
					$form_y_offset = $y_start - $bar_height;
					$form_x_offset = $x_start - $bar_width;
					$slug = SDPVS__PLUGIN_FOLDER;

					if ("y" != $public) {
						echo "<path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"white\"></path>";
						echo "<foreignObject x=\"$form_x_offset\" y=\"$form_y_offset\" width=\"$bar_width\" height=\"$bar_height\">";
						echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
						settings_fields('sdpvs_year_option');
						// echo "<input type=\"hidden\" name=\"_wp_http_referer\" value=\"/wp-admin/admin.php?page=$slug\">";
						echo " <input type=\"hidden\" name=\"sdpvs_year_option[year_number]\" id=\"year-number\" value=\"$year_form_value\">
						<input type=\"submit\" style=\"height: " . $bar_height . "px; width: " . $bar_width . "px; $set_explicit_color\" title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\" class=\"sdpvs_year_bar\">
          				</form>
  						</foreignObject>";
					} else {
						echo "<a xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
					}

				} else {
					echo "<a xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
				}

			}

		}

		echo "</svg>\n";
		if ("n" == $subpage and "y" != $public) {
			echo "<form class='sdpvs_form' action='' method='POST'><input type='hidden' name='whichdata' value='$which'><input type='submit' class='button-primary sdpvs_load_content' value='Show Data'></form></p>";
		}

		return;
	}

}
?>
