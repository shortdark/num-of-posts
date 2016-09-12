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
	 * NUMBER OF POSTS PER CATEGORY / TAG TEXT
	 */
	public function sdpvs_posts_per_cat_tag_list($type, $searchyear = "", $list_type = "admin", $select_array = "") {
		if ("category" == $type) {
			$typetitle = "Category";
			$typetitleplural = "Categories";
			$form_name = 'sdpvs_catselect';
			$taxonomy_type = 'category';
		} elseif ("tag" == $type) {
			$typetitle = "Tag";
			$typetitleplural = "Tags";
			$form_name = 'sdpvs_tagselect';
			$taxonomy_type = 'post_tag';
		}

		if ("subpage" == $list_type) {
			$posts_per_cat_tag = '<h3>' . esc_html__('1. Select', 'post-volume-stats') . '</h3>';
		} elseif ("source" == $list_type) {
			$posts_per_cat_tag = '<h3>' . esc_html__('2. HTML', 'post-volume-stats') . '</h3><p>Copy and paste into text editor</p><code>';
		} elseif ("public" == $list_type) {
			$posts_per_cat_tag = '<h3>' . esc_html__('3. Preview', 'post-volume-stats') . '</h3><p>Copy and paste into visual editor</p>';
		}

		if (0 < $searchyear) {
			$title = sprintf(esc_html__('Post Volumes per %1$s: %2$d!', 'post-volume-stats'), $typetitle, $searchyear);
		} else {
			$title = sprintf(esc_html__('Post Volumes per %s!', 'post-volume-stats'), $typetitle);
		}

		if ("source" == $list_type) {
			$selectable .= '<h2>' . $title . '</h2>';
		} else {
			$posts_per_cat_tag .= '<h2>' . $title . '</h2>';
		}

		if ("" == $select_array and ("admin" == $list_type or "subpage" == $list_type)) {
			// Only grab all data when everything is required
			if ("category" == $type) {
				parent::sdpvs_post_category_volumes($searchyear);
				$universal_array = $this -> category_array;
			} elseif ("tag" == $type) {
				parent::sdpvs_post_tag_volumes($searchyear);
				$universal_array = $this -> tag_array;
			}
			if ("subpage" == $list_type) {
				$posts_per_cat_tag .= '<p>' . sprintf(esc_html__('Check the %s you\'d like to export then click the \'Show HTML\' button.', 'post-volume-stats'), $typetitleplural) . '</p>';
				$posts_per_cat_tag .= "<form class='$form_name' action='' method='POST'>";
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary' value='Show HTML'></div>";
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><a id='select-all'>Select All</a> / <a id='deselect-all'>Deselect All</a></div>";
			}
			$posts_per_cat_tag .= '<ol>';
			$c = 0;
			while ($universal_array[$c]['id']) {
				if (0 < $universal_array[$c]['volume']) {
					if ("category" == $type) {
						$link = admin_url('edit.php?category_name=' . $universal_array[$c]['slug']);
					} elseif ("tag" == $type) {
						$link = admin_url('edit.php?tag=' . $universal_array[$c]['slug']);
					}

					if ("admin" == $list_type) {
						$posts_per_cat_tag .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</li>';
					} elseif ("subpage" == $list_type) {
						$posts_per_cat_tag .= "<li><label><input type=\"checkbox\" name=\"tagid\" value=\"{$universal_array[$c]['id']}\">" . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</label></li>';
					}
				}
				$c++;
			}
			$posts_per_cat_tag .= '</ol>';
			if ("subpage" == $list_type) {
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary' value='Show HTML'></div>";
				$posts_per_cat_tag .= "</form>";
			}
		} else {

			$selectable .= "<ol>";

			$x = 0;
			while ($select_array[1][$x]) {
				if (0 < $select_array[1][$x]) {

					$term_id = $select_array[1][$x];

					// Get slug, name and volume
					$item = parent::sdpvs_get_one_item_info($term_id, $taxonomy_type, $searchyear);

					if ("category" == $type) {
						$link = get_category_link($term_id);
					} elseif ("tag" == $type) {
						$link = get_tag_link($term_id);
					}

					$selectable .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $item['name'], $item['volume']) . '</li>';

				}
				$x++;
			}

			$selectable .= "</ol>";
		}

		if ("source" == $list_type) {
			$selectable = str_replace("<", "&lt;", $selectable);
			$selectable = str_replace(">", "&gt;", $selectable);
			$posts_per_cat_tag .= $selectable . '</code>';
		} elseif ("public" == $list_type) {
			$posts_per_cat_tag .= $selectable;
		}

		if (0 === $c) {
			$posts_per_cat_tag .= sprintf(esc_html__('No posts with %s!', 'post-volume-stats'), $typetitleplural) . '<br />';
		}
		return $posts_per_cat_tag;
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
