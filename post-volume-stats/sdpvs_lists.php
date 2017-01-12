<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsTextLists extends sdpvsArrays {
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	public function sdpvs_posts_per_year_list($searchauthor = "") {
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_year($searchauthor);
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
	 * GET THE COLOR LIST FOR THE LINE GRAPHS
	 */
	public function sdpvs_color_list() {
		$this -> color_list[0] = "#f00";
		$this -> color_list[1] = "#f0f";
		$this -> color_list[2] = "#90f";
		$this -> color_list[3] = "#30f";
		$this -> color_list[4] = "#09f";
		$this -> color_list[5] = "#0ff";
		$this -> color_list[6] = "#0f3";
		$this -> color_list[7] = "#cf0";
		$this -> color_list[8] = "#fc0";
		$this -> color_list[9] = "#f60";
		$this -> color_list[10] = "#000";
		return $this -> color_list;
	}

	/*
	 * NUMBER OF POSTS PER CATEGORY / TAG TEXT
	 */
	public function sdpvs_posts_per_cat_tag_list($type, $searchyear = "", $searchauthor = "", $list_type = "admin", $select_array = "", $colorlist="") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		if ("category" == $type) {
			$typetitle = "Category";
			$typetitleplural = "Categories";
			$form_name = 'sdpvs_catselect';
			$taxonomy_type = 'category';
			$whichlist = 'category';
		} elseif ("tag" == $type) {
			$typetitle = "Tag";
			$typetitleplural = "Tags";
			$form_name = 'sdpvs_tagselect';
			$taxonomy_type = 'post_tag';
			$whichlist = 'tag';
		}else{
			$typetitle = $type;
			$typetitleplural = $type;
			$form_name = 'sdpvs_customselect';
			$taxonomy_type = $type;
			$whichlist = $type;
		}
		$genoptions = get_option('sdpvs_general_settings');
		$listcolors = filter_var ( $genoptions['rainbow'], FILTER_SANITIZE_STRING);

		if ("subpage" == $list_type) {
			// $posts_per_cat_tag = '<h3>' . esc_html__('1. Select', 'post-volume-stats') . '</h3>';
		} elseif ("public" == $list_type) {
			// $posts_per_cat_tag = '<h3>' . esc_html__('2. Preview', 'post-volume-stats') . '</h3><p>' . esc_html__('Copy and paste the list into HTML.') . '</p>';
		} elseif ("buttons" == $list_type) {
			// $posts_per_cat_tag = '<h3>' . esc_html__('3. Export', 'post-volume-stats') . '</h3><p>' . esc_html__('Export the list and line graph into a new post by exporting.') . '</p>';
			$posts_per_cat_tag .= "<form action='" . esc_url(admin_url('admin-post.php')) . "' method='POST'>";
			$posts_per_cat_tag .= "<input type=\"hidden\" name=\"action\" value=\"export_lists\">";
			$posts_per_cat_tag .= "<input type=\"hidden\" name=\"whichlist\" value=\"$whichlist\">";
			// $posts_per_cat_tag .= "<input type=\"hidden\" name=\"howmuch\" value=\"all\">";

			// Make a string for the export button AJAX
			$x = 0;
			while ($select_array[1][$x]) {
				if (0 < $select_array[1][$x]) {
					if (0 != $x) {
						$matches_string .= ",";
					}
					$matches_string .= "[" . $select_array[1][$x] . "]";
				}
				$x++;
			}
			$posts_per_cat_tag .= "<input type=\"hidden\" name=\"matches\" value='$matches_string'>";
			$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' name='all' class='button-primary' value='" . esc_html__('Export All') . "'></div>";
			$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' name='graph' class='button-primary' value='" . esc_html__('Export Graph') . "'></div>";
			$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' name='list' class='button-primary' value='" . esc_html__('Export List') . "'></div>";
			$posts_per_cat_tag .= "</form>";
		}

		if ("buttons" != $list_type and "subpage" != $list_type) {
			if("" != $searchauthor){
				$user = get_user_by( 'id', $searchauthor );
				$extradesc = " $user->display_name";
			}else{
				$extradesc = "";
			}
			if (0 < $searchyear) {
				$title = sprintf(esc_html__('Post Volumes per %1$s: %2$s %3$d!', 'post-volume-stats'), $typetitle, $extradesc, $searchyear);
			} else {
				$title = sprintf(esc_html__('Post Volumes per %s%s!', 'post-volume-stats'), $typetitle, $extradesc);
			}
		}

		if ("source" == $list_type or "export" == $list_type) {
			$selectable = '<h2>' . $title . '</h2>';
		} else {
			$posts_per_cat_tag .= '<h2>' . $title . '</h2>';
		}

		if ("" == $select_array and ("admin" == $list_type or "subpage" == $list_type)) {
			// Only grab all data when everything is required
			parent::sdpvs_post_taxonomy_type_volumes($taxonomy_type, $searchyear, $searchauthor);
			$universal_array = $this -> tax_type_array;
				
			//	var_dump($tax_array_name);
				
			if ("subpage" == $list_type) {
				$posts_per_cat_tag .= '<p>' . sprintf(esc_html__('Check the %s you\'d like to export to a post then click the \'Show Preview\' button. On mobile devices you may have to scroll down as the results may be at the bottom of the page.', 'post-volume-stats'), $typetitleplural) . '</p>';

				$posts_per_cat_tag .= "<form class='$form_name' action='' method='POST'>";
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary sdpvs_preview' value='" . esc_html__('Show Preview') . "'></div>";
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><a id='select-all'>" . esc_html__('Select All') . "</a> / <a id='deselect-all'>" . esc_html__('Deselect All') . "</a></div>";
			}
			$posts_per_cat_tag .= '<ol>';
			$c = 0;
			while (array_key_exists($c, $universal_array)) {
				if (0 < $universal_array[$c]['volume']) {
					if ("category" == $type) {
						$link = admin_url('edit.php?category_name=' . $universal_array[$c]['slug']);
					} elseif ("tag" == $type) {
						$link = admin_url('edit.php?tag=' . $universal_array[$c]['slug']);
					}else{
						$link = admin_url('edit.php?' . $type . '=' . $universal_array[$c]['slug']);
					}

					if ("admin" == $list_type) {
						$posts_per_cat_tag .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array(), 'style' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</li>';
					} elseif ("subpage" == $list_type) {
						$posts_per_cat_tag .= "<li><label><input type=\"checkbox\" name=\"tagid[]\" value=\"{$universal_array[$c]['id']}\">" . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</label></li>';
					}
				}
				$c++;
			}
			$posts_per_cat_tag .= '</ol>';
			if ("subpage" == $list_type) {
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary' value='" . esc_html__('Show HTML') . "'></div>";
				$posts_per_cat_tag .= "</form>";
			}
		} else {

			$selectable .= "<ol>";

			$x = 0;

			while ($select_array[1][$x]) {
				if (0 < $select_array[1][$x]) {
					$term_id = abs($select_array[1][$x]);

					// Get slug, name and volume
					$item = parent::sdpvs_get_one_item_info($term_id, $taxonomy_type, $searchyear,$searchauthor);
					
					$link = get_term_link( $term_id );
					
					if (10 > $x and "off" != $listcolors) {
						$color = $colorlist[$x];
					} else {
						$color = "#000";
					}

					$selectable .= '<li>' . sprintf(wp_kses(__('<a href="%1$s" style="color:%2$s">%3$s</a>: %4$d posts', 'post-volume-stats'), array('a' => array('href' => array(), 'style' => array()))), esc_url($link), $color, $item['name'], $item['volume']) . '</li>';

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
		} elseif ("export" == $list_type) {
			$posts_per_cat_tag = $selectable;
		}

		if (0 === $c) {
			$posts_per_cat_tag .= sprintf(esc_html__('No posts with %s!', 'post-volume-stats'), $typetitleplural) . '<br />';
		}
		return $posts_per_cat_tag;
	}

	/*
	 * NUMBER OF POSTS PER DAY-OF-WEEK TEXT
	 */
	public function sdpvs_posts_per_dayofweek_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_dayofweek($searchyear,$searchauthor);
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
	public function sdpvs_posts_per_hour_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_hour($searchyear,$searchauthor);
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
	public function sdpvs_posts_per_month_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_month($searchyear,$searchauthor);
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
	public function sdpvs_posts_per_day_of_month_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_dayofmonth($searchyear,$searchauthor);
		$posts_per_dom .= '<h2>' . esc_html__('Post Volumes per Day of the Month', 'post-volume-stats') . '</h2>';
		for ($i = 0; $i < 31; $i++) {
			if (!$this -> dom_array[$i]['volume']) {
				$this -> dom_array[$i]['volume'] = 0;
			}
			$posts_per_dom .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> dom_array[$i]['name'], $this -> dom_array[$i]['volume']) . '<br />';
		}
		return $posts_per_dom;
	}
	
	/*
	 * NUMBER OF POSTS PER AUTHOR
	 */
	public function sdpvs_posts_per_author_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_author($searchyear,$searchauthor);
		$posts_per_dom .= '<h2>' . esc_html__('Post Volumes per Author', 'post-volume-stats') . '</h2>';
		$i=0;
		while ( array_key_exists($i, $this -> author_array) ) {
			if (!$this -> author_array[$i]['volume']) {
				$this -> author_array[$i]['volume'] = 0;
			}
			$posts_per_dom .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> author_array[$i]['name'], $this -> author_array[$i]['volume']) . '<br />';
			$i++;
		}
		return $posts_per_dom;
	}

}
?>
