<?php
namespace RealEstatePro;

class Template_Loader {
    public function __construct() {
        add_filter('template_include', [$this, 'load_template']);
        add_filter('single_template', [$this, 'single_template']);
        add_filter('archive_template', [$this, 'archive_template']);
    }
    
    public function load_template($template) {
        return $template;
    }
    
    public function single_template($template) {
        if (is_singular('property')) {
            return $this->locate_template('single-property.php');
        }
        if (is_singular('agent')) {
            return $this->locate_template('single-agent.php');
        }
        return $template;
    }
    
    public function archive_template($template) {
        if (is_post_type_archive('property') || is_tax(['property_type', 'property_status', 'property_city'])) {
            return $this->locate_template('archive-property.php');
        }
        return $template;
    }
    
    private function locate_template($template_name) {
        $template_path = get_stylesheet_directory() . '/realestatepro/' . $template_name;
        
        if (!file_exists($template_path)) {
            $template_path = REALESTATEPRO_PLUGIN_DIR . 'templates/' . $template_name;
        }
        
        return file_exists($template_path) ? $template_path : $template;
    }
    
    public static function get_template_part($slug, $name = null, $args = []) {
        $templates = [];
        if ($name) {
            $templates[] = "realestatepro/{$slug}-{$name}.php";
            $templates[] = "{$slug}-{$name}.php";
        }
        $templates[] = "realestatepro/{$slug}.php";
        $templates[] = "{$slug}.php";
        
        $template = locate_template($templates);
        
        if (!$template) {
            $template = REALESTATEPRO_PLUGIN_DIR . "templates/parts/{$slug}" . ($name ? "-{$name}" : "") . ".php";
            if (!file_exists($template)) {
                return;
            }
        }
        
        if (!empty($args) && is_array($args)) {
            extract($args);
        }
        
        include $template;
    }
}
