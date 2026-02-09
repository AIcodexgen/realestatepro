<form method="get" class="search-filters-form" action="<?php echo get_post_type_archive_link('property'); ?>">
    <div class="filter-group">
        <label><?php _e('Keyword', 'realestatepro'); ?></label>
        <input type="text" name="keyword" value="<?php echo esc_attr($_GET['keyword'] ?? ''); ?>" placeholder="<?php _e('Search...', 'realestatepro'); ?>">
    </div>
    
    <div class="filter-group">
        <label><?php _e('Type', 'realestatepro'); ?></label>
        <select name="property_type">
            <option value=""><?php _e('All Types', 'realestatepro'); ?></option>
            <?php
            $types = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
            foreach ($types as $type) {
                $selected = selected($_GET['property_type'] ?? '', $type->slug, false);
                echo '<option value="' . $type->slug . '" ' . $selected . '>' . $type->name . '</option>';
            }
            ?>
        </select>
    </div>
    
    <div class="filter-group">
        <label><?php _e('Status', 'realestatepro'); ?></label>
        <select name="property_status">
            <option value=""><?php _e('All Statuses', 'realestatepro'); ?></option>
            <?php
            $statuses = get_terms(['taxonomy' => 'property_status', 'hide_empty' => false]);
            foreach ($statuses as $status) {
                $selected = selected($_GET['property_status'] ?? '', $status->slug, false);
                echo '<option value="' . $status->slug . '" ' . $selected . '>' . $status->name . '</option>';
            }
            ?>
        </select>
    </div>
    
    <div class="filter-group price-range">
        <label><?php _e('Price Range', 'realestatepro'); ?></label>
        <input type="number" name="min_price" placeholder="Min" value="<?php echo esc_attr($_GET['min_price'] ?? ''); ?>">
        <input type="number" name="max_price" placeholder="Max" value="<?php echo esc_attr($_GET['max_price'] ?? ''); ?>">
    </div>
    
    <div class="filter-group">
        <label><?php _e('Bedrooms', 'realestatepro'); ?></label>
        <select name="bedrooms">
            <option value=""><?php _e('Any', 'realestatepro'); ?></option>
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <option value="<?php echo $i; ?>" <?php selected($_GET['bedrooms'] ?? '', $i); ?>><?php echo $i; ?>+</option>
            <?php endfor; ?>
        </select>
    </div>
    
    <button type="submit" class="button"><?php _e('Filter Results', 'realestatepro'); ?></button>
</form>
