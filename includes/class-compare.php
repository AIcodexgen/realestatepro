<?php
namespace RealEstatePro;

class Compare {
    public function __construct() {
        add_action('wp_ajax_realestatepro_get_compare', [$this, 'get_compare_properties']);
        add_action('wp_ajax_nopriv_realestatepro_get_compare', [$this, 'get_compare_properties']);
        add_shortcode('realestatepro_compare', [$this, 'compare_shortcode']);
    }
    
    public function get_compare_properties() {
        $ids = array_map('intval', $_POST['property_ids']);
        
        $properties = [];
        foreach ($ids as $id) {
            if (get_post_type($id) === 'property') {
                $properties[] = [
                    'id' => $id,
                    'title' => get_the_title($id),
                    'price' => get_post_meta($id, '_property_price', true),
                    'bedrooms' => get_post_meta($id, '_property_bedrooms', true),
                    'bathrooms' => get_post_meta($id, '_property_bathrooms', true),
                    'size' => get_post_meta($id, '_property_size', true),
                    'address' => get_post_meta($id, '_property_address', true),
                    'image' => get_the_post_thumbnail_url($id, 'medium'),
                    'type' => wp_get_post_terms($id, 'property_type', ['fields' => 'names'])[0] ?? ''
                ];
            }
        }
        
        wp_send_json_success($properties);
    }
    
    public function compare_shortcode() {
        ob_start();
        ?>
        <div id="property-compare-container">
            <div class="compare-notice" style="display:none;">
                <p><?php _e('Select up to 4 properties to compare', 'realestatepro'); ?></p>
            </div>
            <div class="compare-grid" id="compare-grid">
                <!-- Populated via JS -->
            </div>
        </div>
        <script>
        (function($) {
            var compareIds = JSON.parse(localStorage.getItem('realestatepro_compare') || '[]');
            
            function loadCompare() {
                if (compareIds.length === 0) {
                    $('#compare-grid').html('<p><?php _e("No properties selected for comparison.", "realestatepro"); ?></p>');
                    return;
                }
                
                $.ajax({
                    url: realestatepro.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'realestatepro_get_compare',
                        property_ids: compareIds
                    },
                    success: function(response) {
                        if (response.success) {
                            renderCompare(response.data);
                        }
                    }
                });
            }
            
            function renderCompare(properties) {
                var html = '<table class="compare-table"><thead><tr><th>Feature</th>';
                properties.forEach(function(prop) {
                    html += '<th>' + prop.title + '<button class="remove-compare" data-id="' + prop.id + '">×</button></th>';
                });
                html += '</tr></thead><tbody>';
                
                var fields = [
                    {key: 'image', label: 'Image', type: 'image'},
                    {key: 'price', label: 'Price'},
                    {key: 'type', label: 'Type'},
                    {key: 'bedrooms', label: 'Bedrooms'},
                    {key: 'bathrooms', label: 'Bathrooms'},
                    {key: 'size', label: 'Size'},
                    {key: 'address', label: 'Address'}
                ];
                
                fields.forEach(function(field) {
                    html += '<tr><td>' + field.label + '</td>';
                    properties.forEach(function(prop) {
                        var val = prop[field.key];
                        if (field.type === 'image') {
                            val = '<img src="' + val + '" style="max-width:200px;">';
                        }
                        html += '<td>' + (val || '-') + '</td>';
                    });
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                $('#compare-grid').html(html);
            }
            
            $(document).on('click', '.remove-compare', function() {
                var id = $(this).data('id');
                compareIds = compareIds.filter(function(cid) { return cid != id; });
                localStorage.setItem('realestatepro_compare', JSON.stringify(compareIds));
                loadCompare();
            });
            
            loadCompare();
        })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }
}
