<?php
namespace RealEstatePro;

class Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    
    public function enqueue_public_assets() {
        // CSS
        wp_enqueue_style(
            'realestatepro-public',
            REALESTATEPRO_PLUGIN_URL . 'public/css/public.css',
            [],
            REALESTATEPRO_VERSION
        );
        
        // JS
        wp_enqueue_script(
            'realestatepro-public',
            REALESTATEPRO_PLUGIN_URL . 'public/js/public.js',
            ['jquery'],
            REALESTATEPRO_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('realestatepro-public', 'realestatepro', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('realestatepro/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'strings' => [
                'add_to_favorites' => __('Add to Favorites', 'realestatepro'),
                'remove_from_favorites' => __('Remove from Favorites', 'realestatepro'),
                'compare' => __('Compare', 'realestatepro'),
                'added_to_compare' => __('Added to Compare', 'realestatepro'),
            ],
        ]);
        
        // Google Maps
        $api_key = get_option('realestatepro_google_maps_api_key');
        if ($api_key && !is_admin()) {
            wp_enqueue_script(
                'google-maps',
                "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places",
                [],
                null,
                true
            );
        }
    }
    
    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        
        if ($screen->post_type === 'property' || $screen->post_type === 'agent') {
            wp_enqueue_media();
            
            wp_enqueue_style(
                'realestatepro-admin',
                REALESTATEPRO_PLUGIN_URL . 'admin/css/admin.css',
                [],
                REALESTATEPRO_VERSION
            );
            
            wp_enqueue_script(
                'realestatepro-admin',
                REALESTATEPRO_PLUGIN_URL . 'admin/js/admin.js',
                ['jquery'],
                REALESTATEPRO_VERSION,
                true
            );
        }
    }
}
