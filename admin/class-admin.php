<?php
namespace RealEstatePro;

class Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('RealEstatePro', 'realestatepro'),
            __('RealEstatePro', 'realestatepro'),
            'manage_options',
            'realestatepro',
            [$this, 'settings_page'],
            'dashicons-building',
            30
        );
        
        add_submenu_page(
            'realestatepro',
            __('Settings', 'realestatepro'),
            __('Settings', 'realestatepro'),
            'manage_options',
            'realestatepro',
            [$this, 'settings_page']
        );
        
        add_submenu_page(
            'realestatepro',
            __('Transactions', 'realestatepro'),
            __('Transactions', 'realestatepro'),
            'manage_options',
            'realestatepro-transactions',
            [$this, 'transactions_page']
        );
    }
    
    public function register_settings() {
        register_setting('realestatepro_settings', 'realestatepro_currency');
        register_setting('realestatepro_settings', 'realestatepro_google_maps_api_key');
        register_setting('realestatepro_settings', 'realestatepro_stripe_secret_key');
    }
    
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('RealEstatePro Settings', 'realestatepro'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('realestatepro_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th>Currency</th>
                        <td>
                            <select name="realestatepro_currency">
                                <option value="USD" <?php selected(get_option('realestatepro_currency'), 'USD'); ?>>USD</option>
                                <option value="EUR" <?php selected(get_option('realestatepro_currency'), 'EUR'); ?>>EUR</option>
                                <option value="GBP" <?php selected(get_option('realestatepro_currency'), 'GBP'); ?>>GBP</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Google Maps API Key</th>
                        <td>
                            <input type="text" name="realestatepro_google_maps_api_key" 
                                   value="<?php echo esc_attr(get_option('realestatepro_google_maps_api_key')); ?>" 
                                   class="regular-text">
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    public function transactions_page() {
        global $wpdb;
        $transactions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}realestatepro_transactions ORDER BY created_at DESC LIMIT 50");
        ?>
        <div class="wrap">
            <h1><?php _e('Transactions', 'realestatepro'); ?></h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): 
                        $user = get_user_by('id', $tx->user_id);
                        $package = get_post($tx->package_id);
                    ?>
                    <tr>
                        <td><?php echo $tx->id; ?></td>
                        <td><?php echo $user ? $user->display_name : 'Unknown'; ?></td>
                        <td><?php echo $package ? $package->post_title : 'Deleted'; ?></td>
                        <td>$<?php echo number_format($tx->amount, 2); ?></td>
                        <td><?php echo esc_html($tx->payment_method); ?></td>
                        <td><?php echo esc_html($tx->status); ?></td>
                        <td><?php echo $tx->created_at; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
