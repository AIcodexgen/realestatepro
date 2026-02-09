<?php if (!is_user_logged_in()) {
    echo '<p>' . __('Please log in to submit a property.', 'realestatepro') . '</p>';
    return;
} ?>

<div class="property-submission-form">
    <h2><?php _e('Submit Property', 'realestatepro'); ?></h2>
    
    <form id="property-submission-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
        
        <p>
            <label><?php _e('Title', 'realestatepro'); ?></label>
            <input type="text" name="title" required class="regular-text">
        </p>
        
        <p>
            <label><?php _e('Description', 'realestatepro'); ?></label>
            <textarea name="description" rows="5" class="large-text"></textarea>
        </p>
        
        <div class="form-row">
            <p>
                <label><?php _e('Price', 'realestatepro'); ?></label>
                <input type="number" name="price" required step="0.01">
            </p>
            
            <p>
                <label><?php _e('Bedrooms', 'realestatepro'); ?></label>
                <input type="number" name="bedrooms" min="0">
            </p>
            
            <p>
                <label><?php _e('Bathrooms', 'realestatepro'); ?></label>
                <input type="number" name="bathrooms" min="0" step="0.5">
            </p>
        </div>
        
        <p>
            <label><?php _e('Address', 'realestatepro'); ?></label>
            <input type="text" name="address" id="property-address" class="large-text">
        </p>
        
        <p>
            <label><?php _e('Property Type', 'realestatepro'); ?></label>
            <?php
            $types = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
            if ($types) {
                echo '<select name="property_type">';
                foreach ($types as $type) {
                    echo '<option value="' . $type->term_id . '">' . $type->name . '</option>';
                }
                echo '</select>';
            }
            ?>
        </p>
        
        <p>
            <button type="submit" class="button button-primary">
                <?php _e('Submit Property', 'realestatepro'); ?>
            </button>
        </p>
    </form>
</div>
