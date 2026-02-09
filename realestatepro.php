<?php
/**
 * Plugin Name:       RealEstatePro
 * Plugin URI:        https://example.com/realestatepro
 * Description:       A complete real estate management solution with properties, agents, agencies, and payment integration.
 * Version:           1.0.0
 * Author:            RealEstatePro Team
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       realestatepro
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('REALESTATEPRO_VERSION', '1.0.0');
define('REALESTATEPRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('REALESTATEPRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('REALESTATEPRO_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'RealEstatePro\\';
    $base_dir = REALESTATEPRO_PLUGIN_DIR . 'includes/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . str_replace('\\', '/', str_replace('_', '-', strtolower($relative_class))) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Activation hook
register_activation_hook(__FILE__, ['RealEstatePro\\Activator', 'activate']);

// Deactivation hook
register_deactivation_hook(__FILE__, ['RealEstatePro\\Deactivator', 'deactivate']);

// Initialize
add_action('plugins_loaded', ['RealEstatePro\\Core', 'instance']);
