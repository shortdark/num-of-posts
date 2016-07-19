<?php

class sdpvs_text_lists extends sdpvs_arrays {
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	function sdpvs_posts_per_year_list() {
		parent::sdpvs_number_of_posts_per_year();
		$posts_per_year = "<h2>Post Volumes per Year</h2>";
		$posts_per_year .= "<p>Goes back up to 31 years.</p>";
		$i = 0;
		while ($this -> year_array[$i]['title']) {
			if (0 < $this -> year_array[$i]['volume']) {
				$posts_per_year .= "{$this->year_array[$i]['title']}: {$this->year_array[$i]['volume']} posts<br>\n";
			}
			$i++;
		}
		return $posts_per_year;
	}

	/*
	 * NUMBER OF POSTS PER CATEGORY TEXT
	 */
	function sdpvs_posts_per_category_list() {
		parent::sdpvs_post_category_volumes();
		$posts_per_category = "<h2>Post Volumes per Category!</h2>";
		$c = 0;
		while ($this -> category_array[$c]['id']) {
			if (0 < $this -> category_array[$c]['volume']) {
				$posts_per_category .= "<a href='" . admin_url('edit.php?category_name=' . $this -> category_array[$c]['slug']) . "'>{$this->category_array[$c]['name']}</a>: {$this->category_array[$c]['volume']} posts<br>\n";
			}
			$c++;
		}
		return $posts_per_category;
	}

	/*
	 * NUMBER OF POSTS PER TAG TEXT
	 */
	function sdpvs_posts_per_tag_list() {
		parent::sdpvs_post_tag_volumes();
		$posts_per_tag = "<h2>Post Volumes per Tag!</h2>";
		$t = 0;
		while ($this -> tag_array[$t]['id']) {
			if (0 < $this -> tag_array[$t]['volume']) {
				$posts_per_tag .= "<a href='" . admin_url('edit.php?tag=' . $this -> tag_array[$t]['slug']) . "'>{$this->tag_array[$t]['name']}</a>: {$this->tag_array[$t]['volume']} posts<br>\n";
			}
			$t++;
		}
		return $posts_per_tag;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK TEXT
	 */
	function sdpvs_posts_per_dayofweek_list() {
		parent::sdpvs_number_of_posts_per_dayofweek();
		$posts_per_dow = "<h2>Post Volumes per Day of the Week</h2>";
		for ($i = 0; $i <= 6; $i++) {
			if (!$this -> dow_array[$i]['volume']) {
				$this -> dow_array[$i]['volume'] = 0;
			}
			$posts_per_dow .= "{$this->dow_array[$i]['title']}: {$this->dow_array[$i]['volume']} posts<br>\n";
		}
		return $posts_per_dow;
	}

	/*
	 * NUMBER OF POSTS PER HOUR TEXT
	 */
	function sdpvs_posts_per_hour_list() {
		parent::sdpvs_number_of_posts_per_hour();
		$posts_per_hour = "<h2>Post Volumes per Hour</h2>";
		for ($i = 0; $i <= 23; $i++) {
			$posts_per_hour .= "{$this->hour_array[$i]['title']}: {$this->hour_array[$i]['volume']} posts<br>\n";
		}
		return $posts_per_hour;
	}

}
?>
