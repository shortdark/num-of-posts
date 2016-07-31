<?php

class sdpvsInfo extends sdpvsArrays {

	/*
	 * WRITE THE INFO
	 */
	public function sdpvs_info() {
		parent::sdpvs_earliest_date_post();
		parent::sdpvs_latest_date_post();
		parent::sdpvs_total_published_volume();

		// $date1 = $this->earliest_date;
		// $date2 = $this->latest_date;

		$diff = abs(strtotime($this -> latest_date) - strtotime($this -> earliest_date));
		$days = floor($diff / (60 * 60 * 24));

		$ppd = $this -> published_volume / $days;

		$posts_per_day = sprintf("%.5f", $ppd);

		$sdpvs_info .= __("<p>" . $this -> published_volume . " published posts over " . $days . " days is " . $posts_per_day . " posts per day.</p>", 'post-volume-stats');
		$sdpvs_info .= __("<p>Earliest post date: " . $this -> earliest_date . "</p>", 'post-volume-stats');
		$sdpvs_info .= __("<p>Latest post date: " . $this -> latest_date . "</p>", 'post-volume-stats');

		return $sdpvs_info;
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
