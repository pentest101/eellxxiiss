/*
Elxis CMS - plugin Google Maps Links
Copyright (c) 2006-2020 elxis.org
https://www.elxis.org
*/

/* ADD SINGLE LOCATION */
function plugMapAddLocation() {
	var coordinates = document.getElementById('plgmap_area').value;
	if (coordinates == '') { return false; }
	var infotext = document.getElementById('plgmap_info').value;
	if (infotext == '') {
		var pcode = '{map}'+coordinates+'{/map}';
	} else {
		var pcode = '{map info="'+infotext+'"}'+coordinates+'{/map}';
	}
	addPluginCode(pcode);
}

/* ADD ROUTE */
function plugMapAddRoute() {
	var origin = document.getElementById('plgmap_origin').value;
	if (origin == '') { return false; }
	var destination = document.getElementById('plgmap_dest').value;
	if (destination == '') { return false; }
	var waypoints = document.getElementById('plgmap_wayp').value;
	var pmsObj = document.getElementById('plgmap_tmode');
	var travelmode = pmsObj.options[pmsObj.selectedIndex].value;
	if (travelmode == '') { travelmode = 'DRIVING'; }
	if (waypoints == '') {
		var pcode = '{map destination="'+destination+'" travelmode="'+travelmode+'"}'+origin+'{/map}';
	} else {
		var pcode = '{map destination="'+destination+'" travelmode="'+travelmode+'" waypoints="'+waypoints+'"}'+origin+'{/map}';
	}
	addPluginCode(pcode);
}

var googlemaps = [];

/* INITIALIZE GOOGLE MAPS */
function initGoogleMaps() {
	for (var idx = 1; idx < 11; idx++) {
		if (document.getElementById('googlemap'+idx)) {
			initGoogleMap(idx);
		} else {
			break;
		}
	}
}

