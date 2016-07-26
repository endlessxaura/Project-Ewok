@extends('layouts.app')

@section('sidebar')
  <center>
    <div id="sidebarInfo">
      <h2>No place selected</h2>
    </div>
    <div id="sidebarForm">    
      @if (JWTAuth::getToken() == null)
        Login to create a new place!
      @else
        <form id="creationForm" onsubmit="return false">
          <select>
            <option value="farm" name="locationType">Farm</option>
          </select><br>
          <input type="text" value="Name" name="name"><br>
          Opening time: <input type="time" name="openingTime"><br>
          Closing time: <input type="time" class="lastInput" name="closingTime"><br>
          <input type="submit">
        </form>
      @endif
    </div>
  </center>
  <script>
    @if (JWTAuth::getToken() == null)
    @else
    $("#creationForm").submit(function(event){
      var data = $("#creationForm").serializeArray();
      // var geolocationData = [];
      // var farmData = [];
      var geolocationData = "{";
      var farmData = "{";
      for(var i = 0; i < data.length; i++){
        if(data[i].name == "latitude" || data[i].name == "longitude" || data[i].name == "locationType"){
          // geolocationData.push(data[i]);
          geolocationData += '"' + data[i].name + '":' data[i].value + ",";
        }
        else{
          // farmData.push(data[i]);
          farmData += '"' + data[i].name + '":' data[i].value + ",";
        }
      }
      geolocationData += "}";
      farmData += "}";
      console.log(data);
      console.log(geolocationData);
      console.log(farmData);
      $.ajax({
        type: "POST",
        url: "{{url('api/geolocations')}}",
        contentType: 'application/json',
        headers: {
          Authorization: "Bearer {{JWTAuth::getToken()}}"
        },
        data: JSON.stringify(geolocationData)
      });
    });
    @endif
  </script>
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
      $("#sidebarForm").hide();
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
          event.feature.getProperty("name") + "<br>" +
          "Opening Time: " + event.feature.getProperty("openingTime") + "<br>" +
          "Closing Time: " + event.feature.getProperty("closingTime") + "<br>" +
          "</p></center>";
          document.getElementById("sidebarInfo").innerHTML = html;
          $("#sidebarForm").hide();
          $("#sidebarInfo").show();
        });
        map.addListener("click", function(event){
          var html = 
            "There is no place registered at latitude " + event.latLng.lat() +
            " and longitude " + event.latLng.lng() + ". Would you like to register one?<br>";
          var hiddenInput = 
            '<input type="hidden" name="latitude" value=' + event.latLng.lat() + ">" +
            '<input type="hidden" name="longitude" value=' + event.latLng.lng() + ">";
          $("#sidebarForm").prepend(html);
          $(".lastInput").after(hiddenInput);
          $("#sidebarForm").show();
          $("#sidebarInfo").hide();
        });
        map.data.setStyle(function(feature){
          if(feature.getProperty('locationType') == 'farm'){
            return {
              icon: {
                url: "../resources/pictures/icons/FarmIcon.jpg", // url
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