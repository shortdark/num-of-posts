<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsTextLists extends sdpvsArrays {
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	public function sdpvs_posts_per_year_list() {
		parent::sdpvs_number_of_posts_per_year();
		parent::find_highest_first_and_total($this -> year_array);
		$number_of_years = $this -> first_val + 1;
		$posts_per_year = '<h2>' . esc_html__('Post Volumes per Year', 'post-volume-stats') . '</h2>';
		$posts_per_year .= '<p>' . sprintf(esc_html__('%d posts over the past %d years.', 'post-volume-stats'), $this -> total_volume_of_posts, $number_of_years) . '</p>';
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
		if(0 < $searchyear){
			$posts_per_category = '<h2>' . sprintf(esc_html__('Post Volumes per Category: %d!', 'post-volume-stats'),$searchyear) . '</h2>';
		}else{
			$posts_per_category = '<h2>' . esc_html__('Post Volumes per Category!', 'post-volume-stats') . '</h2>';
		}
		
		$c = 0;
		while ($this -> category_array[$c]['id']) {
			if (0 < $this -> category_array[$c]['volume']) {
				$n++;
				$link = admin_url('edit.php?category_name=' . $this -> category_array[$c]['slug']);
				$posts_per_category .= sprintf(wp_kses(__('%1$d <a href="%2$s">%3$s</a>: %4$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), $n, esc_url($link), $this -> category_array[$c]['name'], $this -> category_array[$c]['volume']) . '<br />';
			}
			$c++;
		}
		if(0===$c){
			$posts_per_category .= esc_html__('No posts with categories!', 'post-volume-stats') . '<br />';
		}
		return $posts_per_category;
	}

	/*
	 * NUMBER OF POSTS PER TAG TEXT
	 */
	public function sdpvs_posts_per_tag_list($searchyear = "") {
		parent::sdpvs_post_tag_volumes($searchyear);
		if(0 < $searchyear){
			$posts_per_tag = '<h2>' . sprintf(esc_html__('Post Volumes per Tag: %d!', 'post-volume-stats'),$searchyear) . '</h2>';
		}else{
			$posts_per_tag = '<h2>' . esc_html__('Post Volumes per Tag!', 'post-volume-stats') . '</h2>';
		}
		
		$t = 0;
		while ($this -> tag_array[$t]['id']) {
			if (0 < $this -> tag_array[$t]['volume']) {
				$n++;
				$link = admin_url('edit.php?tag=' . $this -> tag_array[$t]['slug']);
				$posts_per_tag .= sprintf(wp_kses(__('%1$d <a href="%2$s">%3$s</a>: %4$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), $n, esc_url($link), $this -> tag_array[$t]['name'], $this -> tag_array[$t]['volume']) . '<br />';
			}
			$t++;
		}
		if(0===$t){
			$posts_per_tag .= esc_html__('No posts with tags!', 'post-volume-stats') . '<br />';
		}
		return $posts_per_tag;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK TEXT
	 */
	public function sdpvs_posts_per_dayofweek_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_dayofweek($searchyear);
		parent::find_highest_first_and_total($this -> dow_array);
		$posts_per_dow = '<h2>' . esc_html__('Post Volumes per Day of the Week', 'post-volume-stats') . '</h2>';
		$posts_per_dow .= "<p>Which day of the week the $this->total_volume_of_posts posts were made on.</p>";
		for ($i = 0; $i <= 6; $i++) {
			if (!$this -> dow_array[$i]['volume']) {
				$this -> dow_array[$i]['volume'] = 0;
			}
			$posts_per_dow .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> dow_array[$i]['title'], $this -> dow_array[$i]['volume']) . '</p>';
		}

		return $posts_per_dow;
	}

	/*
	 * NUMBER OF POSTS PER HOUR TEXT
	 */
	public function sdpvs_posts_per_hour_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_hour($searchyear);
		parent::find_highest_first_and_total($this -> hour_array);
		$posts_per_hour = '<h2>' . esc_html__('Post Volumes per Hour', 'post-volume-stats') . '</h2>';
		$posts_per_hour .= "<p>Which hour of the day the $this->total_volume_of_posts posts were made on.</p>";
		for ($i = 0; $i <= 23; $i++) {
			$posts_per_hour .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> hour_array[$i]['title'], $this -> hour_array[$i]['volume']) . '</p>';
		}
		return $posts_per_hour;
	}

	/*
	 * NUMBER OF POSTS PER MONTH TEXT
	 */
	public function sdpvs_posts_per_month_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_month($searchyear);
		$posts_per_month = '<h2>' . esc_html__('Post Volumes per Month', 'post-volume-stats') . '</h2>';
		for ($i = 0; $i < 12; $i++) {
			if (!$this -> month_array[$i]['volume']) {
				$this -> month_array[$i]['volume'] = 0;
			}
			$posts_per_month .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> month_array[$i]['title'], $this -> month_array[$i]['volume']) . '</p>';
		}
		return $posts_per_month;
	}

	/*
	 * NUMBER OF POSTS PER DAY OF MONTH TEXT
	 */
	public function sdpvs_posts_per_day_of_month_list($searchyear = "") {
		$searchyear = absint($searchyear);
		parent::sdpvs_number_of_posts_per_dayofmonth($searchyear);
		$posts_per_dom .= '<h2>' . esc_html__('Post Volumes per Day of the Month', 'post-volume-stats') . '</h2>';
		for ($i = 0; $i < 31; $i++) {
			if (!$this -> dom_array[$i]['volume']) {
				$this -> dom_array[$i]['volume'] = 0;
			}
			$posts_per_dom .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> dom_array[$i]['title'], $this -> dom_array[$i]['volume']) . '<br />';
		}
		return $posts_per_dom;
	}

}
?>
