<?php

defined('ABSPATH') or die('No script kiddies please!');

abstract class sdpvsArrays {

	protected $year_array = array();
	protected $category_array = array();
	protected $tag_array = array();
	protected $dow_array = array();
	protected $hour_array = array();
	protected $earliest_date = "";
	protected $latest_date = "";
	protected $published_volume = 0;

	protected $highest_val = 0;
	protected $first_val = 0;
	protected $total_volume_of_posts = 0;

	/*
	 * GET DETAILS FOR ONE CATEGORY / TAG
	 */

	protected function sdpvs_get_one_item_info($term_id = "", $taxonomy_type = "", $searchyear = "") {
		$term_id = absint($term_id);
		if (0 < $term_id and ('category' == $taxonomy_type or 'post_tag' == $taxonomy_type)) {
			global $wpdb;
			if ("" == $searchyear) {
				$count = $wpdb -> get_var($wpdb -> prepare("SELECT count FROM $wpdb->term_taxonomy WHERE taxonomy = %s AND term_id = %d ", $taxonomy_type, $term_id));
				$iteminfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $term_id));
				$one_item_array['name'] = $iteminfo -> name;
				$one_item_array['slug'] = $iteminfo -> slug;
				$one_item_array['volume'] = $count;
			} else {
				$tax_id = $wpdb -> get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = '$taxonomy_type' AND term_id = '$term_id' ");
				$count = $wpdb -> get_var("
					SELECT COUNT(*)
					FROM $wpdb->posts 
					INNER JOIN $wpdb->term_relationships 
					ON $wpdb->posts.post_status = 'publish' 
					AND $wpdb->posts.post_type = 'post' 
					AND $wpdb->posts.post_date LIKE '$searchyear%' 	
					AND $wpdb->term_relationships.object_id = $wpdb->posts.ID 
					AND $wpdb->term_relationships.term_taxonomy_id = $tax_id
				");
				$iteminfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $term_id));
				$one_item_array['name'] = $iteminfo -> name;
				$one_item_array['slug'] = $iteminfo -> slug;
				$one_item_array['volume'] = $count;
			}

			return $one_item_array;
		}
		return;
	}

	/*
	 * NUMBER OF POSTS PER CATEGORY
	 */
	protected function sdpvs_post_category_volumes($searchyear = "") {
		global $wpdb;
		if ("" == $searchyear) {
			$cats = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY count DESC ");
			$c = 0;
			foreach ($cats as $category) {
				$catinfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $category -> term_id));
				$this -> category_array[$c]['id'] = $category -> term_id;
				$this -> category_array[$c]['name'] = $catinfo -> name;
				$this -> category_array[$c]['slug'] = $catinfo -> slug;
				$this -> category_array[$c]['volume'] = $category -> count;
				$this -> category_array[$c]['angle'] = 0;
				$c++;
			}
		} else {
			$cats = $wpdb -> get_results("SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id DESC ");
			$c = 0;
			$highestval = 0;
			// if ($cats) {
			foreach ($cats as $category) {
				$posts = 0;
				$posts = $wpdb -> get_var("
					SELECT COUNT(*)
					FROM $wpdb->posts 
					INNER JOIN $wpdb->term_relationships 
					ON $wpdb->posts.post_status = 'publish' 
					AND $wpdb->posts.post_type = 'post' 
					AND $wpdb->posts.post_date LIKE '$searchyear%' 	
					AND $wpdb->term_relationships.object_id = $wpdb->posts.ID 
					AND $wpdb->term_relationships.term_taxonomy_id = $category->term_taxonomy_id
				");
				if (0 < $posts) {
					$cat_array[$c]['id'] = $category -> term_id;
					$cat_array[$c]['volume'] = $posts;
					if ($highestval < $posts) {
						$highestval = $posts;
					}
					$c++;
				}
			}
			// }
			$d = 0;
			for ($i = $highestval; $i > 0; $i--) {
				$c = 0;
				while (array_key_exists($c, $cat_array)) {
					if ($i == $cat_array[$c]['volume'] and 0 < $cat_array[$c]['id']) {
						$temp = $cat_array[$c]['id'];
						$catinfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $temp));
						$this -> category_array[$d]['id'] = $temp;
						$this -> category_array[$d]['name'] = $catinfo -> name;
						$this -> category_array[$d]['slug'] = $catinfo -> slug;
						$this -> category_array[$d]['volume'] = $cat_array[$c]['volume'];
						$this -> category_array[$d]['angle'] = 0;
						$d++;
					}
					$c++;
				}
			}
		}

		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER TAG
	 */
	protected function sdpvs_post_tag_volumes($searchyear = "") {
		global $wpdb;
		if ("" == $searchyear) {
			$taglist = $wpdb -> get_results("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' ORDER BY count DESC ");
			$t = 0;
			foreach ($taglist as $tag) {
				$taginfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tag -> term_id));
				$this -> tag_array[$t]['id'] = $tag -> term_id;
				$this -> tag_array[$t]['name'] = $taginfo -> name;
				$this -> tag_array[$t]['slug'] = $taginfo -> slug;
				$this -> tag_array[$t]['volume'] = $tag -> count;
				$this -> tag_array[$t]['angle'] = 0;
				$t++;
			}
		} else {
			$tags = $wpdb -> get_results("SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' ORDER BY term_id DESC ");
			$t = 0;
			$highestval = 0;
			foreach ($tags as $tag) {
				$posts = 0;
				$posts = $wpdb -> get_var("
					SELECT COUNT(*)
					FROM $wpdb->posts 
					INNER JOIN $wpdb->term_relationships 
					ON $wpdb->posts.post_status = 'publish' 
					AND $wpdb->posts.post_type = 'post' 
					AND $wpdb->posts.post_date LIKE '$searchyear%' 	
					AND $wpdb->term_relationships.object_id = $wpdb->posts.ID 
					AND $wpdb->term_relationships.term_taxonomy_id = $tag->term_taxonomy_id
				");
				if (0 < $posts) {
					$tg_array[$t]['id'] = $tag -> term_id;
					$tg_array[$t]['volume'] = $posts;
					if ($highestval < $posts) {
						$highestval = $posts;
					}
					$t++;
				}
			}
			$d = 0;
			for ($i = $highestval; $i > 0; $i--) {
				$t = 0;
				while (array_key_exists($t, $tg_array)) {
					if ($i == $tg_array[$t]['volume']) {
						$temp = $tg_array[$t]['id'];
						$taginfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $temp));
						$this -> tag_array[$d]['id'] = $temp;
						$this -> tag_array[$d]['name'] = $taginfo -> name;
						$this -> tag_array[$d]['slug'] = $taginfo -> slug;
						$this -> tag_array[$d]['volume'] = $tg_array[$t]['volume'];
						$this -> tag_array[$d]['angle'] = 0;
						$d++;
					}
					$t++;
				}
			}
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
			$this -> year_array[$i]['name'] = $searchyear;
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
			$this -> dow_array[$w]['name'] = $days_of_week[$w];
			$this -> dow_array[$w]['volume'] = 0;
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
			$this -> hour_array[$i]['name'] = "$j:00-$j:59";
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
			$this -> month_array[$w]['name'] = $months_of_year[$w];
			$this -> month_array[$w]['volume'] = 0;
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
			$this -> dom_array[$i]['name'] = $searchday;
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
	protected function find_highest_first_and_total($testarray = array()) {
		$this -> highest_val = 0;
		$this -> first_val = 0;
		$this -> total_volume_of_posts = 0;
		$i = 0;
		while (array_key_exists($i, $testarray)) {
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
