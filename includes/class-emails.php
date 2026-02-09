<?php
namespace RealEstatePro;

class Emails {
    public function __construct() {
        add_action('realestatepro_property_submitted', [$this, 'notify_admin_new_property'], 10, 2);
        add_action('realestatepro_property_published', [$this, 'notify_agent_approved'], 10, 2);
        add_action('realestatepro_payment_complete', [$this, 'send_payment_receipt'], 10, 3);
    }
    
    public function notify_admin_new_property($property_id, $user_id) {
        $property = get_post($property_id);
        $user = get_user_by('id', $user_id);
        
        $subject = sprintf(__('New Property Submission: %s', 'realestatepro'), $property->post_title);
        $message = sprintf(
            "A new property has been submitted:\n\nTitle: %s\nAuthor: %s\nEdit Link: %s",
            $property->post_title,
            $user->display_name,
            admin_url('post.php?post=' . $property_id . '&action=edit')
        );
        
        wp_mail(get_option('admin_email'), $subject, $message);
    }
    
    public function notify_agent_approved($property_id, $user_id) {
        $user = get_user_by('id', $user_id);
        $property = get_post($property_id);
        
        $subject = __('Your Property Listing is Live', 'realestatepro');
        $message = sprintf(
            "Hello %s,\n\nYour property '%s' has been approved and is now live.\nView it here: %s",
            $user->display_name,
            $property->post_title,
            get_permalink($property_id)
        );
        
        wp_mail($user->user_email, $subject, $message);
    }
    
    public function send_payment_receipt($user_id, $package_id, $transaction_id) {
        $user = get_user_by('id', $user_id);
        $package = get_post($package_id);
        $amount = get_post_meta($package_id, '_package_price', true);
        
        $subject = __('Payment Receipt - RealEstatePro', 'realestatepro');
        $message = sprintf(
            "Thank you for your purchase!\n\nPackage: %s\nAmount: $%s\nTransaction ID: %s\nDate: %s",
            $package->post_title,
            $amount,
            $transaction_id,
            current_time('mysql')
        );
        
        wp_mail($user->user_email, $subject, $message);
    }
}
