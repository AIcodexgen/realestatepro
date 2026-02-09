<?php if (empty($args['properties']) || !$args['properties']->have_posts()) : ?>
    <p><?php _e('You have not submitted any properties yet.', 'realestatepro'); ?></p>
<?php else : ?>
    <div class="my-properties-table-wrapper">
        <table class="my-properties-table">
            <thead>
                <tr>
                    <th><?php _e('Property', 'realestatepro'); ?></th>
                    <th><?php _e('Status', 'realestatepro'); ?></th>
                    <th><?php _e('Date', 'realestatepro'); ?></th>
                    <th><?php _e('Actions', 'realestatepro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($args['properties']->have_posts()) : $args['properties']->the_post(); ?>
                <tr>
                    <td>
                        <strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong>
                        <br>
                        <small><?php echo get_post_meta(get_the_ID(), '_property_address', true); ?></small>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo get_post_status(); ?>">
                            <?php echo get_post_status(); ?>
                        </span>
                    </td>
                    <td><?php echo get_the_date(); ?></td>
                    <td>
                        <a href="<?php echo get_edit_post_link(); ?>" class="button button-small">
                            <?php _e('Edit', 'realestatepro'); ?>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>
