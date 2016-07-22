@extends('layouts.app')

@section('sidebar')
  <center>
    <h2>No place selected</h2>
  </center>
@endsection

@section('content')
  <style type="text/css">
    html, body { 
      height: 100%; 
      margin: 0; 
      padding: 0; 
    }
    #map { 
      height: 50%;
      width: 50%;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
  <div id="map"></div>
  <body>
    <script>
      var map;
      function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: -34.397, lng: 150.644},
          zoom: 15,
          mapTypeControl: false,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        if (navigator.geolocation) {
           navigator.geolocation.getCurrentPosition(function (position) {
               initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
               map.setCenter(initialLocation);
           });
        }
        map.data.loadGeoJson("http://localhost:8888/Project-Ewok/public/api/geolocations");
        map.data.addListener('click', function(event) {
          var html = "<center><p>" + 
          event.feature.getProperty("locationType") + "<br>" +
          "Name: " + event.feature.getProperty("name") + "<br>" +
          "Time of operation: " + event.feature.getProperty("timeOfOperation") +
          "</p></center>";
          document.getElementById("sidebar").innerHTML = html;
        });
        map.data.setStyle(function(feature){
          if(feature.getProperty('locationType') == 'farm'){
            return {
              icon: {
                url: "../resources/pictures/FarmIcon.jpg", // url
                scaledSize: new google.maps.Size(25, 25), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
              }          
            };
          }
        });
      }  
    </script>
  </body>
@endsection