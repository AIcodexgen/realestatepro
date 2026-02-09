<?php 
$property_id = $args['property_id'] ?? get_the_ID();
$price = get_post_meta($property_id, '_property_price', true);
$featured = get_post_meta($property_id, '_property_featured', true);
?>
<article class="property-card <?php echo $featured ? 'featured' : ''; ?>" data-id="<?php echo $property_id; ?>">
    <div class="property-thumbnail">
        <?php if (has_post_thumbnail($property_id)) : ?>
            <a href="<?php echo get_permalink($property_id); ?>">
                <?php echo get_the_post_thumbnail($property_id, 'medium_large'); ?>
            </a>
        <?php endif; ?>
        
        <?php if ($featured) : ?>
            <span class="featured-badge"><?php _e('Featured', 'realestatepro'); ?></span>
        <?php endif; ?>
        
        <div class="property-actions">
            <button class="add-to-favorites" data-id="<?php echo $property_id; ?>">
                <i class="icon-heart"></i>
            </button>
            <button class="add-to-compare" data-id="<?php echo $property_id; ?>">
                <i class="icon-compare"></i>
            </button>
        </div>
    </div>
    
    <div class="property-details">
        <h3 class="property-title">
            <a href="<?php echo get_permalink($property_id); ?>"><?php echo get_the_title($property_id); ?></a>
        </h3>
        
        <div class="property-location">
            <i class="icon-location"></i>
            <?php echo get_post_meta($property_id, '_property_address', true); ?>
        </div>
        
        <div class="property-price">
            <?php echo \RealEstatePro\Core::instance()->format_price($price); ?>
        </div>
        
        <div class="property-meta">
            <span><i class="icon-bed"></i> <?php echo get_post_meta($property_id, '_property_bedrooms', true); ?></span>
            <span><i class="icon-bath"></i> <?php echo get_post_meta($property_id, '_property_bathrooms', true); ?></span>
            <span><i class="icon-size"></i> <?php echo get_post_meta($property_id, '_property_size', true); ?> sqft</span>
        </div>
        
        <div class="property-type">
            <?php 
            $types = get_the_terms($property_id, 'property_type');
            if ($types) echo esc_html($types[0]->name);
            ?>
        </div>
    </div>
</article>
