google.load("maps", "3", {
    "callback"      :   googleMapsLoaded,
    "other_params"  :   "sensor=false"
});

/* ----- Google Map ----- */
function googleMapsLoaded()
{
    jQuery('.themico-google-map').each(function(){

        /* set your coordinates here */
        var latitude = jQuery(this).data('latitude');
        var longitude = jQuery(this).data('longitude');
        var myLatlng = new google.maps.LatLng(latitude, longitude);

        var mapOptions = {
                scrollwheel: false,
                zoom: 8,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        var map = new google.maps.Map(jQuery(this).get(0), mapOptions);

        var object_name = jQuery(this).attr('id');

        if (object_name !== undefined &&  object_name in window) {

            var themicoMarkerInfo = window[object_name];
            var image = new google.maps.MarkerImage(themicoMarkerInfo.image,
                    // This marker is 20 pixels wide by 32 pixels tall.
                    new google.maps.Size(61,60),
                    // The origin for this image is 0,0.
                    new google.maps.Point(0,0),
                    // The anchor for this image is the base of the flagpole at 0,32.
                    new google.maps.Point(30, 30));

            var infowindow = new google.maps.InfoWindow({
                content: themicoMarkerInfo.content
            });

            var marker = new google.maps.Marker({
                    position: myLatlng,
                    icon: image,
                    title: themicoMarkerInfo.title
            });

            google.maps.event.addListener(marker, 'click', function() {
                    infowindow.open(map,marker);
            });

            // To add the marker to the map, call setMap();
            marker.setMap(map);
        }


    });

}
