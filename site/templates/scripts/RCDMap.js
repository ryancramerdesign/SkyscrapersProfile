var RCDMap = {

	markers: [], 
	numMarkers: 0,

	options: {
		zoom: 10, 
		center: null, 
		mapTypeId: google.maps.MapTypeId.SATELLITE, 
		scrollwheel: false, 
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU	
		}, 
		scaleControl: false
	}, 

	map: null,
	mapNote: null,
	currentUrl: '',

	init: function(mapId, lat, lng) {
		RCDMap.options.center = new google.maps.LatLng(lat, lng); 
		RCDMap.map = new google.maps.Map(document.getElementById(mapId), RCDMap.options); 
		RCDMap.mapNote = $("<div id='map_note' class='map_note'></div>");	
		$("body").append(RCDMap.mapNote);
		RCDMap.mapNote.css({
			position: 'absolute', 
			left: 0, 
			top: '-100px'
		});
		RCDMap.mapNote.mouseout(function() {
			RCDMap.mapNote.hide();
		}).click(function() {
			if(RCDMap.currentUrl.length) window.location.href = RCDMap.currentUrl; 
		}); 
	}, 

	addMarker: function(title, url, lat, lng) {

		var marker = new google.maps.Marker({ 
			position: new google.maps.LatLng(lat, lng), 
			map: RCDMap.map 
		});

		RCDMap.markers[RCDMap.numMarkers] = marker;
		RCDMap.numMarkers++;

		google.maps.event.addListener(marker, 'mouseover', function(e) { 
			RCDMap.currentUrl = url; 
			RCDMap.mapNote.html("<span>" + title + "</span>").show()
				.css('top', '0px')
				.css('left', '0px')
				.css('display', 'block')
				.css('width', 'auto')
				.css('color', '#ffffff')
				.css('background', '#000000')
				.css('padding', '2px 5px 2px 5px');

			$(document).mousemove(function(e) {
				$("#map_note").css({
					'top': e.pageY-10, 	
					'left': e.pageX+15
					});
			}); 

		});

		google.maps.event.addListener(marker, 'mouseout', function(e) {
			RCDMap.mapNote.hide();
			$(document).unbind("mousemove"); 
		}); 

		google.maps.event.addListener(marker, 'click', function(e) {
			window.location.href = RCDMap.currentUrl; 
		}); 


	}
}

