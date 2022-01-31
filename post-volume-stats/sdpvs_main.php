<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsMainContent {

    public function sdpvs_page_content() {
        $start_date = "";
        $end_date = "";

        // create an instance of the required classes
        $sdpvs_bar = new sdpvsBarChart();
        $sdpvs_pie = new sdpvsPieChart();

        $selected = 0;
        $options = get_option('sdpvs_year_option');
        if (false !== $options) {
            $selected = absint($options['year_number']);
        }

        if(isset($options['start_date'])){
            $start_date = htmlspecialchars( $options['start_date'], ENT_QUOTES );
        }
        if(isset($options['end_date'])){
            $end_date = htmlspecialchars( $options['end_date'], ENT_QUOTES);
        }
        $textoptions = get_option('sdpvs_text_option');
        if(isset($textoptions['search_text'])){
            $search_text = htmlspecialchars( $textoptions['search_text'], ENT_QUOTES);
        } else {
            $search_text = '';
        }

        $authoroptions = get_option('sdpvs_author_option');
        if (false != $authoroptions) {
            $author = absint($authoroptions['author_number']);
        }

        $authoroff = 'multiple';
        $customoff = 'no';
        $customvalue = '';
        $showimage = 'no';
        $showcomment = 'no';
        $showfilter = 'no';
        $genoptions = get_option('sdpvs_general_settings');
        if (false !== $genoptions) {
            $authoroff = htmlspecialchars ( $genoptions['authoroff'], ENT_QUOTES);
            $customoff = htmlspecialchars ( $genoptions['customoff'], ENT_QUOTES);
            $customvalue = htmlspecialchars ( $genoptions['customvalue'], ENT_QUOTES);
            $showimage = htmlspecialchars ( $genoptions['showimage'], ENT_QUOTES);
            $showcomment = htmlspecialchars ( $genoptions['showcomment'], ENT_QUOTES);
            $showfilter = htmlspecialchars ( $genoptions['showrange'], ENT_QUOTES);
        }


        $sdpvs_settings_link = admin_url('admin.php?page=' . SDPVS__PLUGIN_SETTINGS);
        $sdpvs_filter_link = admin_url('admin.php?page=' . SDPVS__FILTER_RESULTS);

        if(isset($author) && null != $author){
            $user = get_user_by( 'id', $author );
            $extradesc = "for $user->display_name";
        }else{
            $extradesc = "";
            $author=0;
        }

        // Title
        echo '<h1 class="sdpvs">';
        echo esc_html__('Post Volume Stats: ', 'post-volume-stats');
        if (0 < $selected) {
            echo sprintf(__('%d', 'post-volume-stats'), $selected);
        }elseif($start_date){
            echo sprintf(__('%s to %s', 'post-volume-stats'), $start_date, $end_date);
        } else {
            echo __('All-Time', 'post-volume-stats');
        }
        if(isset($extradesc) && '' != $extradesc){
            echo sprintf(__('(%s)'), $extradesc);
        }
        if (!empty($search_text)) {
            echo sprintf(__(': Posts containing "%s"', 'post-volume-stats'), $search_text);
        }
        echo '</h1>';

        echo '<p>';
        echo __('Click a bar of the "Years" or "Authors" bar charts to change to that year or author, or click the selected year/author (red) to view the stats for all years/authors. ');
        if ('yes'==$showfilter) {
            echo sprintf(wp_kses(__('More filtering options in <a href="%1$s">Filter Results</a>. ', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_filter_link));
        }
        echo sprintf(wp_kses(__('Add more features in the <a href="%1$s">Post Volume Stats settings page</a>. ', 'post-volume-stats'), array('a' => array('href' => array()))), esc_url($sdpvs_settings_link));
        echo '</p>';

        echo "<hr>";

        if("one" != $authoroff){
            // posts per author bar chart
            echo "<div class='sdpvs_col'>";
            $sdpvs_bar->sdpvs_draw_bar_chart_svg('author', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
            echo "</div>";
        }

        // year bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('year', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per month bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('month', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per day of the month bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('dayofmonth', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per day of the week bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('dayofweek', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per hour of the day bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('hour', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // words per post bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('words', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        if( "yes" === $showimage ) {
            // images per post bar chart
            echo "<div class='sdpvs_col'>";
            $sdpvs_bar->sdpvs_draw_bar_chart_svg('images', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
            echo "</div>";
        }

        if( "yes" === $showcomment ) {
            // comments per post bar chart
            echo "<div class='sdpvs_col'>";
            $sdpvs_bar->sdpvs_draw_bar_chart_svg('comments', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
            echo "</div>";
        }

        // days between post bar chart
        echo "<div class='sdpvs_col'>";
        $sdpvs_bar->sdpvs_draw_bar_chart_svg('interval', $selected, $author, 'n', '', '', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per category pie chart
        echo "<div class='sdpvs_col'>";
        echo $sdpvs_pie->sdpvs_draw_pie_svg('category', $selected, $author, 'n', 'n', $start_date, $end_date, $search_text);
        echo "</div>";

        // posts per tag pie chart
        echo "<div class='sdpvs_col'>";
        echo $sdpvs_pie->sdpvs_draw_pie_svg('tag', $selected, $author, 'n', 'n', $start_date, $end_date, $search_text);
        echo "</div>";

        if( "yes" === $customoff && "_all_taxonomies" === $customvalue ){
            // Custom Taxonomies
            $args = array(
                'public'   => true,
                '_builtin' => false
            );
            $all_taxes = get_taxonomies( $args );
            $count_taxes = count( $all_taxes );
            if( 1 < $count_taxes ){
                foreach ( $all_taxes as $taxonomy ) {
                    if("category" != $taxonomy && "post_tag" != $taxonomy){
                        $tax_labels = get_taxonomy($taxonomy);
                        // posts per $tax_labels->name pie chart
                        echo "<div class='sdpvs_col'>";
                        echo $sdpvs_pie->sdpvs_draw_pie_svg($tax_labels->name, $selected, $author, 'n', 'n', $start_date, $end_date, $search_text);
                        echo "</div>";
                    }
                }
            }
        }elseif( "yes" === $customoff && "" != $customvalue ){
            // posts per custom taxonomy pie chart
            echo "<div class='sdpvs_col'>";
            echo $sdpvs_pie->sdpvs_draw_pie_svg($customvalue, $selected, $author, 'n', 'n', $start_date, $end_date, $search_text);
            echo "</div>";
        }
    }

}

