<?php

class sdpvs_arrays {

	protected $year_array;
	protected $category_array;
	protected $tag_array;
	protected $dow_array;
	protected $hour_array;

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
	 * NUMBER OF POSTS PER CATEGORY
	 */
	protected function sdpvs_post_category_volumes() {
		global $wpdb;
		$cats = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'category'");
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
		$taglist = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag'");
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
	 * NUMBER OF POSTS PER DAY-OF-WEEK
	 */
	protected function sdpvs_number_of_posts_per_dayofweek() {
		for($w=0;$w<=6;$w++){
			$this -> dow_array[$w]['title'] = jddayofweek($w, 1);
		}
		global $wpdb;
		$posts = $wpdb -> get_results("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ");
		foreach ($posts as $post) {
			$year = substr($post -> post_date, 0, 4);
			$month = substr($post -> post_date, 5, 2);
			$day = substr($post -> post_date, 8, 2);
			$d = date("w", mktime(0, 0, 0, $month, $day, $year));
			$this -> dow_array[$d]['volume']++;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER HOUR
	 */
	protected function sdpvs_number_of_posts_per_hour() {
		global $wpdb;
		for ($i = 0; $i <= 23; $i++) {
			$searchhour = sprintf("%02s", $i);
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '% $searchhour:%' ");
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

	function __construct() {

	}

}
?>
