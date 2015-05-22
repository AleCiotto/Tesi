
function codeAddress(address, id_fornitore) {
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {

      var lat = results[0].geometry.location.lat();
      var lng = results[0].geometry.location.lng();
      var result = new Array(lat, lng);

      query_set_disponibile(id_fornitore, lat, lng);
      return true;

    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}

function codeAddressCerca(my_id, posizione, artigiano, distanza, rating, indirizzo) {
  console.log(indirizzo);
  var geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address': indirizzo}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {

      var lat = results[0].geometry.location.lat();
      var lng = results[0].geometry.location.lng();

      query_cerca(my_id, posizione, artigiano, distanza, rating, lat, lng);

    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}