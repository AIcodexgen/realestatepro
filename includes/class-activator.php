<?php
namespace RealEstatePro;

class Activator {
    public static function activate() {
        // Register post types and taxonomies first
        require_once REALESTATEPRO_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once REALESTATEPRO_PLUGIN_DIR . 'includes/class-taxonomies.php';
        
        $post_types = new Post_Types();
        $post_types->register();
        
        $taxonomies = new Taxonomies();
        $taxonomies->register();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create necessary database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create default pages
        self::create_pages();
    }
    
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Transactions table
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}realestatepro_transactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            package_id bigint(20) NOT NULL,
            payment_method varchar(50) NOT NULL,
            payment_id varchar(100) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) DEFAULT 'USD',
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY package_id (package_id)
        ) $charset_collate;";
        
        // Saved searches table
        $sql .= "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}realestatepro_saved_searches (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            search_name varchar(255) NOT NULL,
            search_params text NOT NULL,
            email_alerts tinyint(1) DEFAULT 1,
            frequency varchar(20) DEFAULT 'weekly',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private static function set_default_options() {
        $defaults = [
            'currency' => 'USD',
            'currency_position' => 'before',
            'area_unit' => 'sqft',
            'map_service' => 'google',
            'google_maps_api_key' => '',
            'default_latitude' => '40.7128',
            'default_longitude' => '-74.0060',
            'properties_per_page' => 12,
            'enable_register_login' => 1,
            'enable_social_login' => 0,
            'enable_reviews' => 1,
            'enable_compare' => 1,
            'enable_favorites' => 1,
            'free_submissions' => 0,
            'submission_moderation' => 1,
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
            'paypal_client_id' => '',
            'paypal_secret' => '',
            'paypal_sandbox' => 1,
        ];
        
        foreach ($defaults as $key => $value) {
            if (false === get_option('realestatepro_' . $key)) {
                add_option('realestatepro_' . $key, $value);
            }
        }
    }
    
    private static function create_pages() {
        $pages = [
            'submit_property' => [
                'title' => __('Submit Property', 'realestatepro'),
                'content' => '[realestatepro_submit_property]',
            ],
            'my_properties' => [
                'title' => __('My Properties', 'realestatepro'),
                'content' => '[realestatepro_my_properties]',
            ],
            'favorite_properties' => [
                'title' => __('Favorite Properties', 'realestatepro'),
                'content' => '[realestatepro_favorites]',
            ],
            'compare_properties' => [
                'title' => __('Compare Properties', 'realestatepro'),
                'content' => '[realestatepro_compare]',
            ],
            'packages' => [
                'title' => __('Pricing Packages', 'realestatepro'),
                'content' => '[realestatepro_packages]',
            ],
            'payment' => [
                'title' => __('Payment', 'realestatepro'),
                'content' => '[realestatepro_payment]',
            ],
        ];
        
        foreach ($pages as $key => $page) {
            $existing = get_page_by_path(sanitize_title($page['title']));
            if (!$existing) {
                $page_id = wp_insert_post([
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                ]);
                update_option('realestatepro_page_' . $key, $page_id);
            }
        }
    }
}
