<?php get_header(); ?>

<div class="realestatepro-container archive-property">
    <div class="archive-header">
        <h1><?php post_type_archive_title(); ?></h1>
        
        <div class="view-toggle">
            <button class="grid-view active" data-view="grid">Grid</button>
            <button class="list-view" data-view="list">List</button>
            <button class="map-view" data-view="map">Map</button>
        </div>
    </div>
    
    <div class="archive-content">
        <aside class="search-sidebar">
            <?php RealEstatePro\Template_Loader::get_template_part('search-filters'); ?>
        </aside>
        
        <div class="properties-wrapper">
            <div id="properties-grid" class="properties-grid">
                <?php 
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        RealEstatePro\Template_Loader::get_template_part('property-card', null, ['property_id' => get_the_ID()]);
                    }
                } else {
                    echo '<p>' . __('No properties found.', 'realestatepro') . '</p>';
                }
                ?>
            </div>
            
            <div class="pagination">
                <?php echo paginate_links(); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
