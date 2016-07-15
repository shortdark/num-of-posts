<?php

// Some CSS to format the plugin page
function sdpvs_post_volume_stats_css() {
	echo "
	<style type='text/css'>
	
#sdpvs_leftcol, #sdpvs_rightcol1, #sdpvs_rightcol2 {
	width: 250px; 
	display: inline-block; 
	vertical-align: top;
}

#sdpvs_leftcol p , #sdpvs_rightcol1 p, #sdpvs_rightcol2 p  {
	text-align: left;
}

@media (max-width: 520px) {
  #sdpvs_leftcol , #sdpvs_rightcol1 {
    width: 100%;
    width: 100vw;
    display: block;
  }
}
.sdpvs_pie{ 
	width: 200px; 
	height: 200px; 
} 
a .sdpvs_segment:hover, a .sdpvs_bar:hover{ 
	stroke:white; 
	fill: green; 
}

	</style>
	";
}

add_action('admin_head', 'sdpvs_post_volume_stats_css');

?>