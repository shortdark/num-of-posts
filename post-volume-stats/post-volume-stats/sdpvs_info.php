<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsInfo extends sdpvsArrays {

	/*
	 * WRITE THE INFO
	 */
	public function sdpvs_info() {
		parent::sdpvs_earliest_date_post();
		parent::sdpvs_latest_date_post();
		parent::sdpvs_total_published_volume();

		$diff = abs(strtotime($this -> latest_date) - strtotime($this -> earliest_date));
		$days = floor($diff / (60 * 60 * 24));

		$ppd = $this -> published_volume / $days;

		$posts_per_day = sprintf("%.5f", $ppd);

		echo '<p>' . sprintf(esc_html__('%d published posts over %d days is %f posts per day.', 'post-volume-stats'), $this -> published_volume, $days, $posts_per_day) . '</p>';
		// echo '<p>' . sprintf(esc_html__('Earliest post date: %s', 'post-volume-stats'), $this -> earliest_date) . '</p>';
		// echo '<p>' . sprintf(esc_html__('Latest post date: %s', 'post-volume-stats'), $this -> latest_date) . '</p>';

		return;
	}

	/*
	 * GET THE NUMBER OF YEARS FOR THE SETTINGS PAGE
	 */
	public function sdpvs_how_many_years_of_posts() {
		parent::sdpvs_number_of_posts_per_year();
		$chart_array = $this -> year_array;
		parent::find_highest_first_and_total($chart_array);
		$bars_total = $this -> first_val;
		return $bars_total;
	}

}
?>
