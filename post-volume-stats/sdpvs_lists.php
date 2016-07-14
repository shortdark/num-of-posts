<?php

class sdpvs_text_lists {
	/*
	 * NUMBER OF POSTS PER YEAR TEXT
	 */
	function sdpvs_number_of_posts_per_year() {

		$posts_per_year = "<h2>Post Volumes per Year</h2>";
		$posts_per_year .= "<p>Goes back up to 15 years.</p>";

		// get the currrent year
		$currentyear = date('Y');

		// get the number of posts over the past 15 years
		for ($i = 0; $i <= 14; $i++) {
			$searchyear = $currentyear - $i;
			$args = array('post_status ' => 'publish', 'post_type' => 'post', 'date_query' => array('year' => $searchyear, ), );
			$the_query = new WP_Query($args);

			// if there are any posts return the volume for that year...
			if (0 < $the_query -> found_posts) {
				$posts_per_year .= "Number of posts in $searchyear: $the_query->found_posts<br>\n";
			}
		}
		return $posts_per_year;
	}

	/*
	 * NUMBER OF POSTS PER CATEGORY TEXT
	 */
	function sdpvs_post_category_volumes() {
		$posts_per_category = "<h2>Post Volumes per Category!</h2>";
		// get all categories, ordered by name ascending
		$catargs = array('orderby' => 'name', 'order' => 'ASC');
		$catlist = get_categories($catargs);
		// return each one with the category admin URL
		foreach ($catlist as $category) {
			$posts_per_category .= "<a href='" . admin_url('edit.php?category_name=' . $category -> slug) . "'>$category->cat_name</a>: $category->category_count posts<br>\n";
		}
		return $posts_per_category;
	}

	/*
	 * NUMBER OF POSTS PER TAG TEXT
	 */
	function sdpvs_post_tag_volumes() {
		$posts_per_tag = "<h2>Post Volumes per Tag!</h2>";
		// get all categories, ordered by name ascending
		$tagargs = array('orderby' => 'name', 'order' => 'ASC');
		$taglist = get_tags($tagargs);
		// return each one with the category admin URL
		foreach ($taglist as $tag) {
			$posts_per_tag .= "<a href='" . admin_url('edit.php?tag=' . $tag -> slug) . "'>$tag->name</a>: $tag->count posts<br>\n";
		}
		return $posts_per_tag;
	}

}

?>
