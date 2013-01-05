/**
 * Display a Google Map and pinpoint a location for InputfieldMapMarker
 *
 */

var InputfieldMapMarker = {

	options: {
		zoom: 12, // mats, previously 5
		draggable: true, // +mats
		center: null,
		mapTypeId: google.maps.MapTypeId.HYBRID,
		scrollwheel: false,	
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		},
		scaleControl: false
	},	

	init: function(mapId, lat, lng) {
		var options = InputfieldMapMarker.options; 
		options.center = new google.maps.LatLng(lat, lng); 	
		// options.zoom = 5; 
		options.zoom = 12; // mats
		var map = new google.maps.Map(document.getElementById(mapId), options); 	
		var marker = new google.maps.Marker({
			position: options.center, 
			map: map,
			draggable: options.draggable	
		}); 

		var $map = $('#' + mapId); 
		var $lat = $map.siblings(".InputfieldMapMarkerLat").find("input[type=text]");
		var $lng = $map.siblings(".InputfieldMapMarkerLng").find("input[type=text]");
		var $addr = $map.siblings(".InputfieldMapMarkerAddress").find("input[type=text]"); 
		var $toggle = $map.siblings(".InputfieldMapMarkerToggle").find("input[type=checkbox]");
		var $notes = $map.siblings(".notes");

		$lat.val(marker.getPosition().lat());
		$lng.val(marker.getPosition().lng());

		google.maps.event.addListener(marker, 'dragend', function(event) {
			var geocoder = new google.maps.Geocoder();
			var position = this.getPosition();
			$lat.val(position.lat());
			$lng.val(position.lng());
			if($toggle.is(":checked")) {
				geocoder.geocode({ 'latLng': position }, function(results, status) {
					if(status == google.maps.GeocoderStatus.OK && results[0]) {
						$addr.val(results[0].formatted_address);	
					}
					$notes.text(status);
				});
			}
		});

		$addr.blur(function() {
			if(!$toggle.is(":checked")) return true;
			var geocoder = new google.maps.Geocoder();
			geocoder.geocode({ 'address': $(this).val()}, function(results, status) {
				if(status == google.maps.GeocoderStatus.OK && results[0]) {
					var position = results[0].geometry.location;
					map.setCenter(position);
					marker.setPosition(position);
					$lat.val(position.lat());
					$lng.val(position.lng());
				}
				$notes.text(status);
			});
			return true;	
		}); 

		$toggle.click(function() {
			if($(this).is(":checked")) {
				$notes.text('Geocode ON');
				// google.maps.event.trigger(marker, 'dragend'); 
				$addr.trigger('blur');
			} else {
				$notes.text('Geocode OFF');
			}
			return true;
		});
		
	}
};

$(document).ready(function() {
	$(".InputfieldMapMarkerMap").each(function() {
		var $t = $(this);
		InputfieldMapMarker.init($t.attr('id'), $t.attr('data-lat'), $t.attr('data-lng')); 
	}); 
}); 
