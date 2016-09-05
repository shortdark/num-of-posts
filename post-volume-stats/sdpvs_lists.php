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
		while ($this -> year_array[$i]['name']) {
			if (0 < $this -> year_array[$i]['volume']) {
				$posts_per_year .= "{$this->year_array[$i]['name']}: {$this->year_array[$i]['volume']} posts<br>\n";
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
		if (0 < $searchyear) {
			$posts_per_category = '<h2>' . sprintf(esc_html__('Post Volumes per Category: %d!', 'post-volume-stats'), $searchyear) . '</h2>';
		} else {
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
		if (0 === $c) {
			$posts_per_category .= esc_html__('No posts with categories!', 'post-volume-stats') . '<br />';
		}
		return $posts_per_category;
	}

	/*
	 * NUMBER OF POSTS PER TAG TEXT
	 */
	public function sdpvs_posts_per_tag_list($searchyear = "", $list_type = "admin", $select_array = "") {

		parent::sdpvs_post_tag_volumes($searchyear);
		$test = $this -> tag_array;
		parent::find_highest_first_and_total($test);

		if ("subpage" == $list_type) {
			$posts_per_tag = '<h3>' . esc_html__('1. Select', 'post-volume-stats') . '</h3>';
		} elseif ("source" == $list_type) {
			$posts_per_tag = '<h3>' . esc_html__('2. HTML', 'post-volume-stats') . '</h3><code>';
		} elseif ("public" == $list_type) {
			$posts_per_tag = '<h3>' . esc_html__('3. Preview', 'post-volume-stats') . '</h3>';
		}

		if (0 < $searchyear) {
			$title = sprintf(esc_html__('Post Volumes per Tag: %d!', 'post-volume-stats'), $searchyear);

		} else {
			$title = esc_html__('Post Volumes per Tag!', 'post-volume-stats');
		}

		if ("source" == $list_type) {
			$selectable .= '<h2>' . $title . '</h2>';
		} else {
			$posts_per_tag .= '<h2>' . $title . '</h2>';
		}

		if ("" == $select_array and ("admin" == $list_type or "subpage" == $list_type)) {
			if ("subpage" == $list_type) {
				$posts_per_tag .= '<p>' . esc_html__('Check the tags you\'d like to export then click the \'Show HTML\' button.', 'post-volume-stats') . '</p>';
				$posts_per_tag .= "<form class='sdpvs_tagselect' action='' method='POST'>";
				$posts_per_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary' value='Show HTML'></div>";
				$posts_per_tag .= "<div style='display: block; padding: 5px;'><a id='select-all'>Select All</a> / <a id='deselect-all'>Deselect All</a></div>";
			}
			$posts_per_tag .= '<ol>';
			$t = 0;
			while ($this -> tag_array[$t]['id']) {
				if (0 < $this -> tag_array[$t]['volume']) {
					if ("admin" == $list_type or "subpage" == $list_type) {
						$link = admin_url('edit.php?tag=' . $this -> tag_array[$t]['slug']);
					}
					if ("admin" == $list_type) {
						$posts_per_tag .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $this -> tag_array[$t]['name'], $this -> tag_array[$t]['volume']) . '</li>';
					} elseif ("subpage" == $list_type) {
						$posts_per_tag .= "<li><label><input type=\"checkbox\" name=\"tagid\" value=\"{$this->tag_array[$t]['id']}\">" . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $this -> tag_array[$t]['name'], $this -> tag_array[$t]['volume']) . '</label></li>';
					}
				}
				$t++;
			}
			$posts_per_tag .= '</ol>';
			if ("subpage" == $list_type) {
				$posts_per_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary' value='Show HTML'></div>";
				$posts_per_tag .= "</form>";
			}
		} else {
			$t = 0;
			$selectable .= "<ol>";
			while ($this -> tag_array[$t]['id']) {
				if (0 < $this -> tag_array[$t]['volume']) {

					$x = 0;
					while ($select_array[1][$x]) {
						if ($select_array[1][$x] == $this -> tag_array[$t]['id']) {
							// make all ordered lists and remove the $n
							$n++;
							$percentage_of_total = ($this -> tag_array[$t]['volume'] / $this -> total_volume_of_posts) * 100;
							$percentage_of_total = sprintf("%.1f", $percentage_of_total);

							$link = get_tag_link($this -> tag_array[$t]['id']);

							// $posts_per_tag .= '&lt;li&gt;' . sprintf(wp_kses(__('&lt;a href="%1$s"&gt;%2$s&lt;/a&gt;: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $this -> tag_array[$t]['name'], $this -> tag_array[$t]['volume']) . '&lt;/li&gt;';
							$selectable .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts, %4$s&#37;', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $this -> tag_array[$t]['name'], $this -> tag_array[$t]['volume'], $percentage_of_total) . '</li>';

						}
						$x++;
					}

				}
				$t++;
			}
			$selectable .= "</ol>";
		}

		if (0 === $t) {
			$posts_per_tag .= esc_html__('No posts with tags!', 'post-volume-stats') . '<br />';
		}

		if ("source" == $list_type) {
			$selectable = str_replace("<", "&lt;", $selectable);
			$selectable = str_replace(">", "&gt;", $selectable);
			$posts_per_tag .= $selectable . '</code>';
		} elseif ("public" == $list_type) {
			$posts_per_tag .= $selectable;
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
			$posts_per_dow .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> dow_array[$i]['name'], $this -> dow_array[$i]['volume']) . '</p>';
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
			$posts_per_hour .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> hour_array[$i]['name'], $this -> hour_array[$i]['volume']) . '</p>';
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
			$posts_per_month .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> month_array[$i]['name'], $this -> month_array[$i]['volume']) . '</p>';
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
			$posts_per_dom .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> dom_array[$i]['name'], $this -> dom_array[$i]['volume']) . '<br />';
		}
		return $posts_per_dom;
	}

}
?>
