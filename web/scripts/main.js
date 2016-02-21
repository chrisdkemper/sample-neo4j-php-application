$(function() {

    function onToMarkerClick(e) {
        App.to_lat = e.target._latlng.lat;
        App.to_lon = e.target._latlng.lng;

        var popupContent = $('.to-marker').parent().html();
        e.target
            .setPopupContent(popupContent)
            .openPopup()
    }

    function onMarkerClick(e) {
        App.marker_latlon = e.target._latlng;

        if(true == App.journey_started) {
            $('.plan-closest').hide();
            $('.reset-start').show();

            e.target.setPopupContent($('.plugin').parent().html());
        }
    }

    function onLocationMarkerClick(e) {
        var point = App.points[ e.target._leaflet_id ];
        var panel = $('.info-panel .location-panel');
        var journey_panel = $('.info-panel .journey-panel');
        journey_panel.hide();

        $('.location-content', panel).html('');
        panel.show();

        var html = '';
        html += "<h2>" + point.name + "</h2>";
        $('.location-content', panel).html(html);
    }

    $(document).on('click', '.plugin .find-closest', function(){
        var data = {
            lat : App.marker_latlon.lat,
            lon : App.marker_latlon.lng
        };

        var location_panel = $('.info-panel .location-panel');
        location_panel.hide();

        var panel = $('.info-panel .journey-panel');
        $('.journey-content', panel).html('');

        $.post("/journey/closest", data)
            .done(function( data ) {
                var html = '';
                panel.show();
                html += "<h2>" + data.name + "</h2>";

                if(data.properties.length > 0) {
                    html += "<ul>";

                    $.each(data.properties, function(key, value){
                        html += "<li><strong>" + key + ":</strong> " + value + "</li>";
                    });

                    html += "</ul>";
                }

                $('.journey-content', panel).html(html);
                panel.show();
            })
        ;
    });

    $(document).on('click', '.plugin .plan-closest', function(){
        App.from_lat = App.marker_latlon.lat;
        App.from_lon = App.marker_latlon.lng;
        App.journey_started = true;

        $('.plan-closest').hide();
        $('.reset-start').show();

        var options = {
            icon : App.createIcon('end'),
            draggable : true
        };

        var popupContent = $('.plugin').parent().html();

        var marker = L.marker([App.marker_latlon.lat, App.marker_latlon.lng], options)
            .bindPopup(popupContent)
            .addTo(App.map)
        ;

        marker.on('click', onToMarkerClick);
    });

    $(document).on('click', '.plugin .reset-start', function(){
        App.from_lat = App.marker_latlon.lat;
        App.from_lon = App.marker_latlon.lng;
    });

    $(document).on('click', '.to-marker .plan-to', function(){
        var data = {
            from : {
                lat : App.from_lat,
                lon : App.from_lon
            },
            to : {
                lat : App.to_lat,
                lon : App.to_lon
            }
        };

        var location_panel = $('.info-panel .location-panel');
        location_panel.hide();

        var journey_panel = $('.info-panel .journey-panel');

        $.post("/journey/plan", data)
            .done(function( data ) {
                var html = '';

                $('.journey-content', journey_panel).html(html);

                if(data.content != undefined) {
                    html += "<p>" + data.content + "</p>";
                }

                if(data.results.length > 0) {
                    $.each(data.results, function(index, location){
                        html += '<h3>Step ' + (index + 1) + '</h3>';
                        html += "<p>Get a <strong>" + location.transport.type
                             + "</strong> at <strong>" + location.start_place.name
                             + "</strong> to <strong>" + location.end_place.name + "</strong></p>";
                        html += "<p>It should take around <strong>" + location.time + "</strong> mins";
                    });
                }

                $('.journey-content', journey_panel).html(html);
                journey_panel.show();
            })
        ;
    });

    var App = {
        map : L.map('map').setView([51.505, -0.09], 13),
        points : {},
        journey_started : false,
        init : function() {
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(App.map);

            var options = {
                icon : App.createIcon('start'),
                draggable : true
            };

            var marker = L.marker([51.505, -0.09], options)
                .addTo(App.map);

            var popupContent = $('.plugin').parent().html();

            marker
                .on('click', onMarkerClick)
                .bindPopup(popupContent)
            ;

            App.points();
        },
        points : function() {
            $.get('/journey/points', function(points) {
                $.each(points, function(index, point){
                    var label = point.label[1];

                    if(point.label.length > 2) {
                        label = 'default';
                    }

                    //Make an icon for the label
                    var options = {
                        icon : App.createIcon(label)
                    };

                    var marker = L.marker([point.lat, point.lon], options)
                        .addTo(App.map);

                    App.points[ marker._leaflet_id ] = point;

                    marker.on('click', onLocationMarkerClick);
                });
            });
        },
        createIcon : function(name) {
            var icon = L.icon({
                iconUrl: '/img/icon/icon-' + name + '.png',
                iconSize: [48, 63],
                iconAnchor: [22, 94],
                popupAnchor: [-3, -76],
                shadowSize: [68, 95],
                shadowAnchor: [22, 94]
            });

            return icon;
        }

    };

    App.init();

});