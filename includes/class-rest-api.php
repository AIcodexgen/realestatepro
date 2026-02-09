<?php
namespace RealEstatePro;

class REST_API {
    private $namespace = 'realestatepro/v1';
    
    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }
    
    public function register_routes() {
        // Search Properties
        register_rest_route($this->namespace, '/properties', [
            'methods' => 'GET',
            'callback' => [$this, 'get_properties'],
            'permission_callback' => '__return_true',
            'args' => [
                'page' => ['default' => 1, 'sanitize_callback' => 'intval'],
                'per_page' => ['default' => 12, 'sanitize_callback' => 'intval'],
                'type' => ['default' => ''],
                'status' => ['default' => ''],
                'min_price' => ['default' => 0],
                'max_price' => ['default' => 0],
                'bedrooms' => ['default' => 0],
                'bathrooms' => ['default' => 0],
                'city' => ['default' => ''],
                'keyword' => ['default' => ''],
                'featured' => ['default' => false],
                'lat' => ['default' => ''],
                'lng' => ['default' => ''],
                'radius' => ['default' => 50],
            ],
        ]);
        
        // Favorite Property
        register_rest_route($this->namespace, '/favorites/(?P<id>\d+)', [
            'methods' => 'POST',
            'callback' => [$this, 'toggle_favorite'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        
        // Compare Properties
        register_rest_route($this->namespace, '/compare', [
            'methods' => 'POST',
            'callback' => [$this, 'add_to_compare'],
            'permission_callback' => '__return_true',
        ]);
        
        // Submit Property (Frontend)
        register_rest_route($this->namespace, '/property', [
            'methods' => 'POST',
            'callback' => [$this, 'submit_property'],
            'permission_callback' => [$this, 'check_can_submit'],
        ]);
        
        // Geocode Address
        register_rest_route($this->namespace, '/geocode', [
            'methods' => 'GET',
            'callback' => [$this, 'geocode_address'],
            'permission_callback' => '__return_true',
            'args' => [
                'address' => ['required' => true],
            ],
        ]);
    }
    
    public function get_properties($request) {
        $args = [
            'post_type' => 'property',
            'post_status' => 'publish',
            'posts_per_page' => $request['per_page'],
            'paged' => $request['page'],
        ];
        
        $meta_query = [];
        $tax_query = [];
        
        if ($request['type']) {
            $tax_query[] = [
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => $request['type'],
            ];
        }
        
        if ($request['status']) {
            $tax_query[] = [
                'taxonomy' => 'property_status',
                'field' => 'slug',
                'terms' => $request['status'],
            ];
        }
        
        if ($request['city']) {
            $tax_query[] = [
                'taxonomy' => 'property_city',
                'field' => 'slug',
                'terms' => $request['city'],
            ];
        }
        
        if ($request['min_price']) {
            $meta_query[] = [
                'key' => '_property_price',
                'value' => $request['min_price'],
                'type' => 'NUMERIC',
                'compare' => '>=',
            ];
        }
        
        if ($request['max_price']) {
            $meta_query[] = [
                'key' => '_property_price',
                'value' => $request['max_price'],
                'type' => 'NUMERIC',
                'compare' => '<=',
            ];
        }
        
        if ($request['bedrooms']) {
            $meta_query[] = [
                'key' => '_property_bedrooms',
                'value' => $request['bedrooms'],
                'type' => 'NUMERIC',
                'compare' => '>=',
            ];
        }
        
        if ($request['bathrooms']) {
            $meta_query[] = [
                'key' => '_property_bathrooms',
                'value' => $request['bathrooms'],
                'type' => 'NUMERIC',
                'compare' => '>=',
            ];
        }
        
        if ($request['featured']) {
            $meta_query[] = [
                'key' => '_property_featured',
                'value' => '1',
            ];
        }
        
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }
        
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        
        if ($request['keyword']) {
            $args['s'] = sanitize_text_field($request['keyword']);
        }
        
        // Geolocation search
        if ($request['lat'] && $request['lng'] && $request['radius']) {
            $args = $this->add_geo_query($args, $request['lat'], $request['lng'], $request['radius']);
        }
        
        $query = new \WP_Query($args);
        $properties = [];
        
        while ($query->have_posts()) {
            $query->the_post();
            $properties[] = $this->format_property(get_the_ID());
        }
        wp_reset_postdata();
        
        return rest_ensure_response([
            'properties' => $properties,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ]);
    }
    
    private function add_geo_query($args, $lat, $lng, $radius) {
        global $wpdb;
        
        $earth_radius = 3959; // miles, use 6371 for km
        
        $sql = $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} 
            WHERE meta_key = '_property_latitude' 
            AND ( 6371 * acos( cos( radians(%s) ) * cos( radians( meta_value ) ) * cos( radians( (SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = p.post_id AND meta_key = '_property_longitude') ) - radians(%s) ) + sin( radians(%s) ) * sin( radians( meta_value ) ) ) ) <= %s",
            $lat, $lng, $lat, $radius
        );
        
        $post_ids = $wpdb->get_col($sql);
        
        if (!empty($post_ids)) {
            $args['post__in'] = $post_ids;
        }
        
        return $args;
    }
    
    private function format_property($post_id) {
        $gallery = get_post_meta($post_id, '_property_gallery', true) ?: [];
        $images = [];
        foreach ($gallery as $image_id) {
            $images[] = wp_get_attachment_url($image_id);
        }
        
        return [
            'id' => $post_id,
            'title' => get_the_title($post_id),
            'permalink' => get_permalink($post_id),
            'thumbnail' => get_the_post_thumbnail_url($post_id, 'medium'),
            'gallery' => $images,
            'price' => $this->format_price(get_post_meta($post_id, '_property_price', true)),
            'bedrooms' => get_post_meta($post_id, '_property_bedrooms', true),
            'bathrooms' => get_post_meta($post_id, '_property_bathrooms', true),
            'size' => get_post_meta($post_id, '_property_size', true),
            'address' => get_post_meta($post_id, '_property_address', true),
            'latitude' => get_post_meta($post_id, '_property_latitude', true),
            'longitude' => get_post_meta($post_id, '_property_longitude', true),
            'type' => wp_get_post_terms($post_id, 'property_type', ['fields' => 'names']),
            'status' => wp_get_post_terms($post_id, 'property_status', ['fields' => 'names']),
            'features' => wp_get_post_terms($post_id, 'property_feature', ['fields' => 'names']),
            'featured' => get_post_meta($post_id, '_property_featured', true) === '1',
        ];
    }
    
    private function format_price($price) {
        $currency = get_option('realestatepro_currency', 'USD');
        $position = get_option('realestatepro_currency_position', 'before');
        $symbol = $this->get_currency_symbol($currency);
        
        if ($position === 'before') {
            return $symbol . number_format($price);
        }
        return number_format($price) . $symbol;
    }
    
    private function get_currency_symbol($currency) {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
        ];
        return $symbols[$currency] ?? $currency;
    }
    
    public function toggle_favorite($request) {
        $user_id = get_current_user_id();
        $property_id = $request['id'];
        $favorites = get_user_meta($user_id, '_favorite_properties', true) ?: [];
        
        if (in_array($property_id, $favorites)) {
            $favorites = array_diff($favorites, [$property_id]);
            $status = 'removed';
        } else {
            $favorites[] = $property_id;
            $status = 'added';
        }
        
        update_user_meta($user_id, '_favorite_properties', $favorites);
        
        return rest_ensure_response(['status' => $status, 'favorites' => $favorites]);
    }
    
    public function check_logged_in() {
        return is_user_logged_in();
    }
    
    public function check_can_submit() {
        return is_user_logged_in() && current_user_can('edit_posts');
    }
    
    public function submit_property($request) {
        // Verify nonce
        $nonce = $request->get_header('X_WP_Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new \WP_Error('invalid_nonce', __('Invalid security token', 'realestatepro'), ['status' => 403]);
        }
        
        $params = $request->get_json_params();
        
        $post_data = [
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['description']),
            'post_type' => 'property',
            'post_status' => get_option('realestatepro_submission_moderation', 1) ? 'pending' : 'publish',
            'post_author' => get_current_user_id(),
        ];
        
        $post_id = wp_insert_post($post_data, true);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Set taxonomy terms
        if (!empty($params['type'])) {
            wp_set_object_terms($post_id, intval($params['type']), 'property_type');
        }
        
        if (!empty($params['status'])) {
            wp_set_object_terms($post_id, intval($params['status']), 'property_status');
        }
        
        // Save meta fields
        update_post_meta($post_id, '_property_price', floatval($params['price']));
        update_post_meta($post_id, '_property_bedrooms', intval($params['bedrooms']));
        update_post_meta($post_id, '_property_bathrooms', floatval($params['bathrooms']));
        update_post_meta($post_id, '_property_size', floatval($params['size']));
        update_post_meta($post_id, '_property_address', sanitize_text_field($params['address']));
        
        return rest_ensure_response([
            'success' => true,
            'property_id' => $post_id,
            'message' => __('Property submitted successfully', 'realestatepro'),
        ]);
    }
    
    public function geocode_address($request) {
        $address = urlencode($request['address']);
        $api_key = get_option('realestatepro_google_maps_api_key');
        
        if (empty($api_key)) {
            return new \WP_Error('no_api_key', __('Google Maps API key not configured', 'realestatepro'), ['status' => 500]);
        }
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$api_key}";
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($body['status'] !== 'OK') {
            return new \WP_Error('geocode_failed', $body['status'], ['status' => 400]);
        }
        
        $location = $body['results'][0]['geometry']['location'];
        
        return rest_ensure_response([
            'latitude' => $location['lat'],
            'longitude' => $location['lng'],
            'formatted_address' => $body['results'][0]['formatted_address'],
        ]);
    }
}
