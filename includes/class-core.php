<?php
namespace RealEstatePro;

class Core {
    private static $instance = null;
    
    private function __construct() {
        $this->init();
    }
    
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function init() {
        load_plugin_textdomain('realestatepro', false, dirname(REALESTATEPRO_PLUGIN_BASENAME) . '/languages/');
        
        // Initialize components
        new Post_Types();
        new Taxonomies();
        new Assets();
        new REST_API();
        new Template_Loader();
        new Search();
        new Favorites();
        new Compare();
        new Membership();
        new Payments();
        new Emails();
        new Widgets();
        new Shortcodes();
        
        if (is_admin()) {
            new Admin();
        } else {
            new Public_Frontend();
        }
        
        add_action('init', [$this, 'register_blocks']);
    }
    
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        wp_register_script(
            'realestatepro-blocks',
            REALESTATEPRO_PLUGIN_URL . 'admin/js/blocks.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
            REALESTATEPRO_VERSION
        );
        
        register_block_type('realestatepro/properties-grid', [
            'editor_script' => 'realestatepro-blocks',
            'render_callback' => [$this, 'render_properties_grid'],
            'attributes' => [
                'per_page' => ['type' => 'number', 'default' => 6],
                'status' => ['type' => 'string', 'default' => ''],
                'type' => ['type' => 'string', 'default' => ''],
                'featured' => ['type' => 'boolean', 'default' => false],
            ],
        ]);
    }
    
    public function render_properties_grid($attributes) {
        $shortcode = new Shortcodes();
        return $shortcode->properties_grid($attributes);
    }
}
