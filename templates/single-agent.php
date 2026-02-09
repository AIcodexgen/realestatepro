<?php get_header(); ?>

<div class="realestatepro-container single-agent">
    <div class="agent-header">
        <div class="agent-avatar">
            <?php echo get_the_post_thumbnail(null, 'medium'); ?>
        </div>
        <div class="agent-info">
            <h1><?php the_title(); ?></h1>
            <?php
            $position = get_post_meta(get_the_ID(), '_agent_position', true);
            $phone = get_post_meta(get_the_ID(), '_agent_phone', true);
            $email = get_post_meta(get_the_ID(), '_agent_email', true);
            
            if ($position) echo '<div class="position">' . esc_html($position) . '</div>';
            if ($phone) echo '<div class="phone"><a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></div>';
            if ($email) echo '<div class="email"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></div>';
            ?>
        </div>
    </div>
    
    <div class="agent-content">
        <div class="bio">
            <?php the_content(); ?>
        </div>
        
        <div class="agent-properties">
            <h2><?php _e('Listed Properties', 'realestatepro'); ?></h2>
            <?php
            $properties = new \WP_Query([
                'post_type' => 'property',
                'meta_key' => '_property_agent',
                'meta_value' => get_the_ID(),
                'posts_per_page' => 6
            ]);
            
            if ($properties->have_posts()) {
                echo '<div class="properties-grid">';
                while ($properties->have_posts()) {
                    $properties->the_post();
                    \RealEstatePro\Template_Loader::get_template_part('property-card', null, ['property_id' => get_the_ID()]);
                }
                echo '</div>';
                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
