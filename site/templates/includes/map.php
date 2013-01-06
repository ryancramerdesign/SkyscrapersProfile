<?php

/**
 * When included, this file outputs a Google Map
 *
 * It is basically a #map div, followed by a <script> that init's a Google 
 * Map and dynamically generates the markers from page dada. 
 *
 */

?>

<div id='map'></div>

<script type='text/javascript'>
	$(document).ready(function() { 

		<?php

		// determine the center point lat/lng and zoom
		if($page->map && $page->map->lat) {
			// take center point from current page
			$lat = $page->map->lat;
			$lng = $page->map->lng; 
			if($page->template == 'skyscraper') $zoom = 15; 
				else $zoom = 10; 
		} else { 
			// use center point in the middle of the US
			$lat = 39.334297; 
			$lng = -97.756348; 
			$zoom = 3; 
		}

		?>

		RCDMap.options.mapTypeId = google.maps.MapTypeId.HYBRID; 
		RCDMap.options.zoom = <?php echo $zoom; ?>;
		RCDMap.init('map', <?php echo $lat?>, <?php echo $lng?>); 

		$("#content").addClass('has_map'); 

		<?php 

		if($page->template == 'skyscraper') {
			$markers = new PageArray();
			$markers->add($page); 

		} else if($page->template == 'cities' || $page->template == 'home') {
			$markers = $pages->get("/cities/")->children();

		} else {
			$markers = mapSkyscrapers(); 
		}

		foreach($markers as $item) {
			if(!$item->map->lat) continue; 
			echo "\n\t\tRCDMap.addMarker('{$item->title}', '{$item->url}', {$item->map->lat}, {$item->map->lng});"; 
		}

		?>

	});

</script>	

