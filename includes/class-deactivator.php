<?php
namespace RealEstatePro;

class Deactivator {
    public static function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook('realestatepro_daily_cron');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
