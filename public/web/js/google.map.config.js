/*
(function($){
	$(document).ready(function(){

		// Google Maps
		//-----------------------------------------------
		if ($("#map-canvas").length>0) {
			var map, myLatlng, myZoom, marker;
			// Set the coordinates of your location
			myLatlng = new google.maps.LatLng(41.38791700, 2.16991870);
			myZoom = 12;
			function initialize() {
				var mapOptions = {
					zoom: myZoom,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: myLatlng,
					scrollwheel: false
				};
				map = new google.maps.Map(document.getElementById("map-canvas"),mapOptions);
				marker = new google.maps.Marker({
					map:map,
					draggable:true,
					animation: google.maps.Animation.DROP,
					position: myLatlng
				});
				google.maps.event.addDomListener(window, "resize", function() {
					map.setCenter(myLatlng);
				});
			}
			google.maps.event.addDomListener(window, "load", initialize);
		}
	}); // End document ready

})(this.jQuery);		
*/
(function($){
	$(document).ready(function(){
		var page_title = websiteName;
		var address = websiteAddress;
		var phone = websitePhone;
		var fax = websiteFax;
		var geocoder = new google.maps.Geocoder();
		var latitude = 0;
		var longitude = 0;
		var info = '<div style="line-height:20px;">';
		info += '<strong>' + page_title + '</strong><br/>';
		info += 'Address: ' + address + '<br/>';
		info += 'Phone: ' + phone + '<br/>';
		if (fax) {
			info += 'Fax: ' + fax + '<br/>';
		}
		info += '</div>';
		infowindow = new google.maps.InfoWindow({
			content: ''
		});
		geocoder.geocode({'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				latitude=results[0].geometry.location.lat();
				longitude=results[0].geometry.location.lng();
				var latLng = new google.maps.LatLng(latitude, longitude);
				var map = new google.maps.Map(document.getElementById('map-canvas'), {
					zoom: 14,
					center: latLng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				var marker = new google.maps.Marker({
					"info": info,
					position: latLng,
					title: address,
					map: map,
					draggable: true					
				});
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.setContent(this.info)
					infowindow.open(map, this);
				});			
			} else {
				document.getElementById('map-canvas').innerHTML('');
			}
		});    
	}); // End document ready
})(this.jQuery);