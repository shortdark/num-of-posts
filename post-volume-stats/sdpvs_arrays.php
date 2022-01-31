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

    /**
     * COMMON FUNCTIONS
     */


    protected function sdpvs_add_date_sql($searchyear, $start_date, $end_date){
        global $wpdb;
        $extra = "";

        if( !empty ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( !empty ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if (!empty($searchyear)) {
            $extra = " AND $wpdb->posts.post_date LIKE '$searchyear%' ";
        }elseif( !empty ($start_date) && !empty ($end_date) ){
            $extra = " AND DATE($wpdb->posts.post_date) >= '$start_date' ";
            $extra .= " AND DATE($wpdb->posts.post_date) <= '$end_date 23:59:59' ";
        }
        return $extra;
    }

    protected function sdpvs_add_author_sql($searchauthor){
        global $wpdb;

        if(!empty($searchauthor) && 0 !== $searchauthor){
            return " AND $wpdb->posts.post_author = $searchauthor ";
        }
        return '';
    }

    protected function sdpvs_add_search_sql($searchtext){
        global $wpdb;

        if(!empty($searchtext)){
            return " AND $wpdb->posts.post_content LIKE '%$searchtext%' ";
        }
        return '';
    }


    /**
     * GET DETAILS FOR ONE CATEGORY / TAG
     */

    protected function sdpvs_get_one_item_info($term_id = 0, $taxonomy_type = "", $searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $term_id = absint($term_id);
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        if (0 < $term_id && "" != $taxonomy_type ) {
            if (0 === $searchyear && 0 === $searchauthor && "" === $start_date) {
                $count = $wpdb->get_var($wpdb->prepare("SELECT count FROM $wpdb->term_taxonomy WHERE taxonomy = %s AND term_id = %d ", $taxonomy_type, $term_id));
                $iteminfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $term_id));
                $one_item_array['name'] = $iteminfo->name;
                $one_item_array['slug'] = $iteminfo->slug;
                $one_item_array['volume'] = $count;
            } else {
                $tax_id = $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = '$taxonomy_type' AND term_id = '$term_id' ");
                $count = $wpdb->get_var("
                    SELECT COUNT(*)
                    FROM $wpdb->posts
                    INNER JOIN $wpdb->term_relationships
                    ON $wpdb->posts.post_status = 'publish'
                    AND $wpdb->posts.post_type = 'post'
                    $extra
                    AND $wpdb->term_relationships.object_id = $wpdb->posts.ID
                    AND $wpdb->term_relationships.term_taxonomy_id = $tax_id
                ");
                $iteminfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $term_id));
                $one_item_array['name'] = $iteminfo->name;
                $one_item_array['slug'] = $iteminfo->slug;
                $one_item_array['volume'] = $count;
            }

            return $one_item_array;
        }
        return [];
    }

    /**
     * NUMBER OF POSTS PER TAXONOMY TYPE (Tags, Categories, Custom)
     */
    protected function sdpvs_post_taxonomy_type_volumes($tax_type = "", $searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $this->tax_type_array = array();
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        if (0 === $searchyear && 0 === $searchauthor && "" === $start_date && "" === $end_date ) {
            // No year, no author, no date range...
            $tax_results = $wpdb->get_results($wpdb->prepare("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY count DESC ", $tax_type));
            $c = 0;
            if($tax_results){
                foreach ($tax_results as $tax_item) {
                    $taxinfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tax_item->term_id));
                    $this->tax_type_array[$c]['id'] = $tax_item->term_id;
                    $this->tax_type_array[$c]['name'] = $taxinfo->name;
                    $this->tax_type_array[$c]['slug'] = $taxinfo->slug;
                    $this->tax_type_array[$c]['volume'] = $tax_item->count;
                    $this->tax_type_array[$c]['angle'] = 0;
                    $c++;
                }
            }

        } else {
            $tax_results = $wpdb->get_results($wpdb->prepare("SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY term_id DESC ", $tax_type));
            $c = 0;
            $highestval = 0;
            if ($tax_results) {
                foreach ($tax_results as $tax_item) {
                    $posts = $wpdb->get_var("
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
                        $cat_array[$c]['id'] = $tax_item->term_id;
                        $cat_array[$c]['volume'] = $posts;
                        if ($highestval < $posts) {
                            $highestval = $posts;
                        }
                        $c++;
                    }
                }
            }
            $d = 0;
            for ($i = $highestval; $i > 0; $i--) {
                $c = 0;
                while (array_key_exists($c, $cat_array)) {
                    if ($i == $cat_array[$c]['volume'] && 0 < $cat_array[$c]['id']) {
                        $temp = $cat_array[$c]['id'];
                        $taxinfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $temp));
                        $this->tax_type_array[$d]['id'] = $temp;
                        $this->tax_type_array[$d]['name'] = $taxinfo->name;
                        $this->tax_type_array[$d]['slug'] = $taxinfo->slug;
                        $this->tax_type_array[$d]['volume'] = absint( $cat_array[$c]['volume'] );
                        $this->tax_type_array[$d]['angle'] = 0;
                        $d++;
                    }
                    $c++;
                }
            }
        }
        $wpdb->flush();
    }



    /**
     * NUMBER OF POSTS PER TAXONOMY TYPE (Tags, Categories, Custom)
     * ---> STRUCTURED FOR CSV EXPORT !!!
     */
    protected function sdpvs_post_tax_type_vols_structured($tax_type = "", $searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $this->list_array = array();
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        $tax_results = $wpdb->get_results($wpdb->prepare("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY count DESC ", $tax_type));
        $c = 0;
        foreach ($tax_results as $tax_item) {
            $taxinfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tax_item->term_id));
            $this->list_array[$c]['id'] = $tax_item->term_id;
            $this->list_array[$c]['name'] = $taxinfo->name;
            $this->list_array[$c]['slug'] = $taxinfo->slug;
            $this->list_array[$c]['volume'] = 0;
            $c++;
        }

        if ($searchyear || $searchauthor) {
            $tax_results = $wpdb->get_results($wpdb->prepare("SELECT term_id, term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY term_id DESC ", $tax_type));

            foreach ($tax_results as $tax_item) {
                $volume = $wpdb->get_var("
                    SELECT COUNT(*)
                    FROM $wpdb->posts
                    INNER JOIN $wpdb->term_relationships
                    ON $wpdb->posts.post_status = 'publish'
                    AND $wpdb->posts.post_type = 'post'
                    $extra
                    AND $wpdb->term_relationships.object_id = $wpdb->posts.ID
                    AND $wpdb->term_relationships.term_taxonomy_id = $tax_item->term_taxonomy_id
                ");
                if (0 < $volume) {
                    $c = 0;
                    while (!empty($this->list_array[$c]['id'])) {
                        if ($tax_item->term_id == $this->list_array[$c]['id']) {
                            $this->list_array[$c]['volume'] = absint( $volume );
                        }
                        $c++;
                    }
                }
            }
        }else{
            $tax_results = $wpdb->get_results($wpdb->prepare("SELECT term_id,count FROM $wpdb->term_taxonomy WHERE taxonomy = %s ORDER BY count DESC ", $tax_type));
            $c = 0;
            foreach ($tax_results as $tax_item) {
                $taxinfo = $wpdb->get_row($wpdb->prepare("SELECT name,slug FROM $wpdb->terms WHERE term_id = %d ", $tax_item->term_id));
                $this->list_array[$c]['volume'] = absint ( $taxinfo->count );
                $c++;
            }
        }

        $wpdb->flush();
    }


    /**
     * NUMBER OF POSTS IN ORDER, FOR "DAYS BETWEEN POSTS"
     */
    protected function sdpvs_number_of_posts_in_order($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="" ) {
        global $wpdb;

        $previous_date="";
        $max_interval = 30;
        $genoptions = get_option('sdpvs_general_settings');
        if (false !== $genoptions) {
            $max_interval = absint ( $genoptions['maxinterval'] );
        }

        $searchauthor = absint($searchauthor);
        $this->list_array = array();
        $test_array = array();

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        $found_posts = $wpdb->get_results("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ORDER BY post_date ASC ");
            foreach ($found_posts as $ordered_post) {
                if(""==$previous_date){
                    $previous_date = substr($ordered_post->post_date, 0, 10);
                }else{
                    $current_date = substr($ordered_post->post_date, 0, 10);
                    $previous = new DateTime($previous_date);
                    $current = new DateTime($current_date);
                    $interval = $current->diff($previous);
                    $i = absint( $interval->format('%a') );
                    if($i >= $max_interval){
                        $test_array[$max_interval]['name'] = $max_interval;
                        if( isset($test_array[$max_interval]['volume']) ){
                            $test_array[$max_interval]['volume'] ++;
                        }else{
                            $test_array[$max_interval]['volume'] = 1;
                        }

                    }else{
                        $test_array[$i]['name'] = $i;
                        if( isset($test_array[$i]['volume']) ){
                            $test_array[$i]['volume'] ++;
                        }else{
                            $test_array[$i]['volume'] = 1;
                        }

                    }
                    $previous_date = $current_date;
                }
            }
            $wpdb->flush();
            for($j=0;$j<=$max_interval;$j++) {
                    $this->list_array[$j]['name'] = "$j days";
                    $this->list_array[$j]['volume'] = 0;
                    if($j == $max_interval){
                        $this->list_array[$j]['name'] = "$j+ days";
                    }
                    for ($k=0; $k <= $max_interval; $k++){
                        if(isset($test_array[$k]['name']) && $j == $test_array[$k]['name'] && 0 < $test_array[$k]['volume']) {
                            $this->list_array[$j]['volume'] = $test_array[$k]['volume'];
                        }
                    }
            }
    }





    /**
     * NUMBER OF POSTS PER YEAR
     */
    protected function sdpvs_number_of_posts_per_year($searchauthor, $searchtext) {
        global $wpdb;

        $searchauthor = absint($searchauthor);
        $this->list_array = array();

        $currentyear = date('Y');

        $extra = $this->sdpvs_add_author_sql($searchauthor);
        if('' != $searchtext){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        for ($i = 0; $i <= 30; $i++) {
            $searchyear = $currentyear - $i;
            $found_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '{$searchyear}%' {$extra} ");
            if (0 > $found_posts || !$found_posts || "" == $found_posts) {
                $found_posts = 0;
            }
            $this->list_array[$i]['name'] = $searchyear;
            $this->list_array[$i]['volume'] = absint ( $found_posts );
            $wpdb->flush();
        }
    }

    /**
     * NUMBER OF POSTS PER DAY-OF-WEEK
     */
    protected function sdpvs_number_of_posts_per_dayofweek($searchyear, $searchauthor, $start_date, $end_date, $searchtext) {
        global $wpdb;

        $startweek = 'sunday';
        $genoptions = get_option('sdpvs_general_settings');
        if (false !== $genoptions) {
            $startweek = htmlspecialchars( $genoptions['startweekon'], ENT_QUOTES);
        }

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();

        if('sunday' === $startweek){
            $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
            for ($w = 0; $w <= 6; $w++) {
                $this->list_array[$w]['name'] = $days_of_week[$w];
                $this->list_array[$w]['volume'] = 0;
            }
            $weekletter = "w";
        }else{
            $days_of_week = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday","Sunday");
            for ($w = 0; $w <= 6; $w++) {
                $this->list_array[$w]['name'] = $days_of_week[$w];
                $this->list_array[$w]['volume'] = 0;
            }
            $weekletter = "N";
        }


        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }
        $sql = "SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' {$extra} ";

        //$myblogitems = $wpdb->get_var($wpdb->prepare("SELECT post_date FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ", ''));
        $myblogitems = $wpdb->get_results( $sql );
        foreach ($myblogitems as $dowpost) {
            $year = substr($dowpost->post_date, 0, 4);
            $month = substr($dowpost->post_date, 5, 2);
            $day = substr($dowpost->post_date, 8, 2);
            $tempdate = mktime(0, 0, 0, $month, $day, $year);
            $d = date($weekletter, $tempdate);
            if("w" === $weekletter){
                $this->list_array[$d]['volume']++;
            }else{
                $g = $d-1;
                $this->list_array[$g]['volume']++;
            }

        }
        $wpdb->flush();
    }

    /**
     * NUMBER OF POSTS PER HOUR
     */
    protected function sdpvs_number_of_posts_per_hour($searchyear=0, $searchauthor=0, $start_date='', $end_date='', $searchtext='') {
        global $wpdb;

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();

        for ($i = 0; $i <= 23; $i++) {
            $searchhour = sprintf("%02s", $i);


            $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
            $extra .= $this->sdpvs_add_author_sql($searchauthor);
            if('' != $searchtext){
                $extra .= $this->sdpvs_add_search_sql($searchtext);
            }
            $sql = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '% $searchhour:%' $extra ";

            $found_posts = $wpdb->get_var($sql);
            if (0 > $found_posts || !$found_posts || "" == $found_posts) {
                $found_posts = 0;
            }
            $j = sprintf("%02s", $i);
            $this->list_array[$i]['name'] = "$j:00-$j:59";
            $this->list_array[$i]['volume'] = $found_posts;
        }
        $wpdb->flush();
    }

    /**
     * NUMBER OF POSTS PER MONTH
     */
    protected function sdpvs_number_of_posts_per_month($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $months_of_year = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        for ($w = 0; $w < 12; $w++) {
            $this->list_array[$w]['name'] = $months_of_year[$w];
            $this->list_array[$w]['volume'] = 0;
        }
        for ($i = 0; $i < 12; $i++) {
            $j = $i + 1;
            $searchmonth = sprintf("%02s", $j);
            $found_posts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchmonth-%' $extra ");
            if (0 > $found_posts || !$found_posts || "" == $found_posts) {
                $found_posts = 0;
            }
            $this->list_array[$i]['volume'] = $found_posts;
        }
        $wpdb->flush();
    }

    /**
     * NUMBER OF POSTS PER DAY-OF-THE-MONTH
     */
    protected function sdpvs_number_of_posts_per_dayofmonth($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        $extra .= $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        for ($i = 0; $i < 31; $i++) {
            $j = $i + 1;
            $searchday = sprintf("%02s", $j);

            $found_posts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_date LIKE '%-$searchday %' $extra ");

            if (0 > $found_posts || !$found_posts || "" == $found_posts) {
                $found_posts = 0;
            }
            $this->list_array[$i]['name'] = $searchday;
            $this->list_array[$i]['volume'] = $found_posts;
        }
        $wpdb->flush();
    }

    /**
     * NUMBER OF POSTS PER AUTHOR
     */
    protected function sdpvs_number_of_posts_per_author($searchyear = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;
        $this->list_array = array();

        $args = array(
            'orderby'    => 'post_count',
            'order'      => 'DESC',
            'capability' => array( 'edit_posts' ),
        );

        // Capability queries were only introduced in WP 5.9.
        if ( version_compare( $GLOBALS['wp_version'], '5.9-alpha', '<' ) ) {
            $args['who'] = 'authors';
            unset( $args['capability'] );
        }

        $blogusers = get_users( $args );

        $extra = $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);
        if(!empty($searchtext)){
            $extra .= $this->sdpvs_add_search_sql($searchtext);
        }

        // Array of WP_User objects.
        $a=0;
        foreach ( $blogusers as $user ) {
            $post_author = absint($user->ID);
            $found_posts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_author = '$post_author' $extra ");
            if(1 <= $found_posts){
                $this->list_array[$a]['id'] = absint($user->ID);
                $this->list_array[$a]['name'] = $user->display_name;
                $this->list_array[$a]['volume'] = $found_posts;
                $a++;
            }
        }
        $wpdb->flush();

        function sortByOrder($j, $k) {
            return $k['volume'] - $j['volume'];
        }

        if(1 < $a){
            usort($this->list_array, 'sortByOrder');
        }
    }

    /**
     * NUMBER OF WORDS PER POST
     */
    protected function sdpvs_number_of_words_per_post($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $extra = "";

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();
        $chart_iterations = 20;
        $maxvalue = 0;

        $extra = $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra = $this->sdpvs_add_search_sql($searchtext);
        }

        // Get the number of posts for all the years for "compare years"
        $total_posts = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
        if ($total_posts) {
            foreach ($total_posts as $post_item) {
                $temp_content = htmlspecialchars ( $post_item->post_content, ENT_QUOTES );
                $word_count = str_word_count( strip_tags( $temp_content ), 0, '123456789&;#' );

                // If no date is specified use this data as the output
                if( 0 == $searchyear && "" == $start_date && "" == $end_date ) {
                    $temp_array[] = $word_count;
                }

                if($maxvalue < $word_count){
                    $maxvalue = $word_count;
                }
            }
            $wpdb->flush();
            if(0 < $maxvalue){
                $vol_per_bar = ceil($maxvalue / $chart_iterations);
                // Make the iterations even, i.e. multiples of 10
                if(0!= $vol_per_bar % 10){
                    while(0!= $vol_per_bar % 10){
                        $vol_per_bar++;
                    }
                }
            }
            // Try to remove empty fields at end
            $chart_max_value = $vol_per_bar * $chart_iterations;
            while($chart_max_value - $vol_per_bar > $maxvalue){
                $chart_iterations--;
                $chart_max_value = $vol_per_bar * $chart_iterations;
            }
            // Now, we can specify the range of words for each bar
            for($h=0;$h<$chart_iterations;$h++){
                $lower = $h * $vol_per_bar;
                //if( $chart_iterations > $h ){
                    $upper = ($h * $vol_per_bar) + $vol_per_bar-1;
                    $this->list_array[$h]['name'] = "$lower - $upper words";
                //}
                $this->list_array[$h]['volume'] = 0;
            }
        }

        // If a date has been specified we run another SQL query for that date
        if( 0 < $searchyear || ("" != $start_date && "" != $end_date) ){
            $temp_array=array();

            $extra .= $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);

            $found_posts = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
            if ($found_posts) {
                foreach ($found_posts as $post_item) {
                    $temp_content = htmlspecialchars( $post_item->post_content , ENT_QUOTES );
                    $word_count = str_word_count( strip_tags( $temp_content ), 0, '123456789&;#' );
                    $temp_array[] = $word_count;
                }
            }
        }
        $wpdb->flush();

        // Populate the output array
        if(isset($temp_array)){
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
                $this->list_array[$i]['volume'] ++;
            }
        }
    }


    /**
     * Counts the images in the post content
     *
     * @param string $post_content
     * @return int
     */
    protected function count_images_from_sql($post_content=""){
        $image_count=0;
        if(""!=$post_content){
            $pattern="/(<img [^>]*>)/i";
            preg_match_all($pattern, $post_content, $out, PREG_SET_ORDER);
            $a=0;
            $image_count=0;
            while(isset($out[$a][0])){
                $image_count++;
                $a++;
            }
            // if there is a gallery count the ids in the gallery
            $pattern2="/\[gallery[a-z=\" ]*ids=\"([0-9,]*)\"/i";
            preg_match_all($pattern2, $post_content, $out2, PREG_SET_ORDER);
            $b=0;
            while(isset($out2[$b][0])){
                $ids = explode(',',$out2[$b][0]);
                $image_count += count($ids);
                $b++;
            }
        }
        return $image_count;
    }


    /**
    * NUMBER OF IMAGES PER POST
    */
    protected function sdpvs_number_of_images_per_post($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $extra = "";

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();
        $maxvalue = 0;

        $extra = $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra = $this->sdpvs_add_search_sql($searchtext);
        }

        // If a date range is specified, the main purpose of the $total_posts SQL is to get the $maxvalue
        // across all the years for "compare years"
        $total_posts = $wpdb->get_results(" SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
        if ($total_posts) {
            foreach ($total_posts as $post_item) {
                // find the number of images in the post
                $image_count = $this->count_images_from_sql($post_item->post_content);
                $temp_array[] = $image_count;
                if($maxvalue < $image_count){
                    $maxvalue = $image_count;
                }
            }

            for($h=0;$h<=$maxvalue;$h++){
                $this->list_array[$h]['name'] = "$h images";
                $this->list_array[$h]['volume'] = 0;
            }

            if(0 < $searchyear || ("" != $start_date && "" != $end_date) ){
                // If a date is specified we do another SQL search for the particular date range specified
                $wpdb->flush();
                if (0 < $searchyear) {
                    $extra .= " AND $wpdb->posts.post_date LIKE '$searchyear%' ";
                }elseif("" != $start_date and "" != $end_date ){
                    $extra .= " AND $wpdb->posts.post_date >= '$start_date' ";
                    $extra .= " AND $wpdb->posts.post_date <= '$end_date' ";
                }
                $extra .= $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);

                $found_posts = $wpdb->get_results(" SELECT post_content FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' $extra ");
                if($found_posts){
                    foreach($found_posts as $post_item){
                        // find the number of images in the post
                        $image_count = $this->count_images_from_sql($post_item->post_content);
                        $this->list_array[$image_count]['volume'] ++;
                    }
                }
            }else{
                // No date specified, use the all-time data we gathered at the start in $total_posts
                foreach ($total_posts as $post_item) {
                    // find the number of images in the post
                    $image_count = $this->count_images_from_sql($post_item->post_content);
                    $this->list_array[$image_count]['volume'] ++;
                }
            }
        }
        $wpdb->flush();
    }


    /**
     * NUMBER OF COMMENTS PER POST
     */
    protected function sdpvs_number_of_comments_per_post($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $searchtext="") {
        global $wpdb;

        $extra = "";

        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $this->list_array = array();
        $maxvalue = 0;

        $extra = $this->sdpvs_add_author_sql($searchauthor);
        if(!empty($searchtext)){
            $extra = $this->sdpvs_add_search_sql($searchtext);
        }

        // If a date range is specified, the main purpose of the $total_posts SQL is to get the $maxvalue
        // across all the years for "compare years"
        $total_posts = $wpdb->get_results(" SELECT $wpdb->posts.ID, COUNT($wpdb->comments.comment_post_ID) AS 'comment_count' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID where $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' $extra GROUP BY $wpdb->posts.ID ");
        if ($total_posts) {
            foreach ($total_posts as $post_item) {
                $comment_count = $post_item->comment_count;
                if(0 == $searchyear && "" == $start_date && "" == $end_date) {
                    // We make $temp_array here
                    // If a date has NOT been specified we will NOT run another SQL query later on
                    $temp_array[] = $comment_count;
                }
                if($maxvalue < $comment_count){
                    $maxvalue = $comment_count;
                }
            }

            for($h=0;$h<=$maxvalue;$h++){
                $this->list_array[$h]['name'] = "$h comments";
                $this->list_array[$h]['volume'] = 0;
            }

            if(0 < $searchyear || ("" != $start_date && "" != $end_date) ){
                // If a date is specified we do another SQL search for the particular date range specified
                $wpdb->flush();

                $extra .= $this->sdpvs_add_date_sql($searchyear, $start_date, $end_date);

                $found_posts = $wpdb->get_results(" SELECT $wpdb->posts.ID, COUNT($wpdb->comments.comment_post_ID) AS 'comment_count' FROM $wpdb->posts LEFT JOIN $wpdb->comments ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID where $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' $extra GROUP BY $wpdb->posts.ID ");
                if($found_posts){
                    foreach($found_posts as $post_item){
                        $comment_count = $post_item->comment_count;
                        $this->list_array[$comment_count]['volume'] ++;
                    }
                }
            }else{
                // No date specified, use the all-time data we gathered at the start in $total_posts
                foreach ($total_posts as $post_item) {
                    $comment_count = $post_item->comment_count;
                    $this->list_array[$comment_count]['volume'] ++;
                }
            }
        }
        $wpdb->flush();
    }



    /**
     * FIND THE POST WITH THE EARLIEST DATE
     */
    protected function sdpvs_earliest_date_post() {
        global $wpdb;
        $this->earliest_date = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date ASC LIMIT 1 ");
        $wpdb->flush();
    }

    /**
     * FIND THE POST WITH THE LATEST DATE
     */
    protected function sdpvs_latest_date_post() {
        global $wpdb;
        $this->latest_date = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 1 ");
        $wpdb->flush();
    }

    /**
     * FIND THE TOTAL VOLUME OF POSTS
     */
    protected function sdpvs_total_published_volume() {
        global $wpdb;
        $this->published_volume = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ");
        $wpdb->flush();
    }

    /**
     * FIND HIGHEST, FIRST AND TOTAL VOLUME VALUES
     */
    protected function find_highest_first_and_total($testarray = array()) {
        $this->highest_val = 0;
        $this->first_val = 0;
        $this->total_volume_of_posts = 0;
        $this->total_bars = 0;
        $i = 0;
        while (array_key_exists($i, $testarray)) {
            $this->total_volume_of_posts += $testarray[$i]['volume'];
            if (0 < $testarray[$i]['volume'] && $this->highest_val < $testarray[$i]['volume']) {
                $this->highest_val = $testarray[$i]['volume'];
            }
            if (0 < $testarray[$i]['volume']) {
                $this->first_val = $i;
            }
            $this->total_bars ++;
            $i++;
        }
    }

}

