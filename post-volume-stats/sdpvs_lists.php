<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsTextLists extends sdpvsArrays {

    public function add_containing_text_search ($search_text='')
    {
        if ('' != $search_text) {
            return sprintf(esc_html__(' for posts containing "%s"', 'post-volume-stats'), $search_text);
        }
        return '';
    }

    /**
     * NUMBER OF POSTS PER AUTHOR
     */
    public function sdpvs_posts_per_author_list($searchyear = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if(0 < $searchyear){
            $label = " $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = " ($start_date to $end_date)";
        }

        $this->sdpvs_number_of_posts_per_author($searchyear, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>';
        $this->list_string .= sprintf(esc_html__('Post Volumes per Author%1$s', 'post-volume-stats'), $label);
        $this->list_string .= $this->add_containing_text_search($search_text);
        $this->list_string .= '</h2>';
        $i=0;
        while ( array_key_exists($i, $this->list_array) ) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '<br />';
            $i++;
        }
        return $this->list_string;
    }

    /**
     * NUMBER OF POSTS PER YEAR TEXT
     */
    public function sdpvs_posts_per_year_list($searchauthor = 0, $search_text = "") {
        $searchauthor = absint($searchauthor);
        $this->sdpvs_number_of_posts_per_year($searchauthor, $search_text);
        $this->find_highest_first_and_total($this->list_array);
        $number_of_years = $this->first_val + 1;
        $extra = $this->add_containing_text_search($search_text);
        $this->list_string = '<h2>' . esc_html__('Post Volumes per Year', 'post-volume-stats') . $extra . '</h2>';

        $this->list_string .= '<p>' . sprintf(esc_html__('%d posts over the past %d years.', 'post-volume-stats'), $this->total_volume_of_posts, $number_of_years) . '</p>';
        $i = $this->first_val;
        while (!empty($this->list_array[$i]['name'])) {
            $this->list_string .= "{$this->list_array[$i]['name']}: {$this->list_array[$i]['volume']} posts<br>\n";
            $i--;
        }
        return $this->list_string;
    }

    /**
     * GET THE COLOR LIST FOR THE LINE GRAPHS
     */
    public function sdpvs_color_list() {
        $this->color_list[0] = "#f00";
        $this->color_list[1] = "#f0f";
        $this->color_list[2] = "#90f";
        $this->color_list[3] = "#30f";
        $this->color_list[4] = "#09f";
        $this->color_list[5] = "#0ff";
        $this->color_list[6] = "#0f3";
        $this->color_list[7] = "#cf0";
        $this->color_list[8] = "#fc0";
        $this->color_list[9] = "#f60";
        $this->color_list[10] = "#000";
        return $this->color_list;
    }

    /**
     * NUMBER OF POSTS PER CATEGORY / TAG TEXT
     */
    public function sdpvs_posts_per_cat_tag_list($type, $searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $list_type = "admin", $select_array = [], $colorlist=[], $search_text="") {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        $selectable='';
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if(0 < $searchyear){
            $label = " in $searchyear";
        }elseif("subpage" != $list_type && "public" != $list_type && "buttons" != $list_type && "export" != $list_type ){
            if(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
                $label = " ($start_date to $end_date)";
            }
        }
        if(!empty($search_text)){
            $label .= " (with \"$search_text\")";
        }
        $title = "";
        $posts_per_cat_tag = "";
        if ("category" === $type) {
            $typetitle = "Category";
            $typetitleplural = "Categories";
            $form_name = 'sdpvs_catselect';
            $taxonomy_type = 'category';
        } elseif ("tag" === $type) {
            $typetitle = "Tag";
            $typetitleplural = "Tags";
            $form_name = 'sdpvs_tagselect';
            $taxonomy_type = 'post_tag';
        }else{
            $tax_labels = get_taxonomy($type);
            $typetitle = '';
            $typetitleplural = '';
            if (!empty($tax_labels)) {
                $typetitle = $tax_labels->labels->singular_name;
                $typetitleplural = $tax_labels->label;
            }
            $form_name = 'sdpvs_customselect';
            $taxonomy_type = $type;
        }
        if("tag" != $type && "category" != $type && "export" != $list_type){
            $logical_starter = 1;
        }else{
            $logical_starter = 0;
        }

        $listcolors = 'on';
        $genoptions = get_option('sdpvs_general_settings');
        if (false !== $genoptions) {
            $listcolors = htmlspecialchars ( $genoptions['rainbow'], ENT_QUOTES);
        }


       if ("buttons" === $list_type) {
            $posts_per_cat_tag .= "<form action='" . esc_url(admin_url('admin-post.php')) . "' method='POST'>";
            $posts_per_cat_tag .= "<input type=\"hidden\" name=\"action\" value=\"export_lists\">";
            $posts_per_cat_tag .= "<input type=\"hidden\" name=\"whichlist\" value=\"$type\">";
            if("category" !== $type && "tag" !== $type){
                $posts_per_cat_tag .= "<input type=\"hidden\" name=\"customname\" value=\"$type\">";
            }

            // Make a string for the export button AJAX
            $x = $logical_starter;
            $matches_string='';
            while (!empty($select_array[1][$x])) {
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

        if ("buttons" != $list_type && "subpage" != $list_type) {
            if(0 != $searchauthor){
                $user = get_user_by( 'id', $searchauthor );
                $extradesc = " by $user->display_name";
            }else{
                $extradesc = "";
            }
            if ( isset($label) ) {
                $title = sprintf(esc_html__('Post Volumes per %1$s%2$s%3$s!', 'post-volume-stats'), $typetitle, $extradesc, $label);
            } else {
                $title = sprintf(esc_html__('Post Volumes per %s%s!', 'post-volume-stats'), $typetitle, $extradesc);
            }
        }

        if ("source" === $list_type || "export" === $list_type) {
            $selectable = '<h2>' . $title . '</h2>';
        } else {
            $posts_per_cat_tag .= '<h2>' . $title . '</h2>';
        }

        if (empty($select_array) && ("admin" === $list_type || "subpage" === $list_type)) {
            // Only grab all data when everything is required
            if("admin" === $list_type){
                $this->sdpvs_post_taxonomy_type_volumes($taxonomy_type, $searchyear, $searchauthor, $start_date, $end_date, $search_text);
            }elseif("subpage" === $list_type){
                $this->sdpvs_post_taxonomy_type_volumes($taxonomy_type, $searchyear, $searchauthor);
            }
            
            $universal_array = $this->tax_type_array;
            if ("subpage" === $list_type) {
                $posts_per_cat_tag .= '<p>' . sprintf(esc_html__('Check the %s you\'d like to export to a post then click the \'Show Preview\' button. On mobile devices you may have to scroll down as the results may be at the bottom of the page.', 'post-volume-stats'), $typetitleplural) . '</p>';

                $posts_per_cat_tag .= "<form class='$form_name' action='' method='POST'>";
                if("category" != $type && "tag" != $type){
                    $posts_per_cat_tag .= "<input type=\"hidden\" name=\"customname\" value=\"$type\">";
                }
                $posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary sdpvs_preview' value='" . esc_html__('Show Preview') . "'></div>";
                $posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><a id='select-all'>" . esc_html__('Select All') . "</a> / <a id='deselect-all'>" . esc_html__('Deselect All') . "</a></div>";
            }
            $posts_per_cat_tag .= '<ol>';
            $c = 0;
            while (array_key_exists($c, $universal_array)) {
                if (0 < $universal_array[$c]['volume']) {
                    if ("category" === $type) {
                        $link = admin_url('edit.php?category_name=' . $universal_array[$c]['slug']);
                    } elseif ("tag" === $type) {
                        $link = admin_url('edit.php?tag=' . $universal_array[$c]['slug']);
                    }else{
                        $link = admin_url('edit.php?' . $type . '=' . $universal_array[$c]['slug']);
                    }

                    if ("admin" === $list_type) {
                        $posts_per_cat_tag .= '<li>' . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array(), 'style' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</li>';
                    } elseif ("subpage" === $list_type) {
                        $posts_per_cat_tag .= "<li><label><input type=\"checkbox\" name=\"tagid[]\" value=\"{$universal_array[$c]['id']}\">" . sprintf(wp_kses(__('<a href="%1$s">%2$s</a>: %3$d posts', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($link), $universal_array[$c]['name'], $universal_array[$c]['volume']) . '</label></li>';
                    }
                }
                $c++;
            }
            $posts_per_cat_tag .= '</ol>';
            if ("subpage" === $list_type) {
                $posts_per_cat_tag .= "<div style='display: block; padding: 5px;'><input type='submit' class='button-primary sdpvs_preview' value='" . esc_html__('Show Preview') . "'></div>";
                $posts_per_cat_tag .= "</form>";
            }
        } else {

            $selectable .= "<ol>";

            $x = $logical_starter;
            $y = 0;
            $c = 0;
            while (!empty($select_array[1][$x])) {
                if (0 < $select_array[1][$x]) {
                    $term_id = abs($select_array[1][$x]);

                    // Get slug, name and volume
                    $item = $this->sdpvs_get_one_item_info($term_id, $taxonomy_type, $searchyear, $searchauthor, $start_date, $end_date, $search_text);

                    $link = get_term_link( $term_id );

                    $color = "#000";
                    if (10 > $y && "off" != $listcolors && !empty($colorlist[$y])) {
                        $color = $colorlist[$y];
                    }

                    $selectable .= '<li>' . sprintf(wp_kses(__('<a href="%1$s" style="color:%2$s">%3$s</a>: %4$d posts', 'post-volume-stats'), array('a' => array('href' => array(), 'style' => array()))), esc_url($link), $color, $item['name'], $item['volume']) . '</li>';

                }
                $x++;
                $y++;
                $c++;
            }

            $selectable .= "</ol>";
        }

        if ("source" === $list_type) {
            $selectable = str_replace(array("<", ">"), array("&lt;", "&gt;"), $selectable);
            $posts_per_cat_tag .= $selectable . '</code>';
        } elseif ("public" === $list_type) {
            $posts_per_cat_tag .= $selectable;
        } elseif ("export" === $list_type) {
            $posts_per_cat_tag = $selectable;
        }

        if (0 === $c) {
            $posts_per_cat_tag .= sprintf(esc_html__('No posts with %s!', 'post-volume-stats'), $typetitleplural) . '<br />';
        }
        return $posts_per_cat_tag;
    }


    /**
     * NUMBER OF DAYS BETWEEN POSTS
     */
    public function sdpvs_interval_between_posts_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_posts_in_order($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf( esc_html__('Intervals Between Posts %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $i=0;
        if (!empty($this->list_array[$i]['name'])) {
            while (isset($this->list_array[$i]['name'])) {
                if (!$this->list_array[$i]['volume']) {
                    $this->list_array[$i]['volume'] = 0;
                }
                $this->list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '</p>';
                $i++;
            }
        }

        return $this->list_string;
    }


    /**
     * NUMBER OF POSTS PER DAY-OF-WEEK TEXT
     */
    public function sdpvs_posts_per_dayofweek_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_posts_per_dayofweek($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->find_highest_first_and_total($this->list_array);
        $this->list_string = '<h2>' . sprintf (esc_html__('Post Volumes per Day of the Week %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $this->list_string .= "<p>Which day of the week the $this->total_volume_of_posts posts were made on.</p>";
        for ($i = 0; $i <= 6; $i++) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '</p>';
        }
        return $this->list_string;
    }

    /**
     * NUMBER OF POSTS PER HOUR TEXT
     */
    public function sdpvs_posts_per_hour_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_posts_per_hour($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->find_highest_first_and_total($this->list_array);
        $this->list_string = '<h2>' . sprintf ( esc_html__('Post Volumes per Hour %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $this->list_string .= "<p>Which hour of the day the $this->total_volume_of_posts posts were made on.</p>";
        for ($i = 0; $i <= 23; $i++) {
            $this->list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '</p>';
        }
        return $this->list_string;
    }

    /**
     * NUMBER OF POSTS PER MONTH TEXT
     */
    public function sdpvs_posts_per_month_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_posts_per_month($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf ( esc_html__('Post Volumes per Month %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        for ($i = 0; $i < 12; $i++) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= '<p>' . sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '</p>';
        }
        return $this->list_string;
    }

    /**
     * NUMBER OF POSTS PER DAY OF MONTH TEXT
     */
    public function sdpvs_posts_per_day_of_month_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "" ) {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_posts_per_dayofmonth($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf ( esc_html__('Post Volumes per Day of the Month %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        for ($i = 0; $i < 31; $i++) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '<br />';
        }
        return $this->list_string;
    }



    /**
     * NUMBER OF WORDS PER POST
     */
    public function sdpvs_words_per_post_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "") {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_words_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf( esc_html__('Words per Post %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $i=0;
        while ( array_key_exists($i, $this->list_array) ) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '<br />';

            $i++;
        }
        return $this->list_string;
    }


    /**
     * NUMBER OF IMAGES PER POST
     */
    public function sdpvs_images_per_post_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "") {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_images_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf( esc_html__('Images per Post %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $i=0;
        while ( array_key_exists($i, $this->list_array) ) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '<br />';

            $i++;
        }
        return $this->list_string;
    }

    /**
     * NUMBER OF IMAGES PER POST
     */
    public function sdpvs_comments_per_post_list($searchyear = 0, $searchauthor = 0, $start_date = "", $end_date = "", $search_text = "") {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $label = "";
        if( isset ($start_date) ){
            $start_date = htmlspecialchars( $start_date, ENT_QUOTES );
        }
        if( isset ($end_date) ){
            $end_date = htmlspecialchars( $end_date, ENT_QUOTES );
        }
        if( 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $extradesc = " by $user->display_name";
        }else{
            $extradesc = "";
        }
        if(0 < $searchyear){
            $label = "in $searchyear";
        }elseif(isset($start_date, $end_date) && "" != $start_date && "" != $end_date){
            $label = "($start_date to $end_date)";
        }
        $extra = $this->add_containing_text_search($search_text);
        $this->sdpvs_number_of_comments_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
        $this->list_string = '<h2>' . sprintf( esc_html__('Comments per Post %1$s %2$s', 'post-volume-stats'), $extradesc, $label ) . $extra . '</h2>';
        $i=0;
        while ( array_key_exists($i, $this->list_array) ) {
            if (!$this->list_array[$i]['volume']) {
                $this->list_array[$i]['volume'] = 0;
            }
            $this->list_string .= sprintf(esc_html__('%s: %d posts', 'post-volume-stats'), $this->list_array[$i]['name'], $this->list_array[$i]['volume']) . '<br />';

            $i++;
        }
        return $this->list_string;
    }



    /**
     * COMPILE YEARS MATRIX
     */
    public function sdpvs_compile_years_matrix($type = "", $firstval=0, $searchauthor=0, $search_text = "" ) {
        if("tag" === $type){
            $type = "post_tag";
        }
        $firstval = absint($firstval);
        $this->sdpvs_number_of_posts_per_year($searchauthor, $search_text);
        $chart_array = $this->list_array;

        for ($i = $firstval; $i >= 0; $i--) {
            $searchyear = absint($chart_array[$i]['name']);
            if ("hour" === $type) {
                $this->sdpvs_number_of_posts_per_hour($searchyear, $searchauthor,'','',$search_text);
            } elseif ("dayofweek" === $type) {
                $this->sdpvs_number_of_posts_per_dayofweek($searchyear, $searchauthor,'','',$search_text);
            } elseif ("month" === $type) {
                $this->sdpvs_number_of_posts_per_month($searchyear, $searchauthor,'','',$search_text);
            } elseif ("dayofmonth" === $type) {
                $this->sdpvs_number_of_posts_per_dayofmonth($searchyear, $searchauthor,'','',$search_text);
            } elseif("words" === $type){
                $this->sdpvs_number_of_words_per_post($searchyear, $searchauthor,'','',$search_text);
            } elseif("images" === $type){
                $this->sdpvs_number_of_images_per_post($searchyear, $searchauthor,'','',$search_text);
            } elseif("comments" === $type){
                $this->sdpvs_number_of_comments_per_post($searchyear, $searchauthor,'','',$search_text);
            }elseif("interval" === $type){
                $this->sdpvs_number_of_posts_in_order($searchyear, $searchauthor,'','',$search_text);
            }else{
                $this->sdpvs_post_tax_type_vols_structured($type, $searchyear, $searchauthor,'','',$search_text);
            }

            $a=0;
            while ( array_key_exists($a, $this->list_array) ) {
                if(0 === $i){
                    $this->year_matrix[$a]['label'] = $this->list_array[$a]['name'];
                }
                $this->year_matrix[$a][$i] = $this->list_array[$a]['volume'];
                $a++;
            }
        }
    }


    /**
     * COMPARE YEARS
     */

     public function sdpvs_compare_years_rows($type="", $searchauthor=0, $search_text="") {
        $searchauthor = absint($searchauthor);
        //$user = "";
        $userstring = "";
        //$years_total = 0;
        //$number_of_years = 0;

        // All this just gets the number of years
        $this->sdpvs_number_of_posts_per_year($searchauthor, $search_text);
        $chart_array = $this->list_array;
        $this->find_highest_first_and_total($chart_array);

         //var_dump($chart_array);
         //exit();

        $this->sdpvs_compile_years_matrix($type, $this->first_val, $searchauthor, $search_text);



        if( isset($searchauthor) && 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $userstring = " by $user->display_name ";
        }


         $this->output_compare_list = '<h2>';

        if ("hour" === $type) {
            $this->output_compare_list .= sprintf(esc_html__('Posts per Hour%1$s', 'post-volume-stats'), $userstring);
        } elseif ("dayofweek" === $type) {
            $this->output_compare_list .= sprintf(esc_html__('Posts per Day of the week%1$s', 'post-volume-stats'), $userstring);
        } elseif ("month" === $type) {
            $this->output_compare_list .= sprintf(esc_html__('Posts per Month%1$s', 'post-volume-stats'), $userstring);
        } elseif ("dayofmonth" === $type) {
            $this->output_compare_list .= sprintf(esc_html__('Posts per Day of the Month%1$s', 'post-volume-stats'), $userstring);
        } elseif("words" === $type){
            $this->output_compare_list .= sprintf(esc_html__('Words per Post%1$s', 'post-volume-stats'), $userstring);
        } elseif("images" === $type){
            $this->output_compare_list .= sprintf(esc_html__('Images per Post%1$s', 'post-volume-stats'), $userstring);
        } elseif("comments" === $type){
            $this->output_compare_list .= sprintf(esc_html__('Comments per Post%1$s', 'post-volume-stats'), $userstring);
        } elseif("interval" === $type){
            $this->output_compare_list .= sprintf(esc_html__('Intervals Between Posts%1$s', 'post-volume-stats'), $userstring);
        }else{
            $this->output_compare_list .= sprintf(esc_html__('Posts per Taxonomy: %1$s%2$s', 'post-volume-stats'), $type, $userstring);
        }
         if (!empty($search_text)) {
             $this->output_compare_list .= sprintf(__(' containing text "%s"', 'post-volume-stats'), $search_text);
         }
         $this->output_compare_list .= '</h2>';

        $this->output_compare_list .= "<table>";
        $this->output_compare_list .= "<tr>";
        $this->output_compare_list .= "<td>&nbsp;</td>";
        for ($i = $this->first_val; $i >= 0; $i--) {
            $searchyear = absint($chart_array[$i]['name']);
            $this->output_compare_list .= "<td><strong>$searchyear</strong></td>";
        }
        $this->output_compare_list .= "<td><strong>" . esc_html__('Total', 'post-volume-stats') . "</strong></td>";
        $this->output_compare_list .= "</tr>";
        $a=0;
        while ( array_key_exists($a, $this->list_array) ) {
            $count_total=0;
            $this->output_compare_list .= "<tr>";
            $this->output_compare_list .= '<td nobr>' . sprintf(esc_html__('%s', 'post-volume-stats'), $this->year_matrix[$a]['label']) . '</td>';
            for ($i = $this->first_val; $i >= 0; $i--) {
                $this->output_compare_list .= "<td>{$this->year_matrix[$a][$i]}</td>";
                $count_total += $this->year_matrix[$a][$i];
            }
            $this->output_compare_list .= "<td>$count_total</td>";
            $this->output_compare_list .= "</tr>";
            $a++;
        }
        $this->output_compare_list .= "</table>";

        return $this->output_compare_list;
    }


    public function sdpvs_create_csv_output($type = "", $searchauthor=0, $search_text = "") {
        $searchauthor = absint($searchauthor);
        $userstring = "";
        $textstring = '';
        if( isset($searchauthor) && 0 < $searchauthor){
            $user = get_user_by( 'id', $searchauthor );
            $userstring = " ($user->display_name)";
        }
        if (!empty($search_text)) {
            $textstring = " containing \"$search_text\"";
        }
        // All this just gets the number of years
        $this->sdpvs_number_of_posts_per_year($searchauthor, $search_text);
        $chart_array = $this->list_array;
        $this->find_highest_first_and_total($chart_array);

        $this->sdpvs_compile_years_matrix($type, $this->first_val, $searchauthor, $search_text);
        if("words"==$type) {
            $this->output_compare_list = "Words per Post$userstring$textstring,";
        }elseif("images"==$type){
            $this->output_compare_list = "Images per Post$userstring$textstring,";
        }elseif("comments"==$type){
            $this->output_compare_list = "Comments per Post$userstring$textstring,";
        }elseif("hour"==$type){
            $this->output_compare_list = "Hours of the Day$userstring$textstring,";
        }elseif("dayofweek"==$type){
            $this->output_compare_list = "Days of the Week$userstring$textstring,";
        }elseif("month"==$type){
            $this->output_compare_list = "Months$userstring$textstring,";
        }elseif("dayofmonth"==$type){
            $this->output_compare_list = "Days of the Month$userstring$textstring,";
        }elseif("category"==$type){
            $this->output_compare_list = "Categories$userstring$textstring,";
        }elseif("tag"==$type){
            $this->output_compare_list = "Tags$userstring$textstring,";
        }elseif("interval"==$type){
            $this->output_compare_list = "Interval$userstring$textstring,";
        }else{
            $this->output_compare_list = $type."$userstring$textstring,";
        }

        for ($i = $this->first_val; $i >= 0; $i--) {
            $searchyear = absint($chart_array[$i]['name']);
            $this->output_compare_list .= "$searchyear,";
        }
        $this->output_compare_list .= "Total,";
        $this->output_compare_list .= PHP_EOL;
        $a=0;
        while ( array_key_exists($a, $this->list_array) ) {
            $count_total=0;

            $this->output_compare_list .=  sprintf(esc_html__('%s', 'post-volume-stats'), $this->year_matrix[$a]['label']) . ',';
            for ($i = $this->first_val; $i >= 0; $i--) {
                $this->output_compare_list .= "{$this->year_matrix[$a][$i]},";
                $count_total += $this->year_matrix[$a][$i];
            }
            $this->output_compare_list .= "$count_total,";
            $this->output_compare_list .= PHP_EOL;
            $a++;
        }

        return $this->output_compare_list;
    }






}

