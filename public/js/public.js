(function($) {
    'use strict';
    
    var RealEstatePro = {
        init: function() {
            this.initFavorites();
            this.initCompare();
            this.initSearch();
            this.initMaps();
            this.initSubmission();
        },
        
        initFavorites: function() {
            $(document).on('click', '.add-to-favorites', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var propertyId = $btn.data('id');
                
                $.ajax({
                    url: realestatepro.rest_url + 'favorites/' + propertyId,
                    method: 'POST',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', realestatepro.nonce);
                    },
                    success: function(response) {
                        if (response.status === 'added') {
                            $btn.addClass('is-favorite').text(realestatepro.strings.remove_from_favorites);
                        } else {
                            $btn.removeClass('is-favorite').text(realestatepro.strings.add_to_favorites);
                        }
                    }
                });
            });
        },
        
        initCompare: function() {
            var compareList = JSON.parse(localStorage.getItem('realestatepro_compare') || '[]');
            
            $(document).on('click', '.add-to-compare', function(e) {
                e.preventDefault();
                var propertyId = $(this).data('id');
                
                if (compareList.indexOf(propertyId) === -1) {
                    if (compareList.length >= 4) {
                        alert('You can compare up to 4 properties');
                        return;
                    }
                    compareList.push(propertyId);
                } else {
                    compareList = compareList.filter(function(id) {
                        return id !== propertyId;
                    });
                }
                
                localStorage.setItem('realestatepro_compare', JSON.stringify(compareList));
                $(this).toggleClass('in-compare');
            });
        },
        
        initSearch: function() {
            var self = this;
            
            // Advanced search form AJAX
            $('#realestatepro-search-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $results = $('#search-results');
                
                $.ajax({
                    url: realestatepro.rest_url + 'properties',
                    data: $form.serialize(),
                    success: function(response) {
                        $results.html(self.renderProperties(response.properties));
                        self.updateMapMarkers(response.properties);
                    }
                });
            });
            
            // Initialize location autocomplete
            if ($('#location-autocomplete').length && typeof google !== 'undefined') {
                var autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('location-autocomplete'),
                    { types: ['(cities)'] }
                );
            }
        },
        
        initMaps: function() {
            $('.realestatepro-map').each(function() {
                var $map = $(this);
                var lat = parseFloat($map.data('lat')) || 40.7128;
                var lng = parseFloat($map.data('lng')) || -74.0060;
                var properties = $map.data('properties') || [];
                
                var map = new google.maps.Map($map[0], {
                    center: { lat: lat, lng: lng },
                    zoom: 12
                });
                
                var markers = [];
                properties.forEach(function(prop) {
                    if (prop.latitude && prop.longitude) {
                        var marker = new google.maps.Marker({
                            position: { lat: parseFloat(prop.latitude), lng: parseFloat(prop.longitude) },
                            map: map,
                            title: prop.title
                        });
                        markers.push(marker);
                    }
                });
                
                if (markers.length > 1) {
                    var bounds = new google.maps.LatLngBounds();
                    markers.forEach(function(m) { bounds.extend(m.getPosition()); });
                    map.fitBounds(bounds);
                }
            });
        },
        
        initSubmission: function() {
            $('#property-submission-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var formData = $form.serialize();
                
                $.ajax({
                    url: realestatepro.rest_url + 'property',
                    method: 'POST',
                    data: JSON.stringify({
                        title: $form.find('[name="title"]').val(),
                        description: $form.find('[name="description"]').val(),
                        price: $form.find('[name="price"]').val(),
                        // ... other fields
                    }),
                    contentType: 'application/json',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', realestatepro.nonce);
                    },
                    success: function(response) {
                        alert(response.message);
                        window.location.reload();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseJSON.message);
                    }
                });
            });
        },
        
        renderProperties: function(properties) {
            var html = '<div class="properties-grid">';
            properties.forEach(function(prop) {
                html += '<div class="property-card">';
                html += '<img src="' + (prop.thumbnail || '') + '" alt="' + prop.title + '">';
                html += '<h3>' + prop.title + '</h3>';
                html += '<div class="price">' + prop.price + '</div>';
                html += '<div class="details">';
                html += '<span>' + prop.bedrooms + ' Beds</span>';
                html += '<span>' + prop.bathrooms + ' Baths</span>';
                html += '<span>' + prop.size + ' sqft</span>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
            return html;
        },
        
        updateMapMarkers: function(properties) {
            // Update map markers based on search results
        }
    };
    
    $(document).ready(function() {
        RealEstatePro.init();
    });
    
})(jQuery);
