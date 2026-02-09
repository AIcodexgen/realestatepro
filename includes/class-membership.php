<?php
namespace RealEstatePro;

class Membership {
    public function __construct() {
        add_action('init', [$this, 'check_membership_status']);
        add_filter('realestatepro_can_submit_property', [$this, 'check_can_submit'], 10, 2);
    }
    
    public function check_membership_status() {
        if (!is_user_logged_in()) return;
        
        $user_id = get_current_user_id();
        $expires = get_user_meta($user_id, '_package_expires', true);
        
        if ($expires && strtotime($expires) < time()) {
            update_user_meta($user_id, '_current_package', '');
            update_user_meta($user_id, '_package_listings_remaining', 0);
        }
    }
    
    public function check_can_submit($can_submit, $user_id) {
        if (get_option('realestatepro_free_submissions', 0)) {
            return true;
        }
        
        $remaining = get_user_meta($user_id, '_package_listings_remaining', true);
        return intval($remaining) > 0;
    }
    
    public static function deduct_listing($user_id) {
        $remaining = get_user_meta($user_id, '_package_listings_remaining', true);
        if ($remaining > 0) {
            update_user_meta($user_id, '_package_listings_remaining', intval($remaining) - 1);
        }
    }
}
