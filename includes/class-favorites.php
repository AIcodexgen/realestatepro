<?php
namespace RealEstatePro;

class Favorites {
    public function __construct() {
        add_action('wp_ajax_realestatepro_add_favorite', [$this, 'add_favorite']);
        add_action('wp_ajax_realestatepro_remove_favorite', [$this, 'remove_favorite']);
        add_shortcode('realestatepro_favorites', [$this, 'favorites_shortcode']);
    }
    
    public function add_favorite() {
        check_ajax_referer('realestatepro_favorites_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('Login required');
        }
        
        $property_id = intval($_POST['property_id']);
        $user_id = get_current_user_id();
        $favorites = get_user_meta($user_id, '_favorite_properties', true) ?: [];
        
        if (!in_array($property_id, $favorites)) {
            $favorites[] = $property_id;
            update_user_meta($user_id, '_favorite_properties', $favorites);
        }
        
        wp_send_json_success(['count' => count($favorites)]);
    }
    
    public function remove_favorite() {
        check_ajax_referer('realestatepro_favorites_nonce', 'nonce');
        
        $property_id = intval($_POST['property_id']);
        $user_id = get_current_user_id();
        $favorites = get_user_meta($user_id, '_favorite_properties', true) ?: [];
        
        $favorites = array_diff($favorites, [$property_id]);
        update_user_meta($user_id, '_favorite_properties', $favorites);
        
        wp_send_json_success(['count' => count($favorites)]);
    }
    
    public function favorites_shortcode() {
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to view favorites.', 'realestatepro') . '</p>';
        }
        
        $favorites = get_user_meta(get_current_user_id(), '_favorite_properties', true) ?: [];
        
        if (empty($favorites)) {
            return '<p>' . __('No favorites yet.', 'realestatepro') . '</p>';
        }
        
        ob_start();
        $query = new \WP_Query([
            'post_type' => 'property',
            'post__in' => $favorites,
            'posts_per_page' => -1
        ]);
        
        echo '<div class="favorites-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            Template_Loader::get_template_part('property-card', null, ['property_id' => get_the_ID()]);
        }
        echo '</div>';
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}