/* INITIALIZE GOOGLE MAP */
function initGoogleMap(idx) {
	var myOptions = {
		zoom: mapcfg.mzoom,
		mapTypeControl: mapcfg.mtypecontrol,
		mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DEFAULT },
		zoomControl: mapcfg.mzoomcontrol,
		zoomControlOptions: { style: google.maps.ZoomControlStyle.DEFAULT, position: google.maps.ControlPosition.TOP_LEFT },
		navigationControl: mapcfg.mnavcontrol,
		navigationControlOptions: { style: google.maps.NavigationControlStyle.DEFAULT, position: google.maps.ControlPosition.RIGHT },
		scaleControl: mapcfg.mscale,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	if (mapcfg.mtypecontrol == true) {
		switch (mapcfg.mtypecontrolopts) {
			case 'DEFAULT': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.DEFAULT }; break;
			case 'HORIZONTAL_BAR': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR }; break;
			case 'DROPDOWN_MENU': myOptions.mapTypeControlOptions = { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU }; break;
			default: break;
		}
	}
	if (mapcfg.mzoomcontrol == true) {
		switch (mapcfg.mzoomcontrolopts) {
			case 'DEFAULT': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.DEFAULT, position: google.maps.ControlPosition.TOP_LEFT }; break;
			case 'SMALL': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.SMALL, position: google.maps.ControlPosition.TOP_LEFT }; break;
			case 'LARGE': myOptions.zoomControlOptions = { style: google.maps.ZoomControlStyle.LARGE, position: google.maps.ControlPosition.TOP_LEFT }; break;
			default: break;
		}
	}
	if (mapcfg.mnavcontrol == true) {
		switch (mapcfg.mnavcontrolopts) {
			case 'DEFAULT': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.DEFAULT, position: google.maps.ControlPosition.RIGHT }; break;
			case 'SMALL': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.SMALL, position: google.maps.ControlPosition.RIGHT }; break;
			case 'ANDROID': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.ANDROID, position: google.maps.ControlPosition.RIGHT }; break;
			case 'ZOOM_PAN': myOptions.navigationControlOptions = { style: google.maps.NavigationControlStyle.ZOOM_PAN, position: google.maps.ControlPosition.RIGHT }; break;
			default: break;
		}
	}
	switch (mapcfg.mnavcontrolopts) {
		case 'ROADMAP': myOptions.mapTypeId = google.maps.MapTypeId.ROADMAP; break;
		case 'SATELLITE': myOptions.mapTypeId = google.maps.MapTypeId.SATELLITE; break;
		case 'HYBRID': myOptions.mapTypeId = google.maps.MapTypeId.HYBRID; break;
		case 'TERRAIN': myOptions.mapTypeId = google.maps.MapTypeId.TERRAIN; break;
		default: break;
	}

	if (mapcfg.destination[idx] != '') {
		var directionsService = new google.maps.DirectionsService;
		var directionsDisplay = new google.maps.DirectionsRenderer;
	}

    if (mapcfg.multiple[idx] == 1) {
    	var prev_infowindow = false;
		var map = new google.maps.Map(document.getElementById('googlemap'+idx), myOptions);
		if (!getGoogleMap(idx)) {
			var mapInfo = { idx: idx, map: map, marker: [], infowindow: [] };
			googlemaps.push(mapInfo);
		}
    	var bounds = new google.maps.LatLngBounds();
    	var lats = mapcfg.lat[idx].split('|');
    	var longs = mapcfg.lng[idx].split('|');
    	var infos = mapcfg.info[idx].split('|');
		for (i = 0; i < lats.length; i++) {
			var mapIndex = getGoogleMap(idx);
			googlemaps[mapIndex].marker[i] = new google.maps.Marker({
				position: new google.maps.LatLng(lats[i], longs[i]),
				map: googlemaps[mapIndex].map,
				animation: google.maps.Animation.DROP
			});
	        bounds.extend(googlemaps[mapIndex].marker[i].position);
			google.maps.event.addListener(googlemaps[mapIndex].marker[i], 'click', (function(marker, i) {
				return function() {
					if (prev_infowindow) { prev_infowindow.close(); }
					infowindow = new google.maps.InfoWindow({ content: infos[i] });
					prev_infowindow = infowindow;
					infowindow.open(map, marker[i]);
				}
			})(googlemaps[mapIndex].marker, i));
			map.fitBounds(bounds);
		}
    } else {
		myOptions.center = new google.maps.LatLng(mapcfg.lat[idx],mapcfg.lng[idx]);

		var map = new google.maps.Map(document.getElementById('googlemap'+idx), myOptions);
		if (!getGoogleMap(idx)) {
			var mapInfo = { idx: idx, map: map, marker: null, infowindow: null };
			googlemaps.push(mapInfo);
		}
		placeMarkers(idx);

		var mapelem = document.getElementById('googlemap'+idx);
		mapelem.style.height = Math.floor(mapelem.offsetWidth * 0.56) +'px';

		google.maps.event.addDomListener(window, "resize", function() {
			var center = map.getCenter();
			google.maps.event.trigger(map, "resize");
			map.setCenter(center);
			mapelem = document.getElementById('googlemap'+idx);
			mapelem.style.height = Math.floor(mapelem.offsetWidth * 0.56) +'px';
		});

		if (mapcfg.destination[idx] != '') {
			var waypts = [];
			if (mapcfg.waypoints[idx] != '') {
				var points = mapcfg.waypoints[idx].split('|');
				for (var i = 0; i < points.length; i++) {
					waypts.push({ location: points[i], stopover: true });
				}
			}

			directionsDisplay.setMap(map);
			directionsService.route({
				origin: mapcfg.lat[idx]+','+mapcfg.lng[idx],
				destination: mapcfg.destination[idx],
				waypoints: waypts,
				optimizeWaypoints: true,
				travelMode: mapcfg.travelmode[idx]
			}, function(response, status) {
				directionsDisplay.setDirections(response);
			});
		}
    }
}

/* GETMAP INSTANCE */
function getGoogleMap(idx) {
	for (var i=0; i < googlemaps.length; i++) {
		if (googlemaps[i].idx == idx) { return i; }
	}
	return false;
}

/* PLACE MARKERS ON MAP */
function placeMarkers(idx) {
	var mapIndex = getGoogleMap(idx);
	if (mapIndex === false) { return; }

	googlemaps[mapIndex].marker = new google.maps.Marker({
		position: googlemaps[mapIndex].map.getCenter(),
		map: googlemaps[mapIndex].map,
		animation: google.maps.Animation.DROP
	});

	if (mapcfg.info[idx] != '') {
		googlemaps[mapIndex].infowindow = new google.maps.InfoWindow({ content: mapcfg.info[idx] });
		google.maps.event.addListener(googlemaps[mapIndex].marker, 'click', function() {
			googlemaps[mapIndex].infowindow.open(googlemaps[mapIndex].map, googlemaps[mapIndex].marker);
		});
	}
}