<?php
namespace RealEstatePro;

class Post_Types {
    public function __construct() {
        add_action('init', [$this, 'register']);
    }
    
    public function register() {
        // Property Post Type
        register_post_type('property', [
            'labels' => [
                'name' => __('Properties', 'realestatepro'),
                'singular_name' => __('Property', 'realestatepro'),
                'add_new' => __('Add New', 'realestatepro'),
                'add_new_item' => __('Add New Property', 'realestatepro'),
                'edit_item' => __('Edit Property', 'realestatepro'),
                'new_item' => __('New Property', 'realestatepro'),
                'view_item' => __('View Property', 'realestatepro'),
                'search_items' => __('Search Properties', 'realestatepro'),
                'not_found' => __('No properties found', 'realestatepro'),
                'not_found_in_trash' => __('No properties found in Trash', 'realestatepro'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'properties'],
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'],
            'menu_icon' => 'dashicons-building',
            'show_in_rest' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
        ]);
        
        // Agent Post Type
        register_post_type('agent', [
            'labels' => [
                'name' => __('Agents', 'realestatepro'),
                'singular_name' => __('Agent', 'realestatepro'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'agents'],
            'supports' => ['title', 'editor', 'thumbnail', 'author'],
            'menu_icon' => 'dashicons-businessperson',
            'show_in_rest' => true,
        ]);
        
        // Agency Post Type
        register_post_type('agency', [
            'labels' => [
                'name' => __('Agencies', 'realestatepro'),
                'singular_name' => __('Agency', 'realestatepro'),
            ],
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'agencies'],
            'supports' => ['title', 'editor', 'thumbnail', 'author'],
            'menu_icon' => 'dashicons-groups',
            'show_in_rest' => true,
        ]);
        
        // Package Post Type (for membership)
        register_post_type('package', [
            'labels' => [
                'name' => __('Packages', 'realestatepro'),
                'singular_name' => __('Package', 'realestatepro'),
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'editor'],
            'menu_icon' => 'dashicons-cart',
            'capability_type' => 'post',
            'capabilities' => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap' => true,
        ]);
        
        // Invoice Post Type
        register_post_type('invoice', [
            'labels' => [
                'name' => __('Invoices', 'realestatepro'),
                'singular_name' => __('Invoice', 'realestatepro'),
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title'],
            'menu_icon' => 'dashicons-text-page',
        ]);
        
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    }
    
    public function add_meta_boxes() {
        add_meta_box('property_details', __('Property Details', 'realestatepro'), [$this, 'property_details_meta_box'], 'property', 'normal', 'high');
        add_meta_box('property_gallery', __('Property Gallery', 'realestatepro'], [$this, 'property_gallery_meta_box'], 'property', 'normal', 'high');
        add_meta_box('agent_details', __('Agent Details', 'realestatepro'], [$this, 'agent_details_meta_box'], 'agent', 'normal', 'high');
        add_meta_box('package_details', __('Package Settings', 'realestatepro'], [$this, 'package_details_meta_box'], 'package', 'normal', 'high');
    }
    
    public function property_details_meta_box($post) {
        wp_nonce_field('realestatepro_save_meta', 'realestatepro_meta_nonce');
        
        $price = get_post_meta($post->ID, '_property_price', true);
        $price_prefix = get_post_meta($post->ID, '_property_price_prefix', true);
        $bedrooms = get_post_meta($post->ID, '_property_bedrooms', true);
        $bathrooms = get_post_meta($post->ID, '_property_bathrooms', true);
        $size = get_post_meta($post->ID, '_property_size', true);
        $address = get_post_meta($post->ID, '_property_address', true);
        $latitude = get_post_meta($post->ID, '_property_latitude', true);
        $longitude = get_post_meta($post->ID, '_property_longitude', true);
        $featured = get_post_meta($post->ID, '_property_featured', true);
        $video_url = get_post_meta($post->ID, '_property_video_url', true);
        $virtual_tour = get_post_meta($post->ID, '_property_virtual_tour', true);
        $agent_id = get_post_meta($post->ID, '_property_agent', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="property_price"><?php _e('Price', 'realestatepro'); ?></label></th>
                <td>
                    <input type="text" id="property_price" name="property_price" value="<?php echo esc_attr($price); ?>" class="regular-text">
                    <input type="text" name="property_price_prefix" value="<?php echo esc_attr($price_prefix); ?>" placeholder="<?php _e('Prefix (e.g., from)', 'realestatepro'); ?>" class="small-text">
                </td>
            </tr>
            <tr>
                <th><label for="property_bedrooms"><?php _e('Bedrooms', 'realestatepro'); ?></label></th>
                <td><input type="number" id="property_bedrooms" name="property_bedrooms" value="<?php echo esc_attr($bedrooms); ?>" min="0" step="1"></td>
            </tr>
            <tr>
                <th><label for="property_bathrooms"><?php _e('Bathrooms', 'realestatepro'); ?></label></th>
                <td><input type="number" id="property_bathrooms" name="property_bathrooms" value="<?php echo esc_attr($bathrooms); ?>" min="0" step="0.5"></td>
            </tr>
            <tr>
                <th><label for="property_size"><?php _e('Size', 'realestatepro'); ?></label></th>
                <td><input type="number" id="property_size" name="property_size" value="<?php echo esc_attr($size); ?>" min="0"> <span class="description"><?php echo get_option('realestatepro_area_unit', 'sqft'); ?></span></td>
            </tr>
            <tr>
                <th><label for="property_address"><?php _e('Address', 'realestatepro'); ?></label></th>
                <td>
                    <input type="text" id="property_address" name="property_address" value="<?php echo esc_attr($address); ?>" class="large-text">
                    <div id="map-canvas" style="height: 300px; margin-top: 10px;"></div>
                    <input type="hidden" id="property_latitude" name="property_latitude" value="<?php echo esc_attr($latitude); ?>">
                    <input type="hidden" id="property_longitude" name="property_longitude" value="<?php echo esc_attr($longitude); ?>">
                </td>
            </tr>
            <tr>
                <th><label for="property_featured"><?php _e('Featured Property', 'realestatepro'); ?></label></th>
                <td><input type="checkbox" id="property_featured" name="property_featured" value="1" <?php checked($featured, '1'); ?>></td>
            </tr>
            <tr>
                <th><label for="property_video_url"><?php _e('Video URL', 'realestatepro'); ?></label></th>
                <td><input type="url" id="property_video_url" name="property_video_url" value="<?php echo esc_url($video_url); ?>" class="large-text" placeholder="https://www.youtube.com/watch?v=..."></td>
            </tr>
            <tr>
                <th><label for="property_virtual_tour"><?php _e('Virtual Tour Embed', 'realestatepro'); ?></label></th>
                <td><textarea id="property_virtual_tour" name="property_virtual_tour" rows="3" class="large-text"><?php echo esc_textarea($virtual_tour); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="property_agent"><?php _e('Assigned Agent', 'realestatepro'); ?></label></th>
                <td>
                    <select id="property_agent" name="property_agent">
                        <option value=""><?php _e('Select Agent', 'realestatepro'); ?></option>
                        <?php
                        $agents = get_posts(['post_type' => 'agent', 'posts_per_page' => -1]);
                        foreach ($agents as $agent) {
                            printf('<option value="%s" %s>%s</option>', 
                                $agent->ID, 
                                selected($agent_id, $agent->ID, false), 
                                esc_html($agent->post_title)
                            );
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
    
    public function property_gallery_meta_box($post) {
        $gallery = get_post_meta($post->ID, '_property_gallery', true);
        if (!is_array($gallery)) $gallery = [];
        ?>
        <div id="property-gallery-container">
            <div id="gallery-preview" class="gallery-preview">
                <?php foreach ($gallery as $image_id): 
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image): ?>
                    <div class="gallery-item" data-id="<?php echo $image_id; ?>">
                        <img src="<?php echo $image[0]; ?>">
                        <button type="button" class="remove-image">×</button>
                        <input type="hidden" name="property_gallery[]" value="<?php echo $image_id; ?>">
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-gallery-images"><?php _e('Add Images', 'realestatepro'); ?></button>
        </div>
        <style>
            .gallery-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
            .gallery-item { position: relative; width: 100px; height: 100px; }
            .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
            .gallery-item .remove-image { position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; }
        </style>
        <?php
    }
    
    public function save_meta_boxes($post_id, $post) {
        if (!isset($_POST['realestatepro_meta_nonce']) || !wp_verify_nonce($_POST['realestatepro_meta_nonce'], 'realestatepro_save_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        if ($post->post_type === 'property') {
            $fields = [
                'property_price' => 'sanitize_text_field',
                'property_price_prefix' => 'sanitize_text_field',
                'property_bedrooms' => 'intval',
                'property_bathrooms' => 'floatval',
                'property_size' => 'floatval',
                'property_address' => 'sanitize_text_field',
                'property_latitude' => 'sanitize_text_field',
                'property_longitude' => 'sanitize_text_field',
                'property_video_url' => 'esc_url_raw',
                'property_virtual_tour' => 'wp_kses_post',
                'property_agent' => 'intval',
            ];
            
            foreach ($fields as $field => $sanitize_callback) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, $sanitize_callback($_POST[$field]));
                }
            }
            
            // Featured checkbox
            update_post_meta($post_id, '_property_featured', isset($_POST['property_featured']) ? '1' : '0');
            
            // Gallery
            if (isset($_POST['property_gallery']) && is_array($_POST['property_gallery'])) {
                $gallery = array_map('intval', $_POST['property_gallery']);
                update_post_meta($post_id, '_property_gallery', $gallery);
            } else {
                update_post_meta($post_id, '_property_gallery', []);
            }
        }
    }
}
