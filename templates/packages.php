<?php if (empty($args['packages']) || !$args['packages']->have_posts()) : ?>
    <p><?php _e('No packages available.', 'realestatepro'); ?></p>
<?php else : ?>
    <div class="packages-grid">
        <?php while ($args['packages']->have_posts()) : $args['packages']->the_post(); 
            $price = get_post_meta(get_the_ID(), '_package_price', true);
            $listings = get_post_meta(get_the_ID(), '_package_listings', true);
            $duration = get_post_meta(get_the_ID(), '_package_duration', true);
            $featured = get_post_meta(get_the_ID(), '_package_featured', true);
        ?>
        <div class="package-card">
            <h3><?php the_title(); ?></h3>
            <div class="package-price">
                $<?php echo number_format($price, 2); ?>
            </div>
            <div class="package-features">
                <ul>
                    <li><?php printf(__('%s Listings', 'realestatepro'), $listings); ?></li>
                    <li><?php printf(__('%s Days Duration', 'realestatepro'), $duration); ?></li>
                    <li><?php printf(__('%s Featured Listings', 'realestatepro'), $featured); ?></li>
                </ul>
            </div>
            <div class="package-description">
                <?php the_content(); ?>
            </div>
            <a href="<?php echo add_query_arg('package_id', get_the_ID(), get_permalink(get_option('realestatepro_page_payment'))); ?>" class="button button-primary">
                <?php _e('Select Package', 'realestatepro'); ?>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>
