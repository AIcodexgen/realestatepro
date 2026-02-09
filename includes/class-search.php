<?php
namespace RealEstatePro;

class Search {
    public function __construct() {
        add_action('pre_get_posts', [$this, 'modify_search_query']);
        add_action('wp_ajax_realestatepro_search', [$this, 'ajax_search']);
        add_action('wp_ajax_nopriv_realestatepro_search', [$this, 'ajax_search']);
    }
    
    public function modify_search_query($query) {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('property')) {
            $this->apply_search_filters($query);
        }
        return $query;
    }
    
    private function apply_search_filters($query) {
        $meta_query = [];
        $tax_query = [];
        
        if (!empty($_GET['property_type'])) {
            $tax_query[] = [
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['property_type'])
            ];
        }
        
        if (!empty($_GET['property_status'])) {
            $tax_query[] = [
                'taxonomy' => 'property_status',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['property_status'])
            ];
        }
        
        if (!empty($_GET['min_price'])) {
            $meta_query[] = [
                'key' => '_property_price',
                'value' => floatval($_GET['min_price']),
                'type' => 'NUMERIC',
                'compare' => '>='
            ];
        }
        
        if (!empty($_GET['max_price'])) {
            $meta_query[] = [
                'key' => '_property_price',
                'value' => floatval($_GET['max_price']),
                'type' => 'NUMERIC',
                'compare' => '<='
            ];
        }
        
        if (!empty($_GET['bedrooms'])) {
            $meta_query[] = [
                'key' => '_property_bedrooms',
                'value' => intval($_GET['bedrooms']),
                'type' => 'NUMERIC',
                'compare' => '>='
            ];
        }
        
        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
        
        if (!empty($_GET['keyword'])) {
            $query->set('s', sanitize_text_field($_GET['keyword']));
        }
    }
    
    public function ajax_search() {
        check_ajax_referer('realestatepro_search_nonce', 'nonce');
        
        $args = [
            'post_type' => 'property',
            'posts_per_page' => 12,
            'paged' => intval($_POST['page'] ?? 1)
        ];
        
        $query = new \WP_Query($args);
        $results = [];
        
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'permalink' => get_permalink(),
                'price' => get_post_meta(get_the_ID(), '_property_price', true),
                'thumbnail' => get_the_post_thumbnail_url(null, 'medium')
            ];
        }
        
        wp_send_json_success([
            'properties' => $results,
            'total' => $query->found_posts
        ]);
    }
}
