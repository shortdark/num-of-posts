<?php

class sdpvsTextLists extends sdpvsArrays {
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	public function sdpvs_posts_per_year_list() {
		parent::sdpvs_number_of_posts_per_year();
		parent::find_highest_first_and_total($this->year_array);
		$number_of_years = $this -> first_val + 1;
		$posts_per_year = __("<h2>Post Volumes per Year</h2>", 'post-volume-stats');
		$posts_per_year .= __("<p>".$this->total_volume_of_posts." posts over the past ".$number_of_years." years.</p>", 'post-volume-stats');
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
	public function sdpvs_posts_per_category_list($searchyear = "") {
		parent::sdpvs_post_category_volumes($searchyear);
		$posts_per_category = __("<h2>Post Volumes per Category!</h2>", 'post-volume-stats');
		$c = 0;
		while ($this -> category_array[$c]['id']) {
			if (0 < $this -> category_array[$c]['volume']) {
				$n++;
				$posts_per_category .= "$n <a href='" . admin_url('edit.php?category_name=' . $this -> category_array[$c]['slug']) . "'>{$this->category_array[$c]['name']}</a>: {$this->category_array[$c]['volume']} posts<br>\n";
			}
			$c++;
		}
		return $posts_per_category;
	}

	/*
	 * NUMBER OF POSTS PER TAG TEXT
	 */
	public function sdpvs_posts_per_tag_list($searchyear = "") {
		parent::sdpvs_post_tag_volumes($searchyear);
		$posts_per_tag = __("<h2>Post Volumes per Tag!</h2>", 'post-volume-stats');
		$t = 0;
		while ($this -> tag_array[$t]['id']) {
			if (0 < $this -> tag_array[$t]['volume']) {
				$n++;
				$posts_per_tag .= "$n <a href='" . admin_url('edit.php?tag=' . $this -> tag_array[$t]['slug']) . "'>{$this->tag_array[$t]['name']}</a>: {$this->tag_array[$t]['volume']} posts<br>\n";
			}
			$t++;
		}
		return $posts_per_tag;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK TEXT
	 */
	public function sdpvs_posts_per_dayofweek_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_dayofweek($searchyear);
		parent::find_highest_first_and_total($this->dow_array);
		$posts_per_dow = __("<h2>Post Volumes per Day of the Week</h2>", 'post-volume-stats');
		$posts_per_dow .= "<p>Which day of the week the $this->total_volume_of_posts posts were made on.</p>";
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
	public function sdpvs_posts_per_hour_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_hour($searchyear);
		parent::find_highest_first_and_total($this->hour_array);
		$posts_per_hour = __("<h2>Post Volumes per Hour</h2>", 'post-volume-stats');
		$posts_per_hour .= "<p>Which hour of the day the $this->total_volume_of_posts posts were made on.</p>";
		for ($i = 0; $i <= 23; $i++) {
			$posts_per_hour .= "{$this->hour_array[$i]['title']}: {$this->hour_array[$i]['volume']} posts<br>\n";
		}
		return $posts_per_hour;
	}
	
	/*
	 * NUMBER OF POSTS PER MONTH TEXT
	 */
	public function sdpvs_posts_per_month_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_month($searchyear);
		$posts_per_month = __("<h2>Post Volumes per Month</h2>", 'post-volume-stats');
		for ($i = 0; $i < 12; $i++) {
			if (!$this -> month_array[$i]['volume']) {
				$this -> month_array[$i]['volume'] = 0;
			}
			$posts_per_month .= __("<p>".$this->month_array[$i]['title'].": ".$this->month_array[$i]['volume']." posts</p>", 'post-volume-stats');
		}
		return $posts_per_month;
	}
	
	/*
	 * NUMBER OF POSTS PER DAY OF MONTH TEXT
	 */
	public function sdpvs_posts_per_day_of_month_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_dayofmonth($searchyear);
		$posts_per_dom .= __("<h2>Post Volumes per Day of the Month</h2>", 'post-volume-stats');
		for ($i = 0; $i < 31; $i++) {
			if (!$this -> dom_array[$i]['volume']) {
				$this -> dom_array[$i]['volume'] = 0;
			}
			$posts_per_dom .= "{$this->dom_array[$i]['title']}: {$this->dom_array[$i]['volume']} posts<br>\n";
		}
		return $posts_per_dom;
	}

}
?>
