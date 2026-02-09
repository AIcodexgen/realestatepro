<?php
namespace RealEstatePro;

class Taxonomies {
    public function __construct() {
        add_action('init', [$this, 'register']);
    }
    
    public function register() {
        // Property Type
        register_taxonomy('property_type', ['property'], [
            'labels' => [
                'name' => __('Property Types', 'realestatepro'),
                'singular_name' => __('Property Type', 'realestatepro'),
            ],
            'hierarchical' => true,
            'public' => true,
            'rewrite' => ['slug' => 'property-type'],
            'show_in_rest' => true,
        ]);
        
        // Property Status
        register_taxonomy('property_status', ['property'], [
            'labels' => [
                'name' => __('Status', 'realestatepro'),
                'singular_name' => __('Status', 'realestatepro'),
            ],
            'hierarchical' => true,
            'public' => true,
            'rewrite' => ['slug' => 'status'],
            'show_in_rest' => true,
        ]);
        
        // Features
        register_taxonomy('property_feature', ['property'], [
            'labels' => [
                'name' => __('Features', 'realestatepro'),
                'singular_name' => __('Feature', 'realestatepro'),
            ],
            'hierarchical' => false,
            'public' => true,
            'rewrite' => ['slug' => 'feature'],
            'show_in_rest' => true,
        ]);
        
        // City
        register_taxonomy('property_city', ['property'], [
            'labels' => [
                'name' => __('Cities', 'realestatepro'),
                'singular_name' => __('City', 'realestatepro'),
            ],
            'hierarchical' => true,
            'public' => true,
            'rewrite' => ['slug' => 'city'],
            'show_in_rest' => true,
        ]);
        
        // Neighborhood
        register_taxonomy('property_neighborhood', ['property'], [
            'labels' => [
                'name' => __('Neighborhoods', 'realestatepro'),
                'singular_name' => __('Neighborhood', 'realestatepro'),
            ],
            'hierarchical' => true,
            'public' => true,
            'rewrite' => ['slug' => 'neighborhood'],
            'show_in_rest' => true,
        ]);
        
        // Label (Hot, New, etc.)
        register_taxonomy('property_label', ['property'], [
            'labels' => [
                'name' => __('Labels', 'realestatepro'),
                'singular_name' => __('Label', 'realestatepro'),
            ],
            'hierarchical' => false,
            'public' => true,
            'rewrite' => ['slug' => 'label'],
            'show_in_rest' => true,
        ]);
    }
}
