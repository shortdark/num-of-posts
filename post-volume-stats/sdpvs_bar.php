<?php

defined('ABSPATH') or die('No script kiddies please!');

class sdpvsBarChart extends sdpvsArrays {

    protected $total_volume_of_posts;

    protected $first_val;

    protected $highest_val;

    protected $highest_val2;

    /**
     * DISPLAY DATA IN A BAR CHART
     */
    public function sdpvs_draw_bar_chart_svg($which = "", $searchyear = 0, $searchauthor = 0, $subpage = "", $public = "", $text_color="black", $start_date = "", $end_date = "", $search_text= "") {
        $searchyear = absint($searchyear);
        $searchauthor = absint($searchauthor);
        $graphwidth = 200;
        $graphheight = 200;
        $graphtop = 10;
        $graphbottom = 30;
        $graph_color = "blue";
        $highlight_color = "red";
        $weekend_color = "#ff9900";

        $exportcsv = 'no';
        $genoptions = get_option('sdpvs_general_settings');
        if (false !== $genoptions) {
            $exportcsv = htmlspecialchars( $genoptions['exportcsv'], ENT_QUOTES);
        }

        if ("year" === $which) {
            $this->sdpvs_number_of_posts_per_year($searchauthor, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->first_val + 1;
            $order = "desc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Years', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Year', 'post-volume-stats') . '</h2>';
            }
        } elseif ("dayofweek" === $which) {
            $this->sdpvs_number_of_posts_per_dayofweek($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = 7;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Days of the Week', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Day of the Week', 'post-volume-stats') . '</h2>';
            }
        } elseif ("hour" === $which) {
            $this->sdpvs_number_of_posts_per_hour($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = 24;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Hours', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Hour', 'post-volume-stats') . '</h2>';
            }
        } elseif ("month" === $which) {
            $this->sdpvs_number_of_posts_per_month($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = 12;
            $order = "asc";
            if ("y" !== $public) {
                echo '<h2>' . esc_html__('Months', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Month', 'post-volume-stats') . '</h2>';
            }
        } elseif ("dayofmonth" === $which) {
            $this->sdpvs_number_of_posts_per_dayofmonth($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = 31;
            $order = "asc";
            if ("y" !== $public) {
                echo '<h2>' . esc_html__('Days of the Month', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Day of the Month', 'post-volume-stats') . '</h2>';
            }
        } elseif ("author" === $which) {
            $this->sdpvs_number_of_posts_per_author($searchyear, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->total_bars;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Authors', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Posts per Author', 'post-volume-stats') . '</h2>';
            }
        } elseif ("words" === $which) {
            $this->sdpvs_number_of_words_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->total_bars;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Words per Post', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Words per Post', 'post-volume-stats') . '</h2>';
            }
        } elseif ("images" === $which) {
            $this->sdpvs_number_of_images_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->total_bars;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Images per Post', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Images per Post', 'post-volume-stats') . '</h2>';
            }
        } elseif ("comments" === $which) {
            $this->sdpvs_number_of_comments_per_post($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->total_bars;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Comments per Post', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Comments per Post', 'post-volume-stats') . '</h2>';
            }
        }elseif ("interval" === $which) {
            $this->sdpvs_number_of_posts_in_order($searchyear, $searchauthor, $start_date, $end_date, $search_text);
            $chart_array = $this->list_array;
            $this->find_highest_first_and_total($chart_array);
            $bars_total = $this->total_bars;
            $order = "asc";
            if ("y" != $public) {
                echo '<h2>' . esc_html__('Intervals Between Posts', 'post-volume-stats') . '</h2>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('Intervals Between Posts', 'post-volume-stats') . '</h2>';
            }
        }
        if ("year" !== $which && "y" === $public) {
            if (0 < $searchyear) {
                echo '<h3>' . sprintf(esc_html__('%d', 'post-volume-stats'), $searchyear) . '</h3>';
            } else {
                echo "<h2 style='color: {$text_color};'>" . esc_html__('All-time', 'post-volume-stats') . '</h3>';
            }
        }

        // specify the margin width on the left of the bar chart
        $graphleft = 25;

        if ($bars_total <= 0) {
            $bars_total = 1;
        }
        $bar_width = $graphwidth / $bars_total;
        $svgwidth = $graphwidth + $graphleft;
        $svgheight = $graphheight + $graphtop + $graphbottom;

        echo "<svg width=\"" . $svgwidth . "px\" height=\"" . $svgheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" class=\"sdpvs_bar\">\n";
        echo "<path stroke=\"$text_color\" stroke-width=\"1\" d=\"M$graphleft $graphtop v $graphheight\"></path>";

        $number_per_increment = ceil($this->highest_val / 5);
        // If an increment is a strange number, like 39, round it up or down to 40 or 35.
        if (5 < $number_per_increment) {
            $inc_mod = $number_per_increment % 5;
            if (0 === $inc_mod) {
            } elseif (0.5 < $inc_mod) {
                while (0 != $number_per_increment % 5) {
                    $number_per_increment++;
                }
            } elseif (0.5 >= $inc_mod) {
                while (0 != $number_per_increment % 5) {
                    $number_per_increment--;
                }
            }
        }
        if ($this->highest_val <= 0) {
                $this->highest_val = 1;
        }
        $horiz_line_increment = $graphheight * ($number_per_increment / $this->highest_val);

        for ($j = 0; $j <= 5; $j++) {
            $depth = $graphtop + $graphheight - ($j * $horiz_line_increment);
            if ($graphtop <= $depth) {
                $value = $j * $number_per_increment;
                if (0 === $j) {
                    echo "<path stroke=\"$text_color\" stroke-width=\"1\" d=\"M$graphleft $depth h $graphwidth\"></path>";
                } else {
                    echo "<path stroke=\"$text_color\" stroke-width=\"0.2\" d=\"M$graphleft $depth h $graphwidth\"></path>";
                }
                $text_x = $graphleft - (strlen($value) * 7) - 5;
                $text_y = $depth + 4;
                echo "<text x=\"$text_x\" y=\"$text_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"$text_color\">$value</text>";
            }
        }
        $y_start = $graphheight + $graphtop;
        $i = 0;
        while (isset($chart_array[$i]['volume'])) {
            if (0 < $chart_array[$i]['volume']) {
                if ("desc" === $order) {
                    $x_start = $svgwidth - ($i * $bar_width);
                } elseif ("asc" === $order) {
                    $x_start = $bar_width + $graphleft + ($i * $bar_width);
                }
                if ($chart_array[$i]['name'] == $searchyear && "year" === $which) {
                    $color = $highlight_color;
                    $set_explicit_color = "background-color: $color;";
                }elseif ( isset($chart_array[$i]['id']) && $chart_array[$i]['id'] == $searchauthor && "author" === $which) {
                    $color = $highlight_color;
                    $set_explicit_color = "background-color: $color;";
                }elseif ( ($chart_array[$i]['name'] === "Saturday" || $chart_array[$i]['name'] === "Sunday") && "dayofweek" === $which) {
                    $color = $weekend_color;
                    $set_explicit_color = "background-color: $color;";
                } else {
                    $color = $graph_color;
                    $set_explicit_color = "";
                }
                $bar_height = absint($graphheight * ( absint($chart_array[$i]['volume']) / $this->highest_val));

                if ("year" === $which) {
                    if ($chart_array[$i]['name'] == $searchyear) {
                        $year_form_value = "";
                    } else {
                        $year_form_value = $chart_array[$i]['name'];
                    }

                    $legend = $chart_array[$i]['name'];
                    $legend_x = $x_start - ($bar_width / 2) - (strlen($legend) * 7) / 2;
                    $legend_y = $y_start + 17;
                    $legendShort = $legend;
                    $year_legend_button_x = $x_start - $bar_width;
                    if ($bars_total > $i) {
                        if (strlen($legendShort) * 7 > $bar_width) {
                            $legendShort = substr($legend, -2);
                            $legend_x = $x_start - ($bar_width / 2) - (strlen($legendShort) * 7) / 2;
                        }
                        echo "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"11px\" fill=\"$text_color\">" . sprintf(esc_html__('%d', 'my-text-domain'), $legendShort) . "</text>";
                        if ("y" != $public) {
                            echo "<foreignObject x=\"$year_legend_button_x\" y=\"$y_start\" width=\"$bar_width\" height=\"24\">";
                            echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
                            settings_fields('sdpvs_year_option');
                            echo " <input type=\"hidden\" name=\"sdpvs_year_option[year_number]\" id=\"year-number\" value=\"$year_form_value\">
                        <input type=\"submit\" style=\"height: 24px; width: " . $bar_width . "px;\" title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\" class=\"sdpvs_author_legend\">";
                            echo "</form></foreignObject>";
                        }
                    }

                    $form_y_offset = $y_start - $bar_height;
                    $form_x_offset = $x_start - $bar_width;

                    if ("y" != $public) {
                        echo "<path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"white\"></path>";
                        echo "<foreignObject x=\"$form_x_offset\" y=\"$form_y_offset\" width=\"$bar_width\" height=\"$bar_height\">";
                        echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
                        settings_fields('sdpvs_year_option');
                        echo " <input type=\"hidden\" name=\"sdpvs_year_option[year_number]\" id=\"year-number\" value=\"$year_form_value\">
                        <input type=\"submit\" style=\"height: " . $bar_height . "px; width: " . $bar_width . "px; $set_explicit_color\" title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\" class=\"sdpvs_year_bar\">
                        </form>
                        </foreignObject>";
                    } else {
                        echo "<a xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
                    }

                } elseif ( "author" === $which) {
                    if ($chart_array[$i]['id'] == $searchauthor) {
                        $author_form_value = "";
                    } else {
                        $author_form_value = $chart_array[$i]['id'];
                    }
                    $legend = $chart_array[$i]['name'];
                    $legend_x = $x_start - ($bar_width / 2) - (strlen($legend) * 7) / 2;
                    $legend_y = $y_start + 17;
                    if (strlen($legend) * 7 >= $bar_width) {
                        $shorten_to = absint($bar_width / 7) - 1;
                        $legend_x = $x_start - ($bar_width / 2) - ($shorten_to * 7) / 2;
                        $legendShort = trim(substr($legend, 0, $shorten_to)) . '.';
                    } else {
                        $legendShort = $legend;
                    }
                    $form_y_offset = $y_start - $bar_height;
                    $form_x_offset = $x_start - $bar_width;
                    $author_button_height = $bar_height + 20;
                    $auth_legend_button_x = $x_start - $bar_width;




                    //echo "<a xlink:href=\"#\" xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"$text_color\">" . sprintf(esc_html__('%s', 'my-text-domain'), $legendShort) . "</text></a>";


                    if ("y" != $public) {
                        // Text
                        echo "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"$text_color\">" . sprintf(esc_html__('%s', 'my-text-domain'), $legendShort) . "</text>";
                        echo "<foreignObject x=\"$auth_legend_button_x\" y=\"$y_start\" width=\"$bar_width\" height=\"24\">";
                        echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
                        settings_fields('sdpvs_author_option');
                        echo " <input type=\"hidden\" name=\"sdpvs_author_option[author_number]\" id=\"author-number\" value=\"$author_form_value\">
                        <input type=\"submit\" style=\"height: 24px; width: " . $bar_width . "px;\" title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\" class=\"sdpvs_author_legend\">";
                        echo "</form></foreignObject>";

                        // Bar
                        echo "<path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"white\"></path>";
                        echo "<foreignObject x=\"$form_x_offset\" y=\"$form_y_offset\" width=\"$bar_width\" height=\"$bar_height\">";
                        echo "<form action=\"options.php\" method=\"post\" class=\"sdpvs_year_form\" style=\"border:0; margin:0;padding:0;\">";
                        settings_fields('sdpvs_author_option');
                        echo " <input type=\"hidden\" name=\"sdpvs_author_option[author_number]\" id=\"author-number\" value=\"$author_form_value\">
                        <input type=\"submit\" style=\"height: " . $author_button_height . "px; width: " . $bar_width . "px; $set_explicit_color\" title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\" class=\"sdpvs_year_bar\">
                        </form>
                        </foreignObject>";
                    } else {
                        echo "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"$text_color\">" . sprintf(esc_html__('%s', 'my-text-domain'), $legendShort) . "</text>";
                        echo "<a xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
                    }

                } else {
                    echo "<a xlink:title=\"{$chart_array[$i]['name']}, {$chart_array[$i]['volume']} posts\"><path fill-opacity=\"0.5\" d=\"M$x_start $y_start v -$bar_height h -$bar_width v $bar_height h $bar_width \" fill=\"$color\" class=\"sdpvs_bar\"></path></a>";
                }

            }else if ("year" === $which && $bars_total > $i) {
                // Even if no posts, still show the year number
                $x_start = $svgwidth - ($i * $bar_width);
                $legend = $chart_array[$i]['name'];
                $legend_x = $x_start - ($bar_width / 2) - (strlen($legend) * 7) / 2;
                $legend_y = $y_start + 17;
                $legendShort = $legend;

                    if (strlen($legendShort) * 7 > $bar_width) {
                        $legendShort = substr($legend, -2);
                        $legend_x = $x_start - ($bar_width / 2) - (strlen($legendShort) * 7) / 2;
                    }
                    echo "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"11px\" fill=\"$text_color\">" . sprintf(esc_html__('%d', 'my-text-domain'), $legendShort) . "</text>";
            }
		$i++;
        }

        echo "</svg>\n";
        if ("n" === $subpage && "y" !== $public) {
            echo "<form class='sdpvs_form' action='' method='POST'><input type='hidden' name='whichdata' value='$which'><input type='submit' class='button-primary sdpvs_load_content' value='Show Data'></form></p>";
            if("words" === $which || "images" === $which || "comments" === $which || "hour" === $which || "dayofweek" === $which || "month" === $which || "dayofmonth" === $which || "interval" === $which){
                echo "<form class='sdpvs_compare' action='' method='POST'><input type='hidden' name='comparedata' value='$which'><input type='submit' class='button-primary sdpvs_load_content' value='Compare Years'></form></p>";
                if("yes"==$exportcsv){
                    $sdpvs_csv_download_url = plugin_dir_url( __FILE__ ) . "download-csv/$which.csv";
                    echo "<form class='sdpvs_export' action=\"$sdpvs_csv_download_url\" method='POST'><input type='submit' class='button-primary' value='Export Compare Years CSV'></form></p>";
                }

            }
        }
    }


    /**
     * BIG LINE-ONLY GRAPH
     */
    public function sdpvs_comparison_line_graph($type, $select_array, $searchauthor, $colorlist, $public='n') {
        if (empty($select_array)) {
            return '';
        }
        $searchauthor = absint($searchauthor);
        $graphwidth = 600;
        $graphheight = 400;
        $graphtop = 20;
        $graphbottom = 40;
        $line_graph = '';

        if ("tag" === $type) {
            $taxonomy_type = 'post_tag';
        }else{
            $taxonomy_type = $type;
        }
        if("tag" != $type && "category" != $type && "y" != $public ){
            $logical_starter = 1;
        }else{
            $logical_starter = 0;
        }

        // All this just gets the number of years
        $this->sdpvs_number_of_posts_per_year($searchauthor, '');
        if (empty($this->list_array)) {
            return '';
        }
        $chart_array = $this->list_array;
        $this->find_highest_first_and_total($chart_array);
        $bars_total = $this->first_val + 1;

        $x = $logical_starter;
        while (!empty($select_array[1][$x])) {
            if (0 < $select_array[1][$x]) {
                $term_id = $select_array[1][$x];
                for ($i = 0; $i <= $this->first_val; $i++) {
                    $searchyear = absint($chart_array[$i]['name']);
                    // Get slug, name and volume
                    $item = $this->sdpvs_get_one_item_info($term_id, $taxonomy_type, $searchyear, $searchauthor);
                    if (!$this->highest_val2 || $this->highest_val2 < $item['volume']) {
                        $this->highest_val2 = $item['volume'];
                    }
                }
            }
            $x++;
        }

        // specify the margin width on the left of the bar chart
        $graphleft = 25;

        $bar_width = $graphwidth / $bars_total;
        $svgwidth = $graphwidth + $graphleft;
        $svgheight = $graphheight + $graphtop + $graphbottom;

        $this->svg_output_string = "<svg width=\"" . $svgwidth . "px\" height=\"" . $svgheight . "px\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" class=\"sdpvs_bar\">\n";
        $this->svg_output_string .= "<path stroke=\"#000\" stroke-width=\"1\" d=\"M$graphleft $graphtop v $graphheight\"></path>";

        $number_per_increment = ceil($this->highest_val2 / 5);
        // If an increment is a strange number, like 39, round it up or down to 40 or 35.
        if (5 < $number_per_increment) {
            $inc_mod = $number_per_increment % 5;
            if (0.5 < $inc_mod) {
                while (0 != $number_per_increment % 5) {
                    $number_per_increment++;
                }
            } elseif (0.5 >= $inc_mod && 0 != $inc_mod) {
                while (0 != $number_per_increment % 5) {
                    $number_per_increment--;
                }
            }
        }
        $horiz_line_increment = $graphheight * ($number_per_increment / $this->highest_val2);

        for ($j = 0; $j <= 5; $j++) {
            $depth = $graphtop + $graphheight - ($j * $horiz_line_increment);
            if ($graphtop <= $depth) {
                $value = $j * $number_per_increment;
                if (0 === $j) {
                    $this->svg_output_string .= "<path stroke=\"#000\" stroke-width=\"1\" d=\"M$graphleft $depth h $graphwidth\"></path>";
                } else {
                    $this->svg_output_string .= "<path stroke=\"#000\" stroke-width=\"0.2\" d=\"M$graphleft $depth h $graphwidth\"></path>";
                }
                $text_x = $graphleft - (strlen($value) * 7) - 5;
                $text_y = $depth + 4;
                $this->svg_output_string .= "<text x=\"$text_x\" y=\"$text_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"#000\">$value</text>";
            }
        }

        $y_start = $graphheight + $graphtop;
        for ($i = 0; $i <= $this->first_val; $i++) {
            $legend = absint($chart_array[$i]['name']);
            $x_start = $svgwidth - ($i * $bar_width);

            if (strlen($legend) * 7 < $bar_width) {
                $legend_x = $x_start - ($bar_width / 2) - (strlen($legend) * 7) / 2;
                $legend_y = $y_start + 17;
                $this->svg_output_string .= "<text x=\"$legend_x\" y=\"$legend_y\" font-family=\"sans-serif\" font-size=\"12px\" fill=\"black\">" . sprintf(esc_html__('%d', 'my-text-domain'), $legend) . "</text>";
            }
        }

        $x = $logical_starter;
        $y = 0;
        while (!empty($select_array[1][$x])) {
            if (0 < $select_array[1][$x]) {

                if (10 > $y) {
                    $color = $colorlist[$y];
                } else {
                    $color = "#000";
                }

                $term_id = absint($select_array[1][$x]);

                if ("y" === $public) {
                    $link = get_term_link( $term_id );
                }


                for ($i = 0; $i <= $this->first_val; $i++) {
                    $searchyear = absint($chart_array[$i]['name']);
                    // Get slug, name and volume
                    $item = $this->sdpvs_get_one_item_info($term_id, $taxonomy_type, $searchyear, $searchauthor);

                    $point_height = absint($graphheight * ( absint($item['volume']) / absint($this->highest_val2)));
                    $x_start = $svgwidth - ($i * $bar_width) - $bar_width / 2;
                    $y_start = $graphheight + $graphtop - $point_height;

                    if (0 < $this->first_val) {
                        if (0 === $i) {
                            $line_graph = "<path d=\"M$x_start $y_start, ";
                        } elseif ($i == $this->first_val) {
                            $line_graph .= "$x_start $y_start\" fill=\"transparent\" stroke=\"$color\"/>";
                        } else {
                            $line_graph .= "$x_start $y_start, ";
                        }
                    }

                    if("y" != $public){
                        if ("category" === $type) {
                            $link_part = "category_name";
                        } elseif ("tag" === $type) {
                            $link_part = "tag";
                        }else{
                            $link_part = $taxonomy_type;
                        }
                        $link = admin_url("edit.php?$link_part=" . $item['slug']);
                    }

                    $this->svg_output_string .= "<a href=\"$link\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xlink:title=\"{$item['name']}, {$item['volume']} posts out of {$chart_array[$i]['volume']}\"><circle cx=\"$x_start\" cy=\"$y_start\" r=\"3\" stroke=\"$color\" stroke-width=\"0\" fill=\"$color\" /></a>";
                }
                $this->svg_output_string .= $line_graph;
            }
            $x++;
            $y++;
        }

        $this->svg_output_string .= "</svg>\n";

        return $this->svg_output_string;
    }

}

