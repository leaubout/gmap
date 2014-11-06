
$(function() {
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
    
    $('#datafile').on('change', function(e) {
		var data = null;
		var file = e.target.files[0];
		var reader = new FileReader();
		
		// Lecture du contenu du fichier
		reader.readAsText(file);
		reader.onload = function(e) {
			var dataCsv = e.target.result;	
			//console.log(dataCsv);
			// Envoi du contenu du fichier au script PHP
			$.post('ajax.php', { action: "upload", data: dataCsv})
				.done(function(result) {
					if( result == 0) {
						swal({   
							title: "Attention!",   
							text: "Aucune ligne ajoutée" ,   
							type: "warning" 
							});
					} else {
						
					var table = new $.fn.dataTable.Api('#adresses-table');
					table.ajax.reload();
					 
					swal({   
						title: "Ok!",   
						text: "Nouvelles lignes ajoutées : " + result ,   
						type: "success" 
						});
					}
				});
		};
    });
    
    $('#adresses-table').dataTable({
		ajax: 'ajax.php?action=loadAddresses',
	}).on('draw.dt', function() {
		$('a[data-action]').on('click', function(e) {
				e.preventDefault();
				var action = $(this).data('action');
				var id = $(this).data('id');
				if (action == "delete") {
					swal({
						title: "Êtes-vous certain ?",   
						text: "L'enregistrement sera définitivement supprimé",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Oui, supprimer !",
						closeOnConfirm: false 
					}, function(){   
						$.get('ajax.php?action=delete&id=' + id)
						.done(function() {
						 	var table = new $.fn.dataTable.Api('#adresses-table');
						 	table.ajax.reload();
						});
						swal("Supprimé!", "Cette adresse n'existe plus.", "success"); 
					});
					
				} else if (action == "edit") {
					$.getJSON('ajax.php?action=loadAddress&id=' + id)
					.done(function(result) {
					 	$("#nom").val(result.coords_nom);
					 	$("#description").val(result.coords_desc);
					 	$("#adresse").val(result.coords_adresse);
					 	$("#url").val(result.coords_url);
					 	$("#id").val(result.coords_id);
					});
					$('html, body').animate({
				        scrollTop: $("#add").offset().top
				    }, 500);
				}
			});
	});
    
    $('#submit').on('click', function(e){
		e.preventDefault();
		$.post('ajax.php', $('#form1').serialize())
		.done(function() {
			var table = new $.fn.dataTable.Api('#adresses-table');
			table.ajax.reload();
			$("#nom").val("");
		 	$("#description").val("");
		 	$("#adresse").val("");
		 	$("#url").val("");
		 	$("#id").val("");
			$('html, body').animate({
		        scrollTop: $("#addrtitle").offset().top
		    }, 500);
		});
	});
    

 // Google Maps Scripts
 // When the window has finished loading create our google map below
 google.maps.event.addDomListener(window, 'load', init);

 function init() {
     // Basic options for a simple Google Map
     // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
     var mapOptions = {
         // How zoomed in you want the map to start at (always required)
         zoom: 13,

         // The latitude and longitude to center the map (always required)
         center: new google.maps.LatLng(45.76, 4.84), // Lyon

         // Disables the default Google Maps UI components
         disableDefaultUI: true,
         scrollwheel: true,
         draggable: true,

         // How you would like to style the map. 
         // This is where you would paste any style found on Snazzy Maps.
         styles: [{
             "featureType": "water",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 17
             }]
         }, {
             "featureType": "landscape",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 20
             }]
         }, {
             "featureType": "road.highway",
             "elementType": "geometry.fill",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 17
             }]
         }, {
             "featureType": "road.highway",
             "elementType": "geometry.stroke",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 29
             }, {
                 "weight": 0.2
             }]
         }, {
             "featureType": "road.arterial",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 18
             }]
         }, {
             "featureType": "road.local",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 16
             }]
         }, {
             "featureType": "poi",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 21
             }]
         }, {
             "elementType": "labels.text.stroke",
             "stylers": [{
                 "visibility": "on"
             }, {
                 "color": "#000000"
             }, {
                 "lightness": 16
             }]
         }, {
             "elementType": "labels.text.fill",
             "stylers": [{
                 "saturation": 36
             }, {
                 "color": "#000000"
             }, {
                 "lightness": 40
             }]
         }, {
             "elementType": "labels.icon",
             "stylers": [{
                 "visibility": "off"
             }]
         }, {
             "featureType": "transit",
             "elementType": "geometry",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 19
             }]
         }, {
             "featureType": "administrative",
             "elementType": "geometry.fill",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 20
             }]
         }, {
             "featureType": "administrative",
             "elementType": "geometry.stroke",
             "stylers": [{
                 "color": "#000000"
             }, {
                 "lightness": 17
             }, {
                 "weight": 1.2
             }]
         }]
     };

     // Get the HTML DOM element that will contain your map 
     // We are using a div with id="map" seen below in the <body>
     var mapElement = document.getElementById('map');

     // Create the Google Map using out element and options defined above
     var map = new google.maps.Map(mapElement, mapOptions);

     // Custom Map Marker Icon - Customize the map-marker.png file to customize your icon
     var image = 'img/map-marker.png';

     return mapOptions;
     
 }

 $('#adresses-table').on('draw.dt', function() {
 	
 	$.getJSON('ajax.php?action=loadAddresses')
 	.done(function(result) {
 	
 		var mapOptions = init();
 	    var mapElement = document.getElementById('map');
 	    var map = new google.maps.Map(mapElement, mapOptions);
 	    var image = 'img/map-marker.png';
 	    
 		var adresses = result.data; 
 		$.each(adresses, function(index, value) {
 			
 			geo = new google.maps.Geocoder();
 			geo.geocode({address : value[2]}, function(result, status) {
 			    var myLatLng = new google.maps.LatLng(
 			    		result[0].geometry.location.k,
 			    		result[0].geometry.location.B
 			    );
 			
 			
 			    var beachMarker = new google.maps.Marker({
 				        position: myLatLng,
 				        map: map,
 				        icon: image,
 				        title: value[0]
 			    });
 				
 				var contentString = '<div id="content" class="infoWindow">' + 
 					    '<div id="siteNotice"></div>' + 
 					    '<h1>' + value[0] + '</h1>' +
 					    '<p>' + value[1] + '</p>' +
 					    '<p><a href="' + value[3] + '" target="_blank">' + value[3] + '</a></p>' +
 					    '</div>';
 			    
 			    var infowindow = new google.maps.InfoWindow({
 				    	content : contentString
 				});
 				
 				google.maps.event.addListener(beachMarker, 'click', function() {
 				 		infowindow.open(map,beachMarker);
 				});
 			});
 		});
 	});
 });
 
});

