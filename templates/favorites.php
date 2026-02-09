<?php if (empty($args['properties']) || !$args['properties']->have_posts()) : ?>
    <p><?php _e('You have no favorite properties.', 'realestatepro'); ?></p>
<?php else : ?>
    <div class="favorites-grid properties-grid">
        <?php while ($args['properties']->have_posts()) : $args['properties']->the_post(); ?>
            <?php \RealEstatePro\Template_Loader::get_template_part('property-card', null, ['property_id' => get_the_ID()]); ?>
        <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>
