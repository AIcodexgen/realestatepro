(function($) {
    'use strict';
    
    // Gallery image upload
    $('#add-gallery-images').on('click', function(e) {
        e.preventDefault();
        
        var frame = wp.media({
            title: 'Select Images',
            multiple: true,
            library: {
                type: 'image'
            }
        });
        
        frame.on('select', function() {
            var attachments = frame.state().get('selection').toJSON();
            var $preview = $('#gallery-preview');
            
            attachments.forEach(function(attachment) {
                var html = '<div class="gallery-item" data-id="' + attachment.id + '">';
                html += '<img src="' + attachment.sizes.thumbnail.url + '">';
                html += '<button type="button" class="remove-image">×</button>';
                html += '<input type="hidden" name="property_gallery[]" value="' + attachment.id + '">';
                html += '</div>';
                
                $preview.append(html);
            });
        });
        
        frame.open();
    });
    
    // Remove gallery image
    $(document).on('click', '.remove-image', function() {
        $(this).parent('.gallery-item').remove();
    });
    
    // Initialize map if address field exists
    if ($('#property-address').length && typeof google !== 'undefined') {
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('property-address')
        );
        
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (place.geometry) {
                $('#property_latitude').val(place.geometry.location.lat());
                $('#property_longitude').val(place.geometry.location.lng());
            }
        });
    }
})(jQuery);// Admin scripts
