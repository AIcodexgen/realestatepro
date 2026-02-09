<?php
namespace RealEstatePro;

class Public_Frontend {
    public function __construct() {
        add_filter('the_content', [$this, 'modify_property_content']);
        add_action('wp_head', [$this, 'add_structured_data']);
    }
    
    public function modify_property_content($content) {
        if (is_singular('property')) {
            // Add custom content before/after
            $before = '<div class="property-single-wrapper">';
            $after = '</div>';
            return $before . $content . $after;
        }
        return $content;
    }
    
    public function add_structured_data() {
        if (!is_singular('property')) return;
        
        $property_id = get_the_ID();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'offers' => [
                '@type' => 'Offer',
                'price' => get_post_meta($property_id, '_property_price', true),
                'priceCurrency' => get_option('realestatepro_currency', 'USD')
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => get_post_meta($property_id, '_property_address', true)
            ]
        ];
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
    }
}
