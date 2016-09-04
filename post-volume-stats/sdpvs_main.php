<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsMainContent {

	public function sdpvs_page_content() {

		// create an instance of the required classes
		$sdpvs_info = new sdpvsInfo();
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();

		$years = $sdpvs_info -> sdpvs_how_many_years_of_posts();
		$options = get_option('sdpvs_year_option');
		$selected = absint($options['year_number']);
		
		// Plugin Description
		if (0 < $selected) {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the stats for %s for the selected year: %d. Click a bar of the "Years" bar chart to change to that year or click the selected year (red) to view the all-time stats.', 'post-volume-stats'), get_bloginfo('name'), $selected) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the all-time stats for %s. Click a bar of the "Years" bar chart to change to that year.', 'post-volume-stats'), get_bloginfo('name')) . '</p>';
		}

		echo "<hr>";

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $selected,'n');
		echo "</div>";

		// posts per month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('month', $selected,'n');
		echo "</div>";

		// posts per day of the month bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofmonth', $selected,'n');
		echo "</div>";

		// posts per day of the week bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('dayofweek', $selected,'n');
		echo "</div>";

		// posts per hour of the day bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('hour', $selected,'n');
		echo "</div>";

		// posts per category pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('category', $selected,'n');
		echo "</div>";

		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('tag', $selected,'n');
		echo "</div>";

		return;
	}

}
?>
