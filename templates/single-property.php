<?php get_header(); ?>

<div class="realestatepro-container single-property">
    <div class="property-header">
        <h1><?php the_title(); ?></h1>
        <div class="property-price">
            <?php echo RealEstatePro\Shortcodes::format_price(get_post_meta(get_the_ID(), '_property_price', true)); ?>
        </div>
    </div>
    
    <div class="property-gallery">
        <?php 
        $gallery = get_post_meta(get_the_ID(), '_property_gallery', true) ?: [];
        foreach ($gallery as $image_id) {
            echo wp_get_attachment_image($image_id, 'large');
        }
        ?>
    </div>
    
    <div class="property-content">
        <div class="main-content">
            <div class="property-meta">
                <span><i class="icon-bed"></i> <?php echo get_post_meta(get_the_ID(), '_property_bedrooms', true); ?> Beds</span>
                <span><i class="icon-bath"></i> <?php echo get_post_meta(get_the_ID(), '_property_bathrooms', true); ?> Baths</span>
                <span><i class="icon-size"></i> <?php echo get_post_meta(get_the_ID(), '_property_size', true); ?> sqft</span>
            </div>
            
            <div class="property-description">
                <?php the_content(); ?>
            </div>
            
            <div class="property-features">
                <h3><?php _e('Features', 'realestatepro'); ?></h3>
                <?php 
                $features = get_the_terms(get_the_ID(), 'property_feature');
                if ($features) {
                    echo '<ul>';
                    foreach ($features as $feature) {
                        echo '<li>' . esc_html($feature->name) . '</li>';
                    }
                    echo '</ul>';
                }
                ?>
            </div>
            
            <div id="property-map" class="realestatepro-map" 
                 data-lat="<?php echo get_post_meta(get_the_ID(), '_property_latitude', true); ?>"
                 data-lng="<?php echo get_post_meta(get_the_ID(), '_property_longitude', true); ?>">
            </div>
        </div>
        
        <aside class="sidebar">
            <div class="agent-box">
                <?php 
                $agent_id = get_post_meta(get_the_ID(), '_property_agent', true);
                if ($agent_id) {
                    echo get_the_post_thumbnail($agent_id, 'medium');
                    echo '<h4>' . get_the_title($agent_id) . '</h4>';
                    echo '<a href="' . get_permalink($agent_id) . '" class="button">' . __('View Profile', 'realestatepro') . '</a>';
                }
                ?>
            </div>
            
            <button class="add-to-favorites" data-id="<?php the_ID(); ?>">
                <?php _e('Add to Favorites', 'realestatepro'); ?>
            </button>
            
            <button class="add-to-compare" data-id="<?php the_ID(); ?>">
                <?php _e('Add to Compare', 'realestatepro'); ?>
            </button>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
