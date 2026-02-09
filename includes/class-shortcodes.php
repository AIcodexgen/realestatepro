<?php
namespace RealEstatePro;

class Shortcodes {
    public function __construct() {
        add_shortcode('realestatepro_search', [$this, 'search_form']);
        add_shortcode('realestatepro_properties', [$this, 'properties_grid']);
        add_shortcode('realestatepro_submit_property', [$this, 'submit_property_form']);
        add_shortcode('realestatepro_my_properties', [$this, 'my_properties']);
        add_shortcode('realestatepro_favorites', [$this, 'favorite_properties']);
        add_shortcode('realestatepro_compare', [$this, 'compare_properties']);
        add_shortcode('realestatepro_packages', [$this, 'pricing_packages']);
        add_shortcode('realestatepro_agents', [$this, 'agents_grid']);
    }
    
    public function search_form($atts) {
        $atts = shortcode_atts([
            'style' => 'default',
        ], $atts);
        
        ob_start();
        Template_Loader::get_template_part('search-form', $atts['style']);
        return ob_get_clean();
    }
    
    public function properties_grid($atts) {
        $atts = shortcode_atts([
            'per_page' => 9,
            'type' => '',
            'status' => '',
            'featured' => 0,
            'columns' => 3,
        ], $atts);
        
        $query_args = [
            'post_type' => 'property',
            'posts_per_page' => $atts['per_page'],
            'post_status' => 'publish',
        ];
        
        if ($atts['featured']) {
            $query_args['meta_key'] = '_property_featured';
            $query_args['meta_value'] = '1';
        }
        
        if ($atts['type']) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => explode(',', $atts['type']),
            ];
        }
        
        $properties = new \WP_Query($query_args);
        
        ob_start();
        Template_Loader::get_template_part('properties-grid', null, [
            'properties' => $properties,
            'columns' => $atts['columns'],
        ]);
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    public function submit_property_form() {
        if (!is_user_logged_in()) {
            return sprintf(
                '<p>%s <a href="%s">%s</a></p>',
                __('Please log in to submit a property.', 'realestatepro'),
                wp_login_url(get_permalink()),
                __('Login', 'realestatepro')
            );
        }
        
        ob_start();
        Template_Loader::get_template_part('submit-property');
        return ob_get_clean();
    }
    
    public function my_properties() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your properties.', 'realestatepro') . '</p>';
        }
        
        $properties = new \WP_Query([
            'post_type' => 'property',
            'author' => get_current_user_id(),
            'posts_per_page' => -1,
        ]);
        
        ob_start();
        Template_Loader::get_template_part('my-properties', null, ['properties' => $properties]);
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    public function favorite_properties() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view your favorites.', 'realestatepro') . '</p>';
        }
        
        $favorites = get_user_meta(get_current_user_id(), '_favorite_properties', true) ?: [];
        
        if (empty($favorites)) {
            return '<p>' . __('You have no favorite properties.', 'realestatepro') . '</p>';
        }
        
        $properties = new \WP_Query([
            'post_type' => 'property',
            'post__in' => $favorites,
            'posts_per_page' => -1,
        ]);
        
        ob_start();
        Template_Loader::get_template_part('favorites', null, ['properties' => $properties]);
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    public function compare_properties() {
        ob_start();
        Template_Loader::get_template_part('compare');
        return ob_get_clean();
    }
    
    public function pricing_packages() {
        $packages = new \WP_Query([
            'post_type' => 'package',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => '_package_price',
            'order' => 'ASC',
        ]);
        
        ob_start();
        Template_Loader::get_template_part('packages', null, ['packages' => $packages]);
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    public function agents_grid($atts) {
        $atts = shortcode_atts([
            'per_page' => 12,
            'agency' => '',
        ], $atts);
        
        $args = [
            'post_type' => 'agent',
            'posts_per_page' => $atts['per_page'],
        ];
        
        if ($atts['agency']) {
            $args['meta_key'] = '_agent_agency';
            $args['meta_value'] = $atts['agency'];
        }
        
        $agents = new \WP_Query($args);
        
        ob_start();
        Template_Loader::get_template_part('agents-grid', null, ['agents' => $agents]);
        wp_reset_postdata();
        return ob_get_clean();
    }
}
