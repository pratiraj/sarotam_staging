    var placeSearch, autocomplete, geocoder;

    function initAutocomplete() {
      geocoder = new google.maps.Geocoder();
      autocomplete = new google.maps.places.Autocomplete(
          (document.getElementById('autocomplete'))/*,
          {types: ['(cities)']}*/);

      autocomplete.addListener('place_changed', fillInAddress);
    }

    function codeAddress(address) {
        geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == 'OK') {
            alert(results[0].geometry.location);
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }

    function fillInAddress() {
      var place = autocomplete.getPlace();
      alert(place.place_id);
      codeAddress(document.getElementById('autocomplete').value);
    }
