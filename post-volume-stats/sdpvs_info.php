<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsInfo extends sdpvsArrays {

	/*
	 * WRITE THE INFO
	 */
	public function sdpvs_info() {
		parent::sdpvs_earliest_date_post();
		parent::sdpvs_latest_date_post();
		parent::sdpvs_total_published_volume();
		$currenttime = time();
		
		$earliest = strtotime($this -> earliest_date);
		
		$startyear = date("Y",$earliest);
		$endyear = date("Y",$currenttime);
		
		$diff2 = abs($currenttime - $earliest);
		$betterdays = floor( $diff2 / (60 * 60 * 24));

		$diff = abs(strtotime($this -> latest_date) - strtotime($this -> earliest_date));
		$days = floor($diff / (60 * 60 * 24));

		$ppd = $this -> published_volume / $betterdays;
		$posts_per_day = sprintf("%.3f", $ppd);
		
		$dpp = $betterdays / $this -> published_volume;
		$days_per_post = sprintf("%.3f", $dpp);
		
		$n_o_w = $betterdays / 7;
		$numberofweeks = sprintf("%.1f", $n_o_w);
		
		$ppw = $numberofweeks / $this -> published_volume;
		$weeks_per_post = sprintf("%.3f", $ppw);
		
		$wpp = $this -> published_volume / $numberofweeks;
		$posts_per_week = sprintf("%.1f", $wpp);
		
		$n_o_m = $betterdays / 30;
		$numberofmonths = sprintf("%.1f", $n_o_m);
		
		$ppm = $this -> published_volume / $numberofmonths;
		$posts_per_month = sprintf("%.1f", $ppm);
		
		$n_o_y = $betterdays / 365;
		$numberofyears = sprintf("%.3f", $n_o_y);
		
		$ppy = $this -> published_volume / $numberofyears;
		$posts_per_year = sprintf("%.1f", $ppy);
		
		$numberofcalendaryears = abs($endyear - $startyear + 1);
		
		$ppy2 = $this -> published_volume / $numberofcalendaryears;
		$posts_per_year_2 = sprintf("%.1f", $ppy2);
		
		echo '<h2>' . esc_html__('Summary', 'post-volume-stats') . '</h2>';
		echo '<p>' . sprintf(esc_html__('%d published posts over %d days is %.3f posts per day or a post every %.3f days.', 'post-volume-stats'), $this -> published_volume, $betterdays, $posts_per_day, $days_per_post) . '</p>';
		echo '<p>' . sprintf(esc_html__('That is %.1f weeks, so that would be %.1f posts per week or a blog post every %.3f weeks.', 'post-volume-stats'), $numberofweeks, $posts_per_week, $weeks_per_post) . '</p>';
		echo '<p>' . sprintf(esc_html__('Over roughly %.1f months there are %.1f posts per month.', 'post-volume-stats'), $numberofmonths, $posts_per_month) . '</p>';
		echo '<p>' . sprintf(esc_html__('Taking the number of years as %.3f years, there are %.1f posts per year. Or, from %d to %d is %d years, which would be %.1f posts per year.', 'post-volume-stats'), $numberofyears, $posts_per_year, $startyear, $endyear, $numberofcalendaryears, $posts_per_year_2 ) . '</p>';
		echo '<p><em>' . esc_html__('These stats are from the date of the first post up to today\'s date.', 'post-volume-stats') . '</em></p>';
		
		// Array of WP_User objects.
/*		
		blogusers = get_users( array( 'who'  => 'authors' ) );
		echo '<p>Users: ';
		foreach ( $blogusers as $user ) {
			$userid = abs($user->ID);
			$link = admin_url("edit.php?author=" . $userid);
			echo sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a> ', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $user->display_name);
		}
		echo '</p>';
*/
		
		$link = "https://wordpress.org/plugins/post-volume-stats/";
		$linkdesc = "Post Volume Stats plugin page";
		
		echo '<h2>' . esc_html__('Thank You', 'post-volume-stats') . '</h2>';
		echo '<p>Thank you for installing Post Volume Stats. If you find this free plugin useful please take a moment to give a rating at the ' . sprintf(wp_kses(__('<a href="%1$s" target="_blank">%2$s</a>.', 'post-volume-stats'), array('a' => array('href' => array(), 'target' => array()))), esc_url($link), $linkdesc) . '</p>';
		
		return;
	}

	/*
	 * GET THE NUMBER OF YEARS FOR THE SETTINGS PAGE
	 */
	public function sdpvs_how_many_years_of_posts() {
		parent::sdpvs_number_of_posts_per_year();
		$chart_array = $this -> year_array;
		parent::find_highest_first_and_total($chart_array);
		$bars_total = $this -> first_val;
		return $bars_total;
	}

}
?>
