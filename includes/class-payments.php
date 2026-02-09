<?php
namespace RealEstatePro;

class Payments {
    public function __construct() {
        add_action('init', [$this, 'process_payment']);
        add_action('realestatepro_payment_complete', [$this, 'create_invoice'], 10, 3);
    }
    
    public function process_payment() {
        if (!isset($_POST['realestatepro_payment_submit'])) {
            return;
        }
        
        $package_id = intval($_POST['package_id']);
        $method = sanitize_text_field($_POST['payment_method']);
        $user_id = get_current_user_id();
        
        if (!$package_id || !$user_id) {
            wp_die(__('Invalid request', 'realestatepro'));
        }
        
        switch ($method) {
            case 'stripe':
                $this->process_stripe_payment($package_id, $user_id);
                break;
            case 'paypal':
                $this->process_paypal_payment($package_id, $user_id);
                break;
            case 'wire':
                $this->process_wire_transfer($package_id, $user_id);
                break;
        }
    }
    
    private function process_stripe_payment($package_id, $user_id) {
        $stripe_key = get_option('realestatepro_stripe_secret_key');
        $price = get_post_meta($package_id, '_package_price', true);
        
        if (empty($_POST['stripe_token'])) {
            wp_die(__('Payment error: No token provided', 'realestatepro'));
        }
        
        \Stripe\Stripe::setApiKey($stripe_key);
        
        try {
            $charge = \Stripe\Charge::create([
                'amount' => $price * 100, // cents
                'currency' => strtolower(get_option('realestatepro_currency', 'usd')),
                'source' => sanitize_text_field($_POST['stripe_token']),
                'description' => sprintf(__('Package: %s', 'realestatepro'), get_the_title($package_id)),
            ]);
            
            if ($charge->paid) {
                $this->complete_payment($user_id, $package_id, 'stripe', $charge->id, $price);
                wp_redirect(add_query_arg('payment', 'success', get_permalink()));
                exit;
            }
        } catch (\Exception $e) {
            wp_die($e->getMessage());
        }
    }
    
    private function process_paypal_payment($package_id, $user_id) {
        // PayPal SDK integration
        $client_id = get_option('realestatepro_paypal_client_id');
        $secret = get_option('realestatepro_paypal_secret');
        $sandbox = get_option('realestatepro_paypal_sandbox', true);
        
        $api_url = $sandbox ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';
        
        // Get access token
        $response = wp_remote_post($api_url . '/v1/oauth2/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $secret),
            ],
            'body' => 'grant_type=client_credentials',
        ]);
        
        if (is_wp_error($response)) {
            wp_die($response->get_error_message());
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $access_token = $body['access_token'];
        
        // Create order
        $price = get_post_meta($package_id, '_package_price', true);
        $order_response = wp_remote_post($api_url . '/v2/checkout/orders', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => get_option('realestatepro_currency', 'USD'),
                        'value' => $price,
                    ],
                    'description' => get_the_title($package_id),
                ]],
            ]),
        ]);
        
        // Handle approval/capture flow...
    }
    
    private function complete_payment($user_id, $package_id, $method, $transaction_id, $amount) {
        global $wpdb;
        
        // Insert transaction
        $wpdb->insert(
            $wpdb->prefix . 'realestatepro_transactions',
            [
                'user_id' => $user_id,
                'package_id' => $package_id,
                'payment_method' => $method,
                'payment_id' => $transaction_id,
                'amount' => $amount,
                'status' => 'completed',
            ]
        );
        
        // Activate package for user
        $listings_limit = get_post_meta($package_id, '_package_listings', true);
        $duration = get_post_meta($package_id, '_package_duration', true);
        
        update_user_meta($user_id, '_current_package', $package_id);
        update_user_meta($user_id, '_package_listings_remaining', $listings_limit);
        update_user_meta($user_id, '_package_expires', date('Y-m-d H:i:s', strtotime("+{$duration} days")));
        
        do_action('realestatepro_payment_complete', $user_id, $package_id, $transaction_id);
    }
    
    public function create_invoice($user_id, $package_id, $transaction_id) {
        $invoice_id = wp_insert_post([
            'post_title' => sprintf(__('Invoice #%s', 'realestatepro'), $transaction_id),
            'post_type' => 'invoice',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ]);
        
        update_post_meta($invoice_id, '_invoice_package', $package_id);
        update_post_meta($invoice_id, '_invoice_transaction', $transaction_id);
        update_post_meta($invoice_id, '_invoice_user', $user_id);
    }
}
