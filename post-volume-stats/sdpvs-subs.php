<?php

defined('ABSPATH') or die('No script kiddies please!' );

	class sdpvsSubPages {

	public function sdpvs_category_page_content() {
		// create an instance of the required classes
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();
		$sdpvs_lists = new sdpvsTextLists();

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);
		
		
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Categories', 'post-volume-stats') . '</h1>';

		// Plugin Description
		if (0 < $searchyear) {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the "Category" stats for %s for the selected year: %d. Click a bar of the "Years" bar chart to change to that year or click the selected year (red) to view the all-time stats.', 'post-volume-stats'), get_bloginfo('name'), $searchyear) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the all-time "Category" stats for %s. Click a bar of the "Years" bar chart to change to that year.', 'post-volume-stats'), get_bloginfo('name')) . '</p>';
		}

		echo "<hr>";

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $searchyear, 'y');
		echo "</div>";
		
		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('category', $searchyear,'y');
		echo "</div>";

		echo "<hr>";
		
		echo $sdpvs_lists -> sdpvs_posts_per_category_list($searchyear);
		
		echo '<h2>' . esc_html__('Coming Soon', 'post-volume-stats') . '</h2>';
		echo '<p>' . esc_html__("It'll soon be easier to export these results.", 'post-volume-stats') . '</p>';

		return;
	}


	public function sdpvs_tag_page_content() {
		// create an instance of the required classes
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();
		$sdpvs_lists = new sdpvsTextLists();

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);
		
		
		echo '<h1 class="sdpvs">' . esc_html__('Post Volume Stats: Tags', 'post-volume-stats') . '</h1>';

		// Plugin Description
		if (0 < $searchyear) {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the "Tag" stats for %s for the selected year: %d. Click a bar of the "Years" bar chart to change to that year or click the selected year (red) to view the all-time stats.', 'post-volume-stats'), get_bloginfo('name'), $searchyear) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the all-time "Tag" stats for %s. Click a bar of the "Years" bar chart to change to that year.', 'post-volume-stats'), get_bloginfo('name')) . '</p>';
		}

		echo "<hr>";

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $searchyear, 'y');
		echo "</div>";
		
		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg('tag', $searchyear,'y');
		echo "</div>";

		echo "<hr>";
		
		echo $sdpvs_lists -> sdpvs_posts_per_tag_list($searchyear);
		
		echo '<h2>' . esc_html__('Coming Soon', 'post-volume-stats') . '</h2>';
		echo '<p>' . esc_html__("It'll soon be easier to export these results.", 'post-volume-stats') . '</p>';

		return;
	}

}
?>
