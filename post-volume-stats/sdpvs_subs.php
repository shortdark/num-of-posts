<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsSubPages {

	public function sdpvs_combined_page_content($type = "") {
		// create an instance of the required classes
		$sdpvs_bar = new sdpvsBarChart();
		$sdpvs_pie = new sdpvsPieChart();
		$sdpvs_lists = new sdpvsTextLists();

		$year = get_option('sdpvs_year_option');
		$searchyear = absint($year['year_number']);

		if ("category" == $type) {
			$typetitle = "Category";
			$typetitleplural = "Categories";
		} elseif ("tag" == $type) {
			$typetitle = "Tag";
			$typetitleplural = "Tags";
		}

		echo '<h1 class="sdpvs">' . sprintf(esc_html__('Post Volume Stats: %s', 'post-volume-stats'), $typetitleplural) . '</h1>';

		// Plugin Description
		if (0 < $searchyear) {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the "%1$s" stats for %2$s for the selected year: %3$d. Click a bar of the "Years" bar chart to change to that year or click the selected year (red) to view the all-time stats.', 'post-volume-stats'), $typetitle, get_bloginfo('name'), $searchyear) . '</p>';
		} else {
			echo '<p class="sdpvs">' . sprintf(esc_html__('These are the all-time "%1$s" stats for %2$s. Click a bar of the "Years" bar chart to change to that year.', 'post-volume-stats'), $typetitle, get_bloginfo('name')) . '</p>';
		}

		echo "<hr>";

		// year bar chart
		echo "<div class='sdpvs_col'>";
		$sdpvs_bar -> sdpvs_draw_bar_chart_svg('year', $searchyear, 'y');
		echo "</div>";

		// posts per tag pie chart
		echo "<div class='sdpvs_col'>";
		echo $sdpvs_pie -> sdpvs_draw_pie_svg($type, $searchyear, 'y');
		echo "</div>";

		echo "<hr>";

		echo "<div style='display: inline-block; width: 250px; vertical-align: top;' id='sdpvs_listselect'>";
		if ("category" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('category', $searchyear, 'subpage', '');
		} elseif ("tag" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('tag', $searchyear, 'subpage', '');
		}
		echo "</div>";

		// Get both methods from AJAX call.
		echo "<div style='display: inline-block; vertical-align: top;' id='sdpvs_ajax_lists'>";

		echo "</div>";

		return;
	}

	function update_ajax_lists($type, $searchyear, $matches) {
			
		$sdpvs_lists = new sdpvsTextLists();
		
		echo "<div style='display: inline-block; width: 500px; vertical-align: top;' id='sdpvs_listsource'>";
		if ("category" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('category', $searchyear, 'source', $matches);
		} elseif ("tag" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('tag', $searchyear, 'source', $matches);
		}
		echo "</div>";

		echo "<div style='display: inline-block; width: 250px; vertical-align: top;' id='sdpvs_listpublic'>";
		if ("category" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('category', $searchyear, 'public', $matches);
		} elseif ("tag" == $type) {
			echo $sdpvs_lists -> sdpvs_posts_per_cat_tag_list('tag', $searchyear, 'public', $matches);
		}
		echo "</div>";

		return;

	}

}
?>