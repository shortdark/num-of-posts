<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsTextLists extends sdpvsArrays {
	
	/*
	 * NUMBER OF POSTS PER AUTHOR
	 */
	public function sdpvs_posts_per_author_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_author($searchyear,$searchauthor);
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Author', 'post-volume-stats') . '</h2>';
		$i=0;
		while ( array_key_exists($i, $this -> list_array) ) {
			if (!$this -> list_array[$i]['volume']) {
				$this -> list_array[$i]['volume'] = 0;
			}
			$this -> list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '<br />';
			$i++;
		}
		return $this -> list_string;
	}
	
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	public function sdpvs_posts_per_year_list($searchauthor = "") {
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_year($searchauthor);
		parent::find_highest_first_and_total($this -> list_array);
		$number_of_years = $this -> first_val + 1;
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Year', 'post-volume-stats') . '</h2>';
		$this -> list_string .= '<p>' . sprintf(esc_html__('%d posts over the past %d years.', 'post-volume-stats'), $this -> total_volume_of_posts, $number_of_years) . '</p>';
		$i = $this -> first_val;
		while ($this -> list_array[$i]['name']) {
			//if (0 < $this -> list_array[$i]['volume']) {
				$this -> list_string .= "{$this->list_array[$i]['name']}: {$this->list_array[$i]['volume']} posts<br>\n";
			//}
			$i--;
		}
		return $this -> list_string;
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
		$title = "";
		$posts_per_cat_tag = "";
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
		}else{
			$tax_labels = get_taxonomy($type);
			$typetitle = $tax_labels->labels->singular_name;
			$typetitleplural = $tax_labels->label;
			$form_name = 'sdpvs_customselect';
			$taxonomy_type = $type;
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
			$posts_per_cat_tag .= "<input type=\"hidden\" name=\"whichlist\" value=\"$type\">";
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
//			$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' name='csv' class='button-primary' value='" . esc_html__('Export ALL Years to CSV') . "'></div>";
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
				$posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary sdpvs_preview' value='" . esc_html__('Show Preview') . "'></div>";
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
		parent::find_highest_first_and_total($this -> list_array);
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Day of the Week', 'post-volume-stats') . '</h2>';
		$this -> list_string .= "<p>Which day of the week the $this->total_volume_of_posts posts were made on.</p>";
		for ($i = 0; $i <= 6; $i++) {
			if (!$this -> list_array[$i]['volume']) {
				$this -> list_array[$i]['volume'] = 0;
			}
			$this -> list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '</p>';
		}
		return $this -> list_string;
	}

	/*
	 * NUMBER OF POSTS PER HOUR TEXT
	 */
	public function sdpvs_posts_per_hour_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_hour($searchyear,$searchauthor);
		parent::find_highest_first_and_total($this -> list_array);
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Hour', 'post-volume-stats') . '</h2>';
		$this -> list_string .= "<p>Which hour of the day the $this->total_volume_of_posts posts were made on.</p>";
		for ($i = 0; $i <= 23; $i++) {
			$this -> list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '</p>';
		}
		return $this -> list_string;
	}

	/*
	 * NUMBER OF POSTS PER MONTH TEXT
	 */
	public function sdpvs_posts_per_month_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_month($searchyear,$searchauthor);
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Month', 'post-volume-stats') . '</h2>';
		for ($i = 0; $i < 12; $i++) {
			if (!$this -> list_array[$i]['volume']) {
				$this -> list_array[$i]['volume'] = 0;
			}
			$this -> list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '</p>';
		}
		return $this -> list_string;
	}

	/*
	 * NUMBER OF POSTS PER DAY OF MONTH TEXT
	 */
	public function sdpvs_posts_per_day_of_month_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_posts_per_dayofmonth($searchyear,$searchauthor);
		$this -> list_string = '<h2>' . esc_html__('Post Volumes per Day of the Month', 'post-volume-stats') . '</h2>';
		for ($i = 0; $i < 31; $i++) {
			if (!$this -> list_array[$i]['volume']) {
				$this -> list_array[$i]['volume'] = 0;
			}
			$this -> list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '<br />';
		}
		return $this -> list_string;
	}
	
	
	
	/*
	 * NUMBER OF WORDS PER POST
	 */
	public function sdpvs_words_per_post_list($searchyear = "", $searchauthor = "") {
		$searchyear = absint($searchyear);
		$searchauthor = absint($searchauthor);
		parent::sdpvs_number_of_words_per_post($searchyear,$searchauthor);
		$this -> list_string = '<h2>' . esc_html__('Words per Post', 'post-volume-stats') . '</h2>';
		$i=0;
		while ( array_key_exists($i, $this -> list_array) ) {
			if (!$this -> list_array[$i]['volume']) {
				$this -> list_array[$i]['volume'] = 0;
			}
			$this -> list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this -> list_array[$i]['name'], $this -> list_array[$i]['volume']) . '<br />';
			
			$i++;
		}
		return $this -> list_string;
	}
	
	
	/**
	 * COMPILE YEARS MATRIX
	 */
	public function sdpvs_compile_years_matrix($type = "", $firstval="", $searchauthor="") {
		$firstval = absint($firstval);
		parent::sdpvs_number_of_posts_per_year($searchauthor);
		$chart_array = $this -> list_array;
		
		for ($i = $firstval; $i >= 0; $i--) {
			$searchyear = absint($chart_array[$i]['name']);
			if ("hour" == $type) {
				parent::sdpvs_number_of_posts_per_hour($searchyear,$searchauthor);
			} elseif ("dayofweek" == $type) {
				parent::sdpvs_number_of_posts_per_dayofweek($searchyear,$searchauthor);
			} elseif ("month" == $type) {
				parent::sdpvs_number_of_posts_per_month($searchyear,$searchauthor);
			} elseif ("dayofmonth" == $type) {
				parent::sdpvs_number_of_posts_per_dayofmonth($searchyear,$searchauthor);
			} elseif("words" == $type){
				parent::sdpvs_number_of_words_per_post($searchyear,$searchauthor);
			}
			$a=0;
			while ( array_key_exists($a, $this -> list_array) ) {
				if(0 == $i){
					$this -> year_matrix[$a]['label'] = $this -> list_array[$a]['name'];
				}
				$this -> year_matrix[$a][$i] = $this -> list_array[$a]['volume'];
				$a++;
			}
		}
		return;
	}
	
	
	/**
	 * COMPARE YEARS
	 */
	 
	 public function sdpvs_compare_years_rows($type = "", $searchauthor="") {
		$searchauthor = absint($searchauthor);
		$years_total = 0;
		$number_of_years = 0;
		
		// All this just gets the number of years
		parent::sdpvs_number_of_posts_per_year($searchauthor);
		$chart_array = $this -> list_array;
		parent::find_highest_first_and_total($chart_array);
		
		$this -> sdpvs_compile_years_matrix($type, $this->first_val, $searchauthor);
		
		if ("hour" == $type) {
			$this -> output_compare_list .= '<h2>' . esc_html__('Posts per Hour', 'post-volume-stats') . '</h2>';
		} elseif ("dayofweek" == $type) {
			$this -> output_compare_list .= '<h2>' . esc_html__('Posts per Day of the week', 'post-volume-stats') . '</h2>';
		} elseif ("month" == $type) {
			$this -> output_compare_list .= '<h2>' . esc_html__('Posts per Month', 'post-volume-stats') . '</h2>';
		} elseif ("dayofmonth" == $type) {
			$this -> output_compare_list .= '<h2>' . esc_html__('Posts per Day of the Month', 'post-volume-stats') . '</h2>';
		} elseif("words" == $type){
			$this -> output_compare_list .= '<h2>' . esc_html__('Words per Post', 'post-volume-stats') . '</h2>';
		}
		
		$this -> output_compare_list .= "<table>";
		$this -> output_compare_list .= "<tr>";
		$this -> output_compare_list .= "<td>&nbsp;</td>";
		for ($i = $this -> first_val; $i >= 0; $i--) {
			$searchyear = absint($chart_array[$i]['name']);
			$this -> output_compare_list .= "<td><strong>$searchyear</strong></td>";
		}
		$this -> output_compare_list .= "<td><strong>" . esc_html__('Total', 'post-volume-stats') . "</strong></td>";
		$this -> output_compare_list .= "</tr>";
		$a=0;
		while ( array_key_exists($a, $this -> list_array) ) {
			$count_total=0;
			$this -> output_compare_list .= "<tr>";
			$this -> output_compare_list .= '<td nobr>' . sprintf(esc_html__('%s', 'post-volume-stats'), $this -> year_matrix[$a]['label']) . '</td>';
			for ($i = $this -> first_val; $i >= 0; $i--) {
				$this -> output_compare_list .= "<td>{$this->year_matrix[$a][$i]}</td>";
				$count_total += $this->year_matrix[$a][$i];
			}
			$this -> output_compare_list .= "<td>$count_total</td>";
			$this -> output_compare_list .= "</tr>";
			$a++;
		}
		$this -> output_compare_list .= "</table>";
		
		return $this -> output_compare_list;
	}
	 
	 
	public function sdpvs_create_csv_output($type = "", $searchauthor="") {
		$searchauthor = absint($searchauthor);
		$years_total = 0;
		$number_of_years = 0;
		
		// All this just gets the number of years
		parent::sdpvs_number_of_posts_per_year($searchauthor);
		$chart_array = $this -> list_array;
		parent::find_highest_first_and_total($chart_array);
		
		$this -> sdpvs_compile_years_matrix($type, $this->first_val, $searchauthor);
		if("words"==$type){
			$this -> output_compare_list = "Words per Post,";
		}elseif("hour"==$type){
			$this -> output_compare_list = "Hours of the Day,";
		}elseif("dayofweek"==$type){
			$this -> output_compare_list = "Days of the Week,";
		}elseif("month"==$type){
			$this -> output_compare_list = "Months,";
		}elseif("dayofmonth"==$type){
			$this -> output_compare_list = "Days of the Month,";
		}
		
			
		for ($i = $this -> first_val; $i >= 0; $i--) {
			$searchyear = absint($chart_array[$i]['name']);
			$this -> output_compare_list .= "$searchyear,";
		}
		$this -> output_compare_list .= "Total,";
		$this -> output_compare_list .= PHP_EOL;
		$a=0;
		while ( array_key_exists($a, $this -> list_array) ) {
			$count_total=0;

			$this -> output_compare_list .=  sprintf(esc_html__('%s', 'post-volume-stats'), $this -> year_matrix[$a]['label']) . ',';
			for ($i = $this -> first_val; $i >= 0; $i--) {
				$this -> output_compare_list .= "{$this->year_matrix[$a][$i]},";
				$count_total += $this->year_matrix[$a][$i];
			}
			$this -> output_compare_list .= "$count_total,";
			$this -> output_compare_list .= PHP_EOL;
			$a++;
		}
		
		return $this -> output_compare_list;
	}
	
	
	
	
	

}
?>
