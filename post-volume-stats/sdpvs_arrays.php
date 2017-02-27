<?php

defined('ABSPATH') or die('No script kiddies please!');

abstract class sdpvsArrays {

	protected $tax_type_array = array();
	protected $list_array = array();
	protected $earliest_date = "";
	protected $latest_date = "";
	protected $published_volume = 0;

	protected $highest_val = 0;
	protected $first_val = 0;
	protected $total_volume_of_posts = 0;

	/*
	 * GET DETAILS FOR ONE CATEGORY / TAG
	 */

	protected function sdpvs_get_one_item_info($term_id = "", $taxonomy_type = "", $searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra = "";
		$term_id = absint($term_id);
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		if (0 < $searchyear) {
			$extra = " AND $wpdb->posts.post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND $wpdb->posts.post_author = '$searchauthor' ";
		}
		if (0 < $term_id and "" != $taxonomy_type ) {
			if ("" == $searchyear and "" == $searchauthor) {
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
					$extra
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
	 * NUMBER OF POSTS PER TAXONOMY TYPE (Tags, Categories, Custom)
	 */
	protected function sdpvs_post_taxonomy_type_volumes($tax_type = "", $searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$tax_results="";
		$this -> tax_type_array = array();
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		if (0 < $searchyear) {
			$extra = " AND $wpdb->posts.post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND $wpdb->posts.post_author = '$searchauthor' ";
		}
		if ("" == $searchyear and "" == $searchauthor) {
			$tax_results = $wpdb -> get_results($wpdb -> prepare("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY count DESC ", $tax_type));
			$c = 0;
			foreach ($tax_results as $tax_item) {
				$taxinfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tax_item -> term_id));
				$this -> tax_type_array[$c]['id'] = $tax_item -> term_id;
				$this -> tax_type_array[$c]['name'] = $taxinfo -> name;
				$this -> tax_type_array[$c]['slug'] = $taxinfo -> slug;
				$this -> tax_type_array[$c]['volume'] = $tax_item -> count;
				$this -> tax_type_array[$c]['angle'] = 0;
				$c++;
			}
		} else {
			$tax_results = $wpdb -> get_results("SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = '$tax_type' ORDER BY term_id DESC ");
			$c = 0;
			$highestval = 0;
			// if ($tax_results) {
			foreach ($tax_results as $tax_item) {
				$posts = 0;
				$posts = $wpdb -> get_var("
					SELECT COUNT(*)
					FROM $wpdb->posts 
					INNER JOIN $wpdb->term_relationships 
					ON $wpdb->posts.post_status = 'publish' 
					AND $wpdb->posts.post_type = 'post' 
					$extra
					AND $wpdb->term_relationships.object_id = $wpdb->posts.ID 
					AND $wpdb->term_relationships.term_taxonomy_id = $tax_item->term_taxonomy_id
				");
				if (0 < $posts) {
					$cat_array[$c]['id'] = $tax_item -> term_id;
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
						$taxinfo = $wpdb -> get_row($wpdb -> prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $temp));
						$this -> tax_type_array[$d]['id'] = $temp;
						$this -> tax_type_array[$d]['name'] = $taxinfo -> name;
						$this -> tax_type_array[$d]['slug'] = $taxinfo -> slug;
						$this -> tax_type_array[$d]['volume'] = $cat_array[$c]['volume'];
						$this -> tax_type_array[$d]['angle'] = 0;
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
	 * NUMBER OF POSTS PER YEAR
	 */
	protected function sdpvs_number_of_posts_per_year($searchauthor = "") {
		global $wpdb;
		$extra="";
		$currentyear = date('Y');
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		if("" != $searchauthor){
			$extra = " AND post_author = '$searchauthor' ";
		}
		for ($i = 0; $i <= 30; $i++) {
			$searchyear = $currentyear - $i;
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '$searchyear%' $extra ");
			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> list_array[$i]['name'] = $searchyear;
			$this -> list_array[$i]['volume'] = $found_posts;
			$wpdb -> flush();
		}
		return;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK
	 */
	protected function sdpvs_number_of_posts_per_dayofweek($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$genoptions = get_option('sdpvs_general_settings');
		$startweek = filter_var ( $genoptions['startweekon'], FILTER_SANITIZE_STRING);
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		if (0 < $searchyear) {
			$extra = " AND post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND post_author = '$searchauthor' ";
		}
		if("sunday" == $startweek or !$startweek){
			$days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
			for ($w = 0; $w <= 6; $w++) {
				$this -> list_array[$w]['name'] = $days_of_week[$w];
				$this -> list_array[$w]['volume'] = 0;
			}
			$weekletter = "w";
		}else{
			$days_of_week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday");
			for ($w = 0; $w <= 6; $w++) {
				$this -> list_array[$w]['name'] = $days_of_week[$w];
				$this -> list_array[$w]['volume'] = 0;
			}
			$weekletter = "N";
		}
		
		$myblogitems = $wpdb -> get_results("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
		foreach ($myblogitems as $dowpost) {
			$year = substr($dowpost -> post_date, 0, 4);
			$month = substr($dowpost -> post_date, 5, 2);
			$day = substr($dowpost -> post_date, 8, 2);
			$tempdate = mktime(0, 0, 0, $month, $day, $year);
			$d = date($weekletter, $tempdate);
			if("w" == $weekletter){
				$this -> list_array[$d]['volume']++;
			}else{
				$g = $d-1;
				$this -> list_array[$g]['volume']++;
			}
			
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER HOUR
	 */
	protected function sdpvs_number_of_posts_per_hour($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		if (0 < $searchyear) {
			$extra = " AND post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND post_author = '$searchauthor' ";
		}
		for ($i = 0; $i <= 23; $i++) {
			$searchhour = sprintf("%02s", $i);
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '% $searchhour:%' $extra ");
			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$j = sprintf("%02s", $i);
			$this -> list_array[$i]['name'] = "$j:00-$j:59";
			$this -> list_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER MONTH
	 */
	protected function sdpvs_number_of_posts_per_month($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$months_of_year = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		if (0 < $searchyear) {
			$extra = " AND post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND post_author = '$searchauthor' ";
		}
		for ($w = 0; $w < 12; $w++) {
			$this -> list_array[$w]['name'] = $months_of_year[$w];
			$this -> list_array[$w]['volume'] = 0;
		}
		for ($i = 0; $i < 12; $i++) {
			$j = $i + 1;
			$searchmonth = sprintf("%02s", $j);
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchmonth-%' $extra ");
			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> list_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-THE-MONTH
	 */
	protected function sdpvs_number_of_posts_per_dayofmonth($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		
		if (0 < $searchyear) {
			$extra = " AND post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND post_author = '$searchauthor' ";
		}
		for ($i = 0; $i < 31; $i++) {
			$j = $i + 1;
			$searchday = sprintf("%02s", $j);
			
			$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchday %' $extra ");

			if (0 > $found_posts or !$found_posts or "" == $found_posts) {
				$found_posts = 0;
			}
			$this -> list_array[$i]['name'] = $searchday;
			$this -> list_array[$i]['volume'] = $found_posts;
		}
		$wpdb -> flush();
		return;
	}
	
	/*
	 * NUMBER OF POSTS PER AUTHOR
	 */
	protected function sdpvs_number_of_posts_per_author($searchyear = "") {
		global $wpdb;
		$this -> list_array = array();
		
		$blogusers = get_users( array( 'who'  => 'authors' ) );
		
		// Array of WP_User objects.
		$a=0;
		foreach ( $blogusers as $user ) {
			$post_author = absint($user->ID);
			if (0 < $searchyear) {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = '$post_author' AND post_date LIKE '$searchyear%' ");
			} else {
				$found_posts = $wpdb -> get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = '$post_author' ");
			}
			if(1 <= $found_posts){
				$this -> list_array[$a]['id'] = absint($user->ID);
				$this -> list_array[$a]['name'] = $user->display_name;
				$this -> list_array[$a]['volume'] = $found_posts;
				$a++;
			}
		}
		$wpdb -> flush();
		
		function sortByOrder($j, $k) {
			return $k['volume'] - $j['volume'];
		}
		
		if(1 < $a){
			usort($this -> list_array, 'sortByOrder');
		}
		
		return;
	}
	
	/*
	 * NUMBER OF WORDS PER POST
	 */
	protected function sdpvs_number_of_words_per_post($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();
		$chart_iterations = 20;
		$maxvalue = 0;
		
		if("" != $searchauthor){
			$extra = " AND post_author = '$searchauthor' ";
		}
		
		$total_posts = $wpdb -> get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
		if ($total_posts) {
			foreach ($total_posts as $post_item) {
				$temp_content = filter_var ( $post_item -> post_content , FILTER_SANITIZE_STRING);
				$word_count = str_word_count( strip_tags( $temp_content ), 0, '123456789&;#' );
				if($maxvalue < $word_count){
					$maxvalue = $word_count;
				}
			}
			
			if(0 < $maxvalue){
				$vol_per_bar = ceil($maxvalue / $chart_iterations);
				// Make the iterations even, i.e. multiples of 10
				if(0!= $vol_per_bar % 10){
					while(0!= $vol_per_bar % 10){
						$vol_per_bar++;
					}
				}
			}
			
			for($h=0;$h<$chart_iterations;$h++){
				$lower = $h * $vol_per_bar;
				if( $chart_iterations > $h ){
					$upper = ($h * $vol_per_bar) + $vol_per_bar-1;
					$this -> list_array[$h]['name'] = "$lower - $upper words";
				}else{
					$this -> list_array[$h]['name'] = "$lower+ words";
				}
				$this -> list_array[$h]['volume'] = 0;
			}
		}
		
		if (0 < $searchyear) {
			$extra .= " AND post_date LIKE '$searchyear%' ";
		}
		
		$found_posts = $wpdb -> get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
		if ($found_posts) {
			foreach ($found_posts as $post_item) {
				$temp_content = filter_var ( $post_item -> post_content , FILTER_SANITIZE_STRING);
				$word_count = str_word_count( strip_tags( $temp_content ), 0, '123456789&;#' );
				$temp_array[] = $word_count;
			}
		}
		$wpdb -> flush();
		
		if($temp_array){
			natsort($temp_array);
			
			foreach($temp_array as $word_count){
				if( 0 == $word_count % $vol_per_bar ){
					$i = absint( $word_count / $vol_per_bar ) -1;
				}else{
					$i = absint( $word_count / $vol_per_bar );
				}
				if($chart_iterations <= $i){
					$i = $chart_iterations;
				}
				$this -> list_array[$i]['volume'] ++;
			}

		}
		
		
		
		
		return;
	}
	
	protected function sdpvs_number_of_words_per_post_orig($searchyear = "", $searchauthor = "") {
		global $wpdb;
		$extra="";
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		$this -> list_array = array();

			for($h=0;$h<=15;$h++){
				$lower = $h * 50;
				if( 15 > $h ){
					$upper = ($h * 50) + 49;
					$this -> list_array[$h]['name'] = "$lower - $upper words";
				}else{
					$this -> list_array[$h]['name'] = "$lower+ words";
				}
				$this -> list_array[$h]['volume'] = 0;
			}

		if (0 < $searchyear) {
			$extra = " AND post_date LIKE '$searchyear%' ";
		}
		if("" != $searchauthor){
			$extra .= " AND post_author = '$searchauthor' ";
		}
		$found_posts = $wpdb -> get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
		if ($found_posts) {
			foreach ($found_posts as $post_item) {
				$temp_content = filter_var ( $post_item -> post_content , FILTER_SANITIZE_STRING);
				$word_count = str_word_count( strip_tags( $temp_content ), 0, '123456789&;#' );
				if( 0 == $word_count % 50 ){
					$i = absint( $word_count / 50 ) -1;
				}else{
					$i = absint( $word_count / 50 );
				}
				if(15 < $i){
					$i = 15;
				}
				$this -> list_array[$i]['volume'] ++;
			}
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
		$this -> total_bars = 0;
		$i = 0;
		while (array_key_exists($i, $testarray)) {
			$this -> total_volume_of_posts += $testarray[$i]['volume'];
			if (0 < $testarray[$i]['volume'] and $this -> highest_val < $testarray[$i]['volume']) {
				$this -> highest_val = $testarray[$i]['volume'];
			}
			if (0 < $testarray[$i]['volume']) {
				$this -> first_val = $i;
			}
			$this -> total_bars ++;
			$i++;
		}

		return;
	}

	function __construct() {

	}

}
?>
