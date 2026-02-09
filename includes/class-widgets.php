<?php
namespace RealEstatePro;

class Widgets {
    public function __construct() {
        add_action('widgets_init', [$this, 'register_widgets']);
    }
    
    public function register_widgets() {
        register_widget('RealEstatePro\Widgets\Search_Widget');
        register_widget('RealEstatePro\Widgets\Recent_Properties_Widget');
        register_widget('RealEstatePro\Widgets\Featured_Properties_Widget');
        register_widget('RealEstatePro\Widgets\Mortgage_Calculator_Widget');
        register_widget('RealEstatePro\Widgets\Agent_Widget');
    }
}

namespace RealEstatePro\Widgets;

class Search_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_search', __('RealEstatePro: Search', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        \RealEstatePro\Template_Loader::get_template_part('widget-search');
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
}

class Mortgage_Calculator_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_mortgage', __('RealEstatePro: Mortgage Calculator', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        ?>
        <form class="mortgage-calculator">
            <p>
                <label><?php _e('Home Price', 'realestatepro'); ?></label>
                <input type="number" id="mc-price" class="widefat">
            </p>
            <p>
                <label><?php _e('Down Payment', 'realestatepro'); ?></label>
                <input type="number" id="mc-down" class="widefat">
            </p>
            <p>
                <label><?php _e('Interest Rate (%)', 'realestatepro'); ?></label>
                <input type="number" id="mc-rate" step="0.1" class="widefat" value="4.5">
            </p>
            <p>
                <label><?php _e('Loan Term (years)', 'realestatepro'); ?></label>
                <input type="number" id="mc-term" class="widefat" value="30">
            </p>
            <button type="button" id="calculate-mortgage" class="button"><?php _e('Calculate', 'realestatepro'); ?></button>
            <div id="mortgage-result" style="margin-top: 10px; font-weight: bold;"></div>
        </form>
        <script>
        (function($) {
            $('#calculate-mortgage').on('click', function() {
                var price = parseFloat($('#mc-price').val()) || 0;
                var down = parseFloat($('#mc-down').val()) || 0;
                var rate = parseFloat($('#mc-rate').val()) || 0;
                var term = parseFloat($('#mc-term').val()) || 30;
                
                var principal = price - down;
                var monthlyRate = rate / 100 / 12;
                var numberOfPayments = term * 12;
                
                var monthlyPayment = principal * (monthlyRate * Math.pow(1 + monthlyRate, numberOfPayments)) / (Math.pow(1 + monthlyRate, numberOfPayments) - 1);
                
                $('#mortgage-result').text('Monthly Payment: $' + monthlyPayment.toFixed(2));
            });
        })(jQuery);
        </script>
        <?php
        echo $args['after_widget'];
    }
}
