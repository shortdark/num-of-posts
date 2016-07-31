<?php

abstract class sdpvsArrays {

	protected $year_array;
	protected $category_array;
	protected $tag_array;
	protected $dow_array;
	protected $hour_array;
	protected $earliest_date;
	protected $latest_date;
	protected $published_volume;

	/*
	 * NUMBER OF POSTS PER CATEGORY
	 */
	protected function sdpvs_post_category_volumes() {
		global $wpdb;
		$cats = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY count DESC ");

		$c = 0;
		foreach ($cats as $category) {
			$catinfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $category -> term_id));
			$this -> category_array[$c]['id'] = $category -> term_id;
			$this -> category_array[$c]['name'] = $catinfo -> name;
			$this -> category_array[$c]['slug'] = $catinfo -> slug;
			$this -> category_array[$c]['volume'] = $category -> count;
			$c++;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER TAG
	 */
	protected function sdpvs_post_tag_volumes() {
		global $wpdb;
		$taglist = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' ORDER BY count DESC ");
		$t = 0;
		foreach ($taglist as $tag) {
			$taginfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tag -> term_id));
			$this -> tag_array[$t]['id'] = $tag -> term_id;
			$this -> tag_array[$t]['name'] = $taginfo -> name;
			$this -> tag_array[$t]['slug'] = $taginfo -> slug;
			$this -> tag_array[$t]['volume'] = $tag -> count;
			$t++;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER YEAR
	 */
	protected function sdpvs_number_of_posts_per_year() {
		$currentyear = date('Y');
		global $wpdb;
		for ($i = 0; $i <= 30; $i++) {
			$searchyear = $currentyear - $i;
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '$searchyear%' ");
			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> year_array[$i]['title'] = $searchyear;
			$this -> year_array[$i]['volume'] = $found_posts;
			$wpdb -> flush();
		}
		return;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK
	 */
	protected function sdpvs_number_of_posts_per_dayofweek($searchyear = "") {
		$searchyear = absint($searchyear);
		$days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
		for ($w = 0; $w <= 6; $w++) {
			$this -> dow_array[$w]['title'] = $days_of_week[$w];
		}
		global $wpdb;
		if (0 < $searchyear) {
			$myblogitems = $wpdb -> get_results("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '$searchyear%' ");
		} else {
			$myblogitems = $wpdb -> get_results("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ");
		}
		foreach ($myblogitems as $dowpost) {
			$year = substr($dowpost -> post_date, 0, 4);
			$month = substr($dowpost -> post_date, 5, 2);
			$day = substr($dowpost -> post_date, 8, 2);
			$tempdate = mktime(0, 0, 0, $month, $day, $year);
			$d = date("w", $tempdate);
			$this -> dow_array[$d]['volume']++;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER HOUR
	 */
	protected function sdpvs_number_of_posts_per_hour($searchyear = "") {
		$searchyear = absint($searchyear);
		global $wpdb;
		for ($i = 0; $i <= 23; $i++) {
			$searchhour = sprintf("%02s", $i);
			if (0 < $searchyear) {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '% $searchhour:%' AND post_date LIKE '$searchyear%' ");
			} else {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '% $searchhour:%' ");
			}
			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$j = sprintf("%02s", $i);
			$this -> hour_array[$i]['title'] = "$j:00-$j:59";
			$this -> hour_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER MONTH
	 */
	protected function sdpvs_number_of_posts_per_month($searchyear = "") {
		$months_of_year = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$searchyear = absint($searchyear);
		for ($w = 0; $w < 12; $w++) {
			$this -> month_array[$w]['title'] = $months_of_year[$w];
		}
		global $wpdb;
		for ($i = 0; $i < 12; $i++) {
			$j = $i + 1;
			$searchmonth = sprintf("%02s", $j);
			if (0 < $searchyear) {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchmonth-%' AND post_date LIKE '$searchyear%' ");
			} else {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchmonth-%' ");
			}

			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> month_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-THE-MONTH
	 */
	protected function sdpvs_number_of_posts_per_dayofmonth($searchyear = "") {
		$searchyear = absint($searchyear);
		global $wpdb;
		for ($i = 0; $i < 31; $i++) {
			$j = $i + 1;
			$searchday = sprintf("%02s", $j);
			if (0 < $searchyear) {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchday %' AND post_date LIKE '$searchyear%' ");
			} else {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchday %' ");
			}

			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> dom_array[$i]['title'] = $searchday;
			$this -> dom_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * FIND THE POST WITH THE EARLIEST DATE
	 */
	protected function sdpvs_earliest_date_post() {
		global $wpdb;
		$this -> earliest_date = $wpdb -> get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date ASC LIMIT 1 ");
		$wpdb -> flush();
		return;
	}

	/*
	 * FIND THE POST WITH THE LATEST DATE
	 */
	protected function sdpvs_latest_date_post() {
		global $wpdb;
		$this -> latest_date = $wpdb -> get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 1 ");
		$wpdb -> flush();
		return;
	}

	/*
	 * FIND THE TOTAL VOLUME OF POSTS
	 */
	protected function sdpvs_total_published_volume() {
		global $wpdb;
		$this -> published_volume = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ");
		$wpdb -> flush();
		return;
	}

	/*
	 * FIND HIGHEST, FIRST AND TOTAL VOLUME VALUES
	 */
	protected function find_highest_first_and_total($testarray) {
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

	function __construct() {

	}

}
?>
