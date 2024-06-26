<div class="row p-2 pb-1 border border-info rounded-4 mt-3 mt-lg-0 justify-content-end" id="category-dropdown">
    <?php
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'orderby' => 'name',
        'parent' => 0, // Get only parent categories
        'hide_empty' => true,
    ));

    $current_category_id = get_queried_object_id(); // Get the current category ID

    if ($categories) {
        foreach ($categories as $category) {
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $category_id = 'category-' . $category->term_id; // Generate a unique ID for each category
            $category_class = ($category->term_id === $current_category_id) ? 'bg-primary bg-opacity-10' : ''; // Add class for the current category
            ?>

            <a href="<?= esc_url(get_term_link($category, $category->taxonomy)); ?>"
               class="my-1 <?= $category_class; ?> border-info d-flex justify-content-between align-items-center p-3 border border-1 overflow-hidden rounded-4">
                <h6 class="category-title fw-bold mb-0 fs-6"><?= $category->name; ?></h6>
                <p class="mb-0 text-primary small fw-bold pe-2 ms-auto">
                    <?= $category->count; ?>
                    <span class="ps-1">کالا</span>
                </p>
            </a>

            <?php
            // Get child categories
            $children = get_terms(array(
                'taxonomy' => 'product_cat',
                'orderby' => 'name',
                'parent' => $category->term_id, // Get child categories for the current parent
                'hide_empty' => false,
            ));

            if ($children) {
                foreach ($children as $subcat) {
                    $thumbnail_id = get_term_meta($subcat->term_id, 'thumbnail_id', true);
                    $subcat_id = 'subcat-' . $subcat->term_id; // Generate a unique ID for each subcategory
                    $subcat_class = ($subcat->term_id === $current_category_id) ? 'bg-primary bg-opacity-10' : 'bg-info bg-opacity-25'; // Add class for the current subcategory
                    ?>

                    <a href="<?= esc_url(get_term_link($subcat, $subcat->taxonomy)); ?>"
                       class="my-1 col-11 <?= $subcat_class; ?> border-info d-flex justify-content-between align-items-center p-3 border border-1 overflow-hidden rounded-4">
                        <h6 class="category-title fw-bold mb-0 fs-6"><?= $subcat->name; ?></h6>
                        <p class="mb-0 text-primary small fw-bold pe-2 ms-auto">
                            <?= $subcat->count; ?>
                            <span class="ps-1">کالا</span>
                        </p>
                    </a>

                    <?php
                    wp_reset_postdata(); // Reset Query for child categories
                }
            }
        }
    }
    ?>
</div>