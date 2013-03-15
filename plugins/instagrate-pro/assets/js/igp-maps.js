jQuery(document).ready(function($) {

	    $maps = $('.map_canvas');
	    $maps.each(function(index, Element) {
	    
 	    var map;
	    var geocoder;
	    var marker;
	    var infowindow;
	  
		var lat = $(Element).attr("data-lat");
		var lon = $(Element).attr("data-lon");
		var markertitle = $(Element).attr("data-marker");
		var mapstyle = $(Element).attr("data-style");	
	    var myLatlng = new google.maps.LatLng(lat, lon);
	    var myOptions = {	zoom: 14,
								center: myLatlng,
								mapTypeId: google.maps.MapTypeId[mapstyle]
							};
	        
	    map = new google.maps.Map(Element, myOptions);
	    marker = new google.maps.Marker({
	        map: map,
	        position: myLatlng,
	        title: markertitle
	    });
	    google.maps.event.addListener(marker, 'load', function() {
	        infowindow.open(map, marker);
	    });
     
    });
    
});

