<?php

// Some CSS to format the plugin page
function sdpvs_post_volume_stats_css() {
	echo "
	<style type='text/css'>
	
.sdpvs_col{
	width: 250px; 
	display: inline-block; 
	vertical-align: top;
	padding: 5px;
}

.sdpvs_col p {
	text-align: left;
}

@media (max-width: 520px) {
  .sdpvs_col {
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
