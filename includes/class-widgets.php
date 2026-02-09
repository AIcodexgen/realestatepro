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

// Widget classes in same file (alternative: separate files)
namespace RealEstatePro\Widgets;

class Search_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_search', __('RealEstatePro: Search', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = !empty($instance['title']) ? $instance['title'] : '';
        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        
        // Simple search form
        ?>
        <form role="search" method="get" action="<?php echo home_url('/'); ?>">
            <input type="hidden" name="post_type" value="property" />
            <input type="search" name="s" placeholder="<?php _e('Search properties...', 'realestatepro'); ?>" />
            <button type="submit"><?php _e('Search', 'realestatepro'); ?></button>
        </form>
        <?php
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
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

class Recent_Properties_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_recent', __('RealEstatePro: Recent Properties', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Properties', 'realestatepro');
        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        
        $recent = new \WP_Query([
            'post_type' => 'property',
            'posts_per_page' => !empty($instance['number']) ? intval($instance['number']) : 5
        ]);
        
        if ($recent->have_posts()) {
            echo '<ul class="recent-properties">';
            while ($recent->have_posts()) {
                $recent->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? intval($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">Number of properties:</label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 5;
        return $instance;
    }
}

class Featured_Properties_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_featured', __('RealEstatePro: Featured', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Properties', 'realestatepro');
        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        
        $featured = new \WP_Query([
            'post_type' => 'property',
            'posts_per_page' => !empty($instance['number']) ? intval($instance['number']) : 3,
            'meta_key' => '_property_featured',
            'meta_value' => '1'
        ]);
        
        if ($featured->have_posts()) {
            echo '<div class="featured-properties-widget">';
            while ($featured->have_posts()) {
                $featured->the_post();
                echo '<div class="property-item">';
                if (has_post_thumbnail()) {
                    echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail(null, 'thumbnail') . '</a>';
                }
                echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
                echo '<span class="price">' . get_post_meta(get_the_ID(), '_property_price', true) . '</span>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? intval($instance['number']) : 3;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">Number:</label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? intval($new_instance['number']) : 3;
        return $instance;
    }
}

class Mortgage_Calculator_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_mortgage', __('RealEstatePro: Mortgage Calc', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = !empty($instance['title']) ? $instance['title'] : __('Mortgage Calculator', 'realestatepro');
        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        ?>
        <div class="mortgage-calculator">
            <p>
                <label><?php _e('Home Price', 'realestatepro'); ?></label>
                <input type="number" class="mc-price widefat" placeholder="300000">
            </p>
            <p>
                <label><?php _e('Down Payment', 'realestatepro'); ?></label>
                <input type="number" class="mc-down widefat" placeholder="60000">
            </p>
            <p>
                <label><?php _e('Interest Rate (%)', 'realestatepro'); ?></label>
                <input type="number" class="mc-rate widefat" step="0.1" value="4.5">
            </p>
            <p>
                <label><?php _e('Years', 'realestatepro'); ?></label>
                <input type="number" class="mc-term widefat" value="30">
            </p>
            <button type="button" class="button calculate-mortgage"><?php _e('Calculate', 'realestatepro'); ?></button>
            <div class="mortgage-result" style="margin-top:10px;font-weight:bold;"></div>
        </div>
        <script>
        (function($){
            $('.calculate-mortgage').on('click', function(){
                var $widget = $(this).closest('.mortgage-calculator');
                var price = parseFloat($widget.find('.mc-price').val()) || 0;
                var down = parseFloat($widget.find('.mc-down').val()) || 0;
                var rate = parseFloat($widget.find('.mc-rate').val()) || 0;
                var term = parseFloat($widget.find('.mc-term').val()) || 30;
                
                var principal = price - down;
                var monthlyRate = rate / 100 / 12;
                var payments = term * 12;
                
                if (principal > 0 && rate > 0) {
                    var monthly = principal * (monthlyRate * Math.pow(1 + monthlyRate, payments)) / (Math.pow(1 + monthlyRate, payments) - 1);
                    $widget.find('.mortgage-result').text('<?php _e('Monthly:', 'realestatepro'); ?> $' + monthly.toFixed(2));
                }
            });
        })(jQuery);
        </script>
        <?php
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
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

class Agent_Widget extends \WP_Widget {
    public function __construct() {
        parent::__construct('realestatepro_agent', __('RealEstatePro: Agent', 'realestatepro'));
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        $title = !empty($instance['title']) ? $instance['title'] : '';
        echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        
        $agent_id = !empty($instance['agent_id']) ? intval($instance['agent_id']) : 0;
        if ($agent_id && get_post_type($agent_id) === 'agent') {
            echo '<div class="agent-widget">';
            if (has_post_thumbnail($agent_id)) {
                echo '<div class="agent-image">' . get_the_post_thumbnail($agent_id, 'medium') . '</div>';
            }
            echo '<h4>' . get_the_title($agent_id) . '</h4>';
            $phone = get_post_meta($agent_id, '_agent_phone', true);
            if ($phone) echo '<p><a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></p>';
            echo '<a href="' . get_permalink($agent_id) . '" class="button">' . __('View Profile', 'realestatepro') . '</a>';
            echo '</div>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $agent_id = !empty($instance['agent_id']) ? intval($instance['agent_id']) : '';
        $agents = get_posts(['post_type' => 'agent', 'posts_per_page' => -1]);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('agent_id')); ?>">Select Agent:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('agent_id')); ?>" name="<?php echo esc_attr($this->get_field_name('agent_id')); ?>">
                <option value="">Select...</option>
                <?php foreach ($agents as $agent): ?>
                <option value="<?php echo $agent->ID; ?>" <?php selected($agent_id, $agent->ID); ?>><?php echo esc_html($agent->post_title); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['agent_id'] = (!empty($new_instance['agent_id'])) ? intval($new_instance['agent_id']) : '';
        return $instance;
    }
}
