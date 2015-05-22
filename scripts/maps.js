var x = 10;
var y = 13;
var pos = new google.maps.LatLng(x, y);
getLocation();
function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        pos = new google.maps.LatLng(position.coords.latitude,
                                         position.coords.longitude);
        x = position.coords.latitude;
        y = position.coords.longitude;
      });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

var map;

function initialize() {
  var mapOptions = {
    zoom: 14
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  // Try HTML5 geolocation
  if(navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var pos = new google.maps.LatLng(position.coords.latitude,
                                       position.coords.longitude);

      var infowindow = new google.maps.InfoWindow({
        content: 'Ti trovi qui.'
      });

      map.setCenter(pos);

      var marker = new google.maps.Marker({
        map: map,
        position: map.getCenter()
      });
      google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map, marker);
      });

    }, function() {
      handleNoGeolocation(true);
    });
  } else {
    // Browser doesn't support Geolocation
    handleNoGeolocation(false);
  }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag) {
    var content = 'Error: The Geolocation service failed.';
  } else {
    var content = 'Error: Your browser doesn\'t support geolocation.';
  }

  var options = {
    map: map,
    position: new google.maps.LatLng(60, 105),
    content: content
  };

  var infowindow = new google.maps.InfoWindow(options);
  map.setCenter(options.position);
}

google.maps.event.addDomListener(window, 'load', initialize);

// funzione chiamata in automatico quando il fornitore aggiunge una disponibilita'
function getPos_x(){
  return x;
}
function getPos_y(){
  return y;
}